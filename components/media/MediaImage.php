<?php
/**
 * Files of the images of the component media.
 *
 * An uploaded image is stored in public/images/ under a readable name (the name of the product when
 * there is one), with one resized copy for each size of SIZES:
 *   t-shirt-bleu.jpg          the original file
 *   t-shirt-bleu-thumb.jpg    the thumbnail displayed in the lists and the forms
 *   t-shirt-bleu-small.jpg, t-shirt-bleu-medium.jpg, t-shirt-bleu-large.jpg
 * The table media only keeps the name of the original file (media.filename).
 */

declare(strict_types=1);
namespace Apgenic\Media;

class MediaImage {

    /** Folder of the images, inside public/ */
    const DIR = 'images';

    /**
     * Sizes created for each image: name => array(maximum width, maximum height) in pixels.
     * The image is reduced to fit in the box, the proportions are kept and a small image is never enlarged.
     * Add, remove or change sizes here; only the images uploaded afterwards are concerned.
     */
    const SIZES = array(
        'thumb'  => array(150, 150),
        'small'  => array(320, 320),
        'medium' => array(800, 800),
        'large'  => array(1600, 1600),
    );

    /** Size displayed next to the name of an image in the lists and the forms */
    const THUMBNAIL = 'thumb';

    /** Accepted images: real mime type of the file => extension of the stored file */
    const TYPES = array(
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    );

    /** Maximum size of the uploaded file, in bytes (10 MB) */
    const MAX_FILE_SIZE = 10485760;
    /** Maximum number of pixels of the uploaded image (resizing needs about 5 bytes of memory per pixel) */
    const MAX_PIXELS = 40000000;
    const JPEG_QUALITY = 85;
    const WEBP_QUALITY = 85;


    /**
     * @return string absolute path of the folder of the images
     */
    public static function dir():string{
        return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.self::DIR;
    }


    /**
     * Was a file sent in this entry of $_FILES
     * @return bool
     */
    public static function isUploaded($file):bool{
        return is_array($file) && isset($file['error']) && !is_array($file['error']) && $file['error'] != UPLOAD_ERR_NO_FILE;
    }


    /**
     * Check an uploaded image, store it under the given name and create its resized copies.
     *
     * @param array $file entry of $_FILES, ex: $_FILES['file']
     * @param string $name name to give to the file, ex: the name of the product. It is simplified
     *                     ("T-shirt Bleu" => t-shirt-bleu) and numbered if it is already used (t-shirt-bleu-2)
     * @return array filename, mime_type, size_bytes, width, height: the values of the table media
     * @throws \RuntimeException with a message that can be displayed to the user
     */
    public static function store(array $file, string $name):array{
        if(!extension_loaded('gd')){
            throw new \RuntimeException('The GD extension of PHP is needed to resize the images');
        }

        // Upload errors
        if(!isset($file['error']) || is_array($file['error'])){
            throw new \RuntimeException('Invalid upload');
        }
        switch($file['error']){
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new \RuntimeException('No file sent');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new \RuntimeException('The file is too big (upload_max_filesize of php.ini: '.ini_get('upload_max_filesize').')');
            default:
                throw new \RuntimeException('The upload failed (code '.$file['error'].')');
        }
        if(!is_uploaded_file($file['tmp_name'])){
            throw new \RuntimeException('Invalid upload');
        }
        if($file['size'] <= 0 || $file['size'] > self::MAX_FILE_SIZE){
            throw new \RuntimeException('The file is too big, '.round(self::MAX_FILE_SIZE / 1048576).' MB maximum');
        }

        // Real type of the file, never the one announced by the browser
        $mime = (string)(new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $size = @getimagesize($file['tmp_name']);
        if(!isset(self::TYPES[$mime]) || $size === false || $size[0] < 1 || $size[1] < 1){
            throw new \RuntimeException('This file is not an accepted image (JPEG, PNG, GIF or WebP)');
        }
        if($size[0] * $size[1] > self::MAX_PIXELS){
            throw new \RuntimeException('The image is too large, '.round(self::MAX_PIXELS / 1000000).' megapixels maximum');
        }
        $extension = self::TYPES[$mime];

        $source = self::open($file['tmp_name'], $mime);
        if(!$source){
            throw new \RuntimeException('The image could not be read');
        }

        $dir = self::dir();
        if(!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)){
            throw new \RuntimeException('The folder public/'.self::DIR.' could not be created');
        }

        $base = self::freeName(self::slug($name));
        $filename = $base.'.'.$extension;
        $created = array();

        try{
            if(!move_uploaded_file($file['tmp_name'], $dir.DIRECTORY_SEPARATOR.$filename)){
                throw new \RuntimeException('The file could not be stored in public/'.self::DIR);
            }
            $created[] = $filename;
            @chmod($dir.DIRECTORY_SEPARATOR.$filename, 0644);

            foreach(self::SIZES as $sizeName => $box){
                $variant = self::variant($filename, $sizeName);
                if(!self::resize($source, $mime, $box[0], $box[1], $dir.DIRECTORY_SEPARATOR.$variant)){
                    throw new \RuntimeException('The size "'.$sizeName.'" of the image could not be created');
                }
                $created[] = $variant;
            }
        }
        catch(\Throwable $e){
            // Nothing is left behind
            foreach($created as $createdName){
                @unlink($dir.DIRECTORY_SEPARATOR.$createdName);
            }
            throw $e;
        }

        return array(
            'filename'   => $filename,
            'mime_type'  => $mime,
            'size_bytes' => (int)$file['size'],
            'width'      => (int)$size[0],
            'height'     => (int)$size[1],
        );
    }


