<?php
namespace Apgenic\Classes;

const UPLOAD_DIR      = 'public'.DIRECTORY_SEPARATOR.'uploads';
const MAX_FILE_SIZE   = 10 * 1024 * 1024; // 10 Mo

const ALLOWED_UPLOADS = [
    'image/jpeg' => ['jpg', 'jpeg'],
    'image/png'  => ['png'],
    'image/gif'  => ['gif'],
    'image/webp' => ['webp'],
    'image/avif' => ['avif'],
    'application/pdf' => ['pdf'],
    'application/msword' => ['doc'],
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
    'application/vnd.ms-excel' => ['xls'],
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['xlsx'],
    'application/vnd.ms-outlook' => ['msg'],
    'text/plain' => ['txt'],
    'text/csv'   => ['csv'],
    'application/zip' => ['zip'],
];

const IMAGES_MIMES_TYPES = [
    'image/jpeg' => ['jpg', 'jpeg'],
    'image/png'  => ['png'],
    'image/gif'  => ['gif'],
    'image/webp' => ['webp'],
    'image/avif' => ['avif'],
];



class Upload
{
    /**
     * Valide, renomme et déplace un fichier uploadé.
     *
     * @param array $file Entrée de $_FILES (ex. $_FILES['fichier'])
     * @return array{name: string, path: string, mime: string, size: int}
     * @throws RuntimeException
     */
    static function handle(array $file): array
    {
        $fileInfos = array();
        // 1. Erreurs PHP natives
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Paramètres d\'upload invalides.');
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('Aucun fichier envoyé.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Fichier trop volumineux.');
            default:
                throw new RuntimeException('Échec de l\'upload (code ' . $file['error'] . ').');
        }

        // 2. Fichier réellement uploadé via HTTP POST
        if (!is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('Fichier temporaire invalide.');
        }

        // 3. Taille
        if ($file['size'] <= 0 || $file['size'] > MAX_FILE_SIZE) {
            throw new RuntimeException('Taille de fichier non autorisée.');
        }

        // 4. Type MIME réel (jamais $file['type'], fourni par le client)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']) ?: '';
        $ext   = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!isset(ALLOWED_UPLOADS[$mime]) || !in_array($ext, ALLOWED_UPLOADS[$mime], true)) {
            throw new RuntimeException('Type de fichier non autorisé : ' . $mime);
        }

        // 5. Renommage : nom aléatoire non devinable, extension canonique
        $safeExt  = ALLOWED_UPLOADS[$mime][0];
        $newName  = bin2hex(random_bytes(16)) . '.' . $safeExt;

        // 6. Répertoire de destination
        if (!is_dir(UPLOAD_DIR) && !mkdir(UPLOAD_DIR, 0755, true) && !is_dir(UPLOAD_DIR)) {
            throw new RuntimeException('Impossible de créer le répertoire de destination.');
        }

        $destination = UPLOAD_DIR . DIRECTORY_SEPARATOR . $newName;

        // 7. Déplacement
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new RuntimeException('Impossible de déplacer le fichier uploadé.');
        }

        chmod($destination, 0644);

        // 8. If its an image, get his dimentions
        if (isset(IMAGES_MIMES_TYPES[$mime]) && in_array($ext, IMAGES_MIMES_TYPES[$mime], true)) {
            $data = getimagesize($destination);
            $fileInfos['width'] = $data[0];
            $fileInfos['height'] = $data[1];
        }

        $fileInfos['name'] = $newName;
        $fileInfos['path'] = $destination;
        $fileInfos['mime'] = $mime;
        $fileInfos['size'] = (int) $file['size'];
        return $fileInfos;
    }


}

