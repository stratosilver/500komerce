<?php
/**
 * API of the component media: same operations as ControllerMedia, same model, JSON instead of views.
 * See api.php for the way to call it.
 *
 * The image is sent as multipart/form-data in the field "file" (required by add, optional in edit with POST).
 * Optional fields: "id_product" names the file after the product and, in add, adds the image to the
 * images of the product; "name" gives another name to the file.
 * filename, mime_type, size_bytes, width and height are read from the file, they cannot be sent.
 * Each media is returned with "urls": the address of the original file and of each size of MediaImage::SIZES.
 */

declare(strict_types=1);
namespace Apgenic\Media;

use Apgenic\Classes\ApiException;

class ControllerApiMedia extends \Apgenic\Classes\ControllerApi {

    const MODEL = ModelMedia::class;
    const PK = array('id_media');
    const PK_AUTO = true;
    const SOFT_DELETE = true;
    const AUTO_DATES = array();

    // Fields saved by ModelMedia
    const FIELDS = array(
        'filename'             => array('type' => 'string', 'null' => false, 'required' => true, 'max' => 255),
        'mime_type'            => array('type' => 'string', 'null' => false, 'required' => true, 'max' => 100),
        'size_bytes'           => array('type' => 'int', 'null' => true, 'required' => false),
        'width'                => array('type' => 'int', 'null' => true, 'required' => false),
        'height'               => array('type' => 'int', 'null' => true, 'required' => false),
        'alt_text'             => array('type' => 'string', 'null' => true, 'required' => false, 'max' => 500),
        'caption'              => array('type' => 'string', 'null' => true, 'required' => false),
    );

    // All the fields of the table `media`
    public static array $fieldsNames = array('id_media', 'filename', 'mime_type', 'size_bytes', 'width', 'height', 'alt_text', 'caption', 'created_at', 'updated_at', 'deleted_at');


    // Fields filled from the uploaded file
    const FILE_FIELDS = array('filename', 'mime_type', 'size_bytes', 'width', 'height');


    /**
     * Create a media from an uploaded image
     * @return void
     */
    protected function add():void{
        if(!MediaImage::isUploaded($_FILES['file'] ?? null)){
            throw new ApiException(422, 'Validation failed', array('fields' => array('file' => 'Required: the image, sent as multipart/form-data')));
        }
        $idProduct = (int)filter_var($this->input['id_product'] ?? null, FILTER_VALIDATE_INT);
        $productName = $this->productName($idProduct);

        $filename = $this->storeUpload($productName);
        try{
            parent::add();
        }
        catch(\Throwable $e){
            // The files are useless if the row is not created
            MediaImage::delete($filename);
            throw $e;
        }

        // A new image named after a product is added to the images of this product
        if($productName !== null){
            try{
                $oProductMedia = new \Apgenic\ProductMedia\ModelProductMedia();
                $position = $oProductMedia->getList(0, 0, array('id_product' => $idProduct)) + 1;
                $oProductMedia->id_product = $idProduct;
                $oProductMedia->id_media = $this->data->id_media;
                $oProductMedia->position = $position;
                $oProductMedia->add();
            }
            catch(\Throwable $e){
                error_log('API media, link to the product: '.$e->getMessage());
            }
        }
    }


    /**
     * Update a media, the image is replaced when a file is sent
     * @return void
     */
    protected function edit():void{
        if(!MediaImage::isUploaded($_FILES['file'] ?? null)){
            foreach(self::FILE_FIELDS as $field){
                unset($this->input[$field]);
            }
            parent::edit();
            return;
        }

        $this->load();
        $oldFilename = (string)$this->data->filename;

        $filename = $this->storeUpload($this->productName((int)filter_var($this->input['id_product'] ?? null, FILTER_VALIDATE_INT)));
        try{
            parent::edit();
        }
        catch(\Throwable $e){
            MediaImage::delete($filename);
            throw $e;
        }
        if($oldFilename != $filename){
            MediaImage::delete($oldFilename);
        }
    }


    /**
     * Delete a media and the files of its image
     * @return void
     */
    protected function del():void{
        $this->load();
        $filename = (string)$this->data->filename;

        parent::del();
        MediaImage::delete($filename);
    }


    /**
     * Add the addresses of the image to the row
     * @return array
     */
    protected function export(array $row):array{
        $out = parent::export($row);

        $urls = null;
        $filename = (string)($row['filename'] ?? '');
        if(MediaImage::thumbnailUrl($filename) != ''){
            $urls = array('original' => MediaImage::url($filename));
            foreach(MediaImage::SIZES as $sizeName => $box){
                $urls[$sizeName] = MediaImage::url($filename, $sizeName);
            }
        }
        $out['urls'] = $urls;
        return $out;
    }


    /**
     * @return string|null name of the product, null if there is none
     */
    private function productName(int $idProduct):?string{
        if($idProduct < 1){
            return null;
        }
        $oProduct = new \Apgenic\Product\ModelProduct();
        $oProduct->id_product = $idProduct;
        if(!$oProduct->get()){
            throw new ApiException(422, 'Validation failed', array('fields' => array('id_product' => 'Unknown product')));
        }
        return (string)$oProduct->name;
    }


    /**
     * Store the uploaded image and put the values read from the file in the input of the request
     * @param string|null $productName the file is named after the product when there is one
     * @return string name of the stored file
     */
    private function storeUpload(?string $productName):string{
        // Name of the file: the product, else the field "name", else the alt text, else the name of the uploaded file
        $name = $productName;
        foreach(array('name', 'alt_text') as $field){
            if($name === null && is_string($this->input[$field] ?? null) && trim($this->input[$field]) != ''){
                $name = $this->input[$field];
            }
        }
        if($name === null){
            $name = pathinfo((string)($_FILES['file']['name'] ?? ''), PATHINFO_FILENAME);
        }

        try{
            $stored = MediaImage::store($_FILES['file'], $name);
        }
        catch(\RuntimeException $e){
            throw new ApiException(422, 'Validation failed', array('fields' => array('file' => $e->getMessage())));
        }

        foreach(self::FILE_FIELDS as $field){
            $this->input[$field] = $stored[$field];
        }
        return $stored['filename'];
    }

}