    /**
     * Delete the file of an image and all its resized copies
     * @param string $filename value of media.filename
     * @return void
     */
    public static function delete(string $filename):void{
        if(!self::isStoredName($filename)){
            return;
        }
        $names = array($filename);
        foreach(self::SIZES as $sizeName => $box){
            $names[] = self::variant($filename, $sizeName);
        }
        foreach($names as $name){
            $path = self::dir().DIRECTORY_SEPARATOR.$name;
            if(is_file($path)){
                @unlink($path);
            }
        }
    }


    /**
     * Name of the file of a size, ex: variant('t-shirt.jpg', 'thumb') => t-shirt-thumb.jpg
     * @return string
     */
    public static function variant(string $filename, string $sizeName):string{
        $dot = strrpos($filename, '.');
        if($dot === false){
            return $filename.'-'.$sizeName;
        }
        return substr($filename, 0, $dot).'-'.$sizeName.substr($filename, $dot);
    }


    /**
     * Url of an image
     * @param string $filename value of media.filename
     * @param string $sizeName one of the keys of SIZES, '' for the original file
     * @return string
     */
    public static function url(string $filename, string $sizeName = ''):string{
        $name = $sizeName == '' ? $filename : self::variant($filename, $sizeName);
        return BASE_URL.'/'.self::DIR.'/'.rawurlencode($name);
    }


    /**
     * Url of the thumbnail of an image
     * @param mixed $filename value of media.filename
     * @return string '' when the image has no thumbnail file (ex: a media created before the uploads)
     */
    public static function thumbnailUrl($filename):string{
        $filename = (string)$filename;
        if(!self::isStoredName($filename) || !is_file(self::dir().DIRECTORY_SEPARATOR.self::variant($filename, self::THUMBNAIL))){
            return '';
        }
        return self::url($filename, self::THUMBNAIL);
    }


    /**
     * Html of the thumbnail of an image, displayed wherever the name of the image appears
     * @param mixed $filename value of media.filename
     * @return string an img tag, '' when the image has no thumbnail file
     */
    public static function thumbnail($filename, string $alt = ''):string{
        $url = self::thumbnailUrl($filename);
        if($url == ''){
            return '';
        }
        return '<img class="media-thumb" src="'.htmlspecialchars($url).'" alt="'.htmlspecialchars($alt).'" loading="lazy">';
    }


    /**
     * Simplify a name to use it as a file name: "T-shirt Bleu été" => t-shirt-bleu-ete
     * @return string only a-z, 0-9 and -
     */
    public static function slug(string $name):string{
        // The texts are stored html-escaped by the forms
        $name = html_entity_decode($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $name = strtr($name, array(
            'à'=>'a','á'=>'a','â'=>'a','ã'=>'a','ä'=>'a','å'=>'a','æ'=>'ae','ç'=>'c','è'=>'e','é'=>'e','ê'=>'e','ë'=>'e',
            'ì'=>'i','í'=>'i','î'=>'i','ï'=>'i','ñ'=>'n','ò'=>'o','ó'=>'o','ô'=>'o','õ'=>'o','ö'=>'o','ø'=>'o','œ'=>'oe',
            'ù'=>'u','ú'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y','ß'=>'ss',
            'À'=>'a','Á'=>'a','Â'=>'a','Ã'=>'a','Ä'=>'a','Å'=>'a','Æ'=>'ae','Ç'=>'c','È'=>'e','É'=>'e','Ê'=>'e','Ë'=>'e',
            'Ì'=>'i','Í'=>'i','Î'=>'i','Ï'=>'i','Ñ'=>'n','Ò'=>'o','Ó'=>'o','Ô'=>'o','Õ'=>'o','Ö'=>'o','Ø'=>'o','Œ'=>'oe',
            'Ù'=>'u','Ú'=>'u','Û'=>'u','Ü'=>'u','Ý'=>'y',
        ));
        $name = strtolower($name);
        $name = trim((string)preg_replace('/[^a-z0-9]+/', '-', $name), '-');
        $name = trim(substr($name, 0, 80), '-');
        return $name == '' ? 'image' : $name;
    }


    /**
     * First name not used by another image: name, name-2, name-3...
     * @return string
     */
    private static function freeName(string $slug):string{
        $dir = self::dir().DIRECTORY_SEPARATOR;
        for($i = 1; $i < 10000; $i++){
            $base = $i == 1 ? $slug : $slug.'-'.$i;
            // Whatever the extension: t-shirt.jpg and t-shirt.png would be confusing
            if(!glob($dir.$base.'.*')){
                return $base;
            }
        }
        return $slug.'-'.bin2hex(random_bytes(4));
    }


    /**
     * Is it the name of a file created by store(): no folder, only the characters of slug()
     * @return bool
     */
    private static function isStoredName(string $filename):bool{
        return (bool)preg_match('/^[a-z0-9-]+\.(jpg|png|gif|webp)$/', $filename);
    }


    /**
     * Open an image with GD, turned the right way when a photo has an orientation
     * @return \GdImage|false
     */
    private static function open(string $path, string $mime){
        switch($mime){
            case 'image/jpeg': $image = @imagecreatefromjpeg($path); break;
            case 'image/png':  $image = @imagecreatefrompng($path); break;
            case 'image/gif':  $image = @imagecreatefromgif($path); break;
            case 'image/webp': $image = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false; break;
            default: $image = false;
        }
        if(!$image){
            return false;
        }

        // The phones store the photos unrotated, with the orientation in the EXIF data
        if($mime == 'image/jpeg' && function_exists('exif_read_data')){
            $exif = @exif_read_data($path);
            $angles = array(3 => 180, 6 => -90, 8 => 90);
            if(is_array($exif) && isset($exif['Orientation'], $angles[$exif['Orientation']])){
                $rotated = imagerotate($image, $angles[$exif['Orientation']], 0);
                if($rotated){
                    $image = $rotated;
                }
            }
        }
        return $image;
    }


    /**
     * Write a copy of the image reduced to fit in a box
     * @param \GdImage $source
     * @return bool
     */
    private static function resize($source, string $mime, int $maxWidth, int $maxHeight, string $path):bool{
        $width = imagesx($source);
        $height = imagesy($source);

        // Never enlarge
        $ratio = min(1, $maxWidth / $width, $maxHeight / $height);
        $newWidth = max(1, (int)round($width * $ratio));
        $newHeight = max(1, (int)round($height * $ratio));

        $copy = imagecreatetruecolor($newWidth, $newHeight);
        if(!$copy){
            return false;
        }
        if($mime != 'image/jpeg'){
            // Keep the transparency
            imagealphablending($copy, false);
            imagesavealpha($copy, true);
            imagefilledrectangle($copy, 0, 0, $newWidth, $newHeight, imagecolorallocatealpha($copy, 0, 0, 0, 127));
        }
        imagecopyresampled($copy, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        switch($mime){
            case 'image/jpeg': return imagejpeg($copy, $path, self::JPEG_QUALITY);
            case 'image/png':  return imagepng($copy, $path, 6);
            case 'image/gif':  return imagegif($copy, $path);
            case 'image/webp': return function_exists('imagewebp') && imagewebp($copy, $path, self::WEBP_QUALITY);
        }
        return false;
    }
}
