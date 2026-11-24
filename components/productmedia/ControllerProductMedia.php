<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

declare(strict_types=1);
namespace Apgenic\ProductMedia;

use Apgenic\Media\MediaImage;
use Apgenic\Media\ModelMedia;


class ControllerProductMedia  extends \Apgenic\Classes\Controller {

    private ViewProductMedia $HTMLProductMedia;
    private ModelProductMedia $dataProductMedia;
    /** The products: id_product => row, the name of a product names the file of its images */
    private array $productList = array();

    // Accepted sort fields
    public static array $fieldsNames = array('id_product','id_media','position');

    function __construct(){
        if(filter_var($_GET['showTabs'] ?? null, FILTER_VALIDATE_INT) === 0){
            $this->showTabs = 0;
        }
    }

    /**
     * Controller default method, validate inputs, create object and dispatch the request to other method depending of the request
     * @return void
     */
    function render(){

        $oModelMedia = new \Apgenic\Media\ModelMedia();
        $oModelMedia->getList();


        $oModelProduct = new \Apgenic\Product\ModelProduct();
        $oModelProduct->getList();
        $this->productList = $oModelProduct->list;

        $this->HTMLProductMedia = new ViewProductMedia($oModelMedia->list, $oModelProduct->list);
        $this->dataProductMedia = new ModelProductMedia();



        // Task to execute
        $task = filter_var($_GET['task'] ?? 'editlist', FILTER_SANITIZE_SPECIAL_CHARS); ;

        $this->setOrderAndFilters();
        $this->HTMLProductMedia->filters = $this->filters;
        $this->HTMLProductMedia->filtersGet = $this->filtersGet;


        $textSearch = filter_var($_GET['text_search'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            $this->HTMLProductMedia->header(T_COMPONENT_PRODUCTMEDIA);
            if($task == 'viewlist' || $task == 'editlist' || $task == 'trashedlist'){
                $this->HTMLProductMedia->search(self::$fieldsNames, $task);
            }
            ////echo '<div id="core_content">';

        }

        try{
            switch ($task) {
                case 'edit':       $this->edit(); break;
                case 'del':        $this->del(); break;
                case 'delHtmx':    $this->delHTMX(); break;
                case 'editlist':   $this->editlist($textSearch); break;
                case 'childlist':  $this->childlist(); break;

                case 'view':       $this->view(); break;
                case 'viewlist':   $this->viewList($textSearch); break;


                default: throw new \ErrorException(T_PAGE_NOT_FOUND, 404, E_ERROR);
            }
        }
        catch(Exception $e){
            $this->message['type'] = 'danger';
            $this->message['text'] = $e->getMessage();
        }

        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            //echo '</div>';
            $this->HTMLProductMedia->footer();
        }

    }

    /**
     * Edit or add a row in product_media
     * @return void
     */
    function edit(string $task='edit'){
        // For when the edit is under a tab
        $this->dataProductMedia->id_media = (int)($_GET['id_media'] ?? 0);

        // For when the edit is under a tab
        $this->dataProductMedia->id_product = (int)($_GET['id_product'] ?? 0);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->addImage();
        }

        else{
            $this->dataProductMedia->id_product = filter_var($_GET['id_product'] ?? null, FILTER_VALIDATE_INT);
            $this->dataProductMedia->id_media = filter_var($_GET['id_media'] ?? null, FILTER_VALIDATE_INT);

        }


        if( 1 && $this->dataProductMedia->id_product > 0 && $this->dataProductMedia->id_media > 0 ){
            $this->dataProductMedia->get();
        }
        else{
            // Do not show tabs when wee create the entry
            $this->showTabs = 0;
        }


        if($task == 'edit'){
            $this->HTMLProductMedia->edit($this->dataProductMedia,  $this->showTabs, $this->message, 'edit');
        }
        else{
            $this->dataProductMedia = new ModelProductMedia();
            $this->HTMLProductMedia->edit($this->dataProductMedia,  $this->showTabs, $this->message, 'childlist');
        }
    }



    /**
     * Add an image to a product (POST).
     * Tab of a product: the file sent (field file) is stored as a new media (component media), then added to the
     * images of the product. Tab of a media: the media (id_media) is added to the images of the chosen product.
     * position: place of the image, empty = after the other images of the product.
     * @return void
     */
    private function addImage():void{
        $idProduct = (int)filter_var($_POST['id_product'] ?? null, FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
        $idMedia = (int)filter_var($_POST['id_media'] ?? null, FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
        $position = (int)filter_var($_POST['position'] ?? null, FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
        $hasFile = MediaImage::isUploaded($_FILES['file'] ?? null);

        $this->dataProductMedia->id_product = $idProduct;
        $this->dataProductMedia->id_media = $idMedia;

        if($idProduct < 1){
            $this->message = array('type' => 'danger', 'text' => T_MSG_FILL_MANDATORY);
            return;
        }
        if(!$hasFile && $idMedia < 1){
            $this->message = array('type' => 'danger', 'text' => T_ERROR_CHOOSE_IMAGE);
            return;
        }
        if(!$hasFile){
            $existing = new ModelProductMedia();
            $existing->id_product = $idProduct;
            $existing->id_media = $idMedia;
            if($existing->get()){
                $this->message = array('type' => 'danger', 'text' => T_ERROR_ALREADY_LINKED);
                return;
            }
        }

        $stored = null;
        $newMedia = null;
        try{
            if($hasFile){
                // The file is named after the product, else after the name of the uploaded file
                $name = (string)($this->productList[$idProduct]['name'] ?? pathinfo((string)($_FILES['file']['name'] ?? ''), PATHINFO_FILENAME));
                $stored = MediaImage::store($_FILES['file'], $name);

                $newMedia = new ModelMedia();
                $newMedia->filename = $stored['filename'];
                $newMedia->mime_type = $stored['mime_type'];
                $newMedia->size_bytes = $stored['size_bytes'];
                $newMedia->width = $stored['width'];
                $newMedia->height = $stored['height'];
                // The name of the product describes the image (stored html-escaped like the forms do)
                $newMedia->alt_text = isset($this->productList[$idProduct]) ? (string)$this->productList[$idProduct]['name'] : null;
                if(!$newMedia->save()){
                    throw new \RuntimeException(T_ERROR_NOT_SAVED);
                }
                $idMedia = (int)$newMedia->id_media;
            }

            // After the other images of the product
            if($position < 1){
                $position = (new ModelProductMedia())->getList(0, 0, array('id_product' => $idProduct)) + 1;
            }

            $this->dataProductMedia->id_product = $idProduct;
            $this->dataProductMedia->id_media = $idMedia;
            $this->dataProductMedia->position = $position;
            if(!$this->dataProductMedia->add()){
                throw new \RuntimeException(T_ERROR_NOT_SAVED);
            }

            $this->message = array('type' => 'success', 'text' => T_MSG_ADDED);

            // The list under the form displays the new media
            if($newMedia !== null){
                $oModelMedia = new ModelMedia();
                $oModelMedia->getList();
                $this->HTMLProductMedia->mediaList1 = $oModelMedia->list;
            }
        }
        catch(\Throwable $e){
            // Nothing is left behind: the new media and its files
            if($newMedia !== null && (int)$newMedia->id_media > 0){
                try{ $newMedia->del(); } catch(\Throwable $ignored){}
            }
            if($stored !== null){
                MediaImage::delete($stored['filename']);
            }
            if(!($e instanceof \RuntimeException) || $e instanceof \PDOException){
                error_log('Product image: '.$e->getMessage());
            }
            $this->message = array('type' => 'danger', 'text' => $e instanceof \RuntimeException && !($e instanceof \PDOException) ? $e->getMessage() : T_ERROR_NOT_SAVED);
            $this->dataProductMedia->id_media = $hasFile ? 0 : $idMedia;
        }
    }



    /**
     * Delete a row in product_media and display the list of elements
     * @return void
     */
    function del(){
        $this->dataProductMedia->id_product = filter_var($_POST['id_product'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataProductMedia->id_product){
            $this->dataProductMedia->del();
            $this->message['type'] = 'success';
            $this->message['text'] = T_MSG_DONE;
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = T_MSG_MISSING_PARAMETER;
        }

        $this->editlist();
    }



    /**
     * Delete a row in product_media and return a JSON response
     * @return void
     */
    function delHtmx(int $logical=0){
        $this->dataProductMedia->id_product = filter_var($_GET['id_product'] ?? null, FILTER_VALIDATE_INT);
        $this->dataProductMedia->id_media = filter_var($_GET['id_media'] ?? null, FILTER_VALIDATE_INT);

        if( 1  && $this->dataProductMedia->id_product  && $this->dataProductMedia->id_media ){

            $this->dataProductMedia->del();

        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = T_MSG_MISSING_PARAMETER;
        }

        if(count($this->message)){
            //http_response_code(204);
            $this->HTMLProductMedia->message($this->message);
        }
    }

    /**
     * Display a list of row from product_media with add and del buttons
     * @return void
     */
    function editlist($textSearch = ''){
        // Display items list for edition

        $nbItems = $this->dataProductMedia->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLProductMedia->editList($this->dataProductMedia->list ,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLProductMedia->pagination($nbPages, $this->page, "?component=productmedia&task=editlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * Display tabs above the edit form if the element have data from others tables
     * @return void
     */
    function childlist(){
        $this->edit('childlist');
        $this->dataProductMedia->getList(0 , 0, $this->filters, $this->orderBy, strtoupper($this->order));
        $this->HTMLProductMedia->childList($this->dataProductMedia->list ,  $this->filters, $this->orderBy, $this->revertOrder);
    }


    /**
     * Display element details
     * @return void
     */
    function view(){
        // Display selected trainings
        $this->dataProductMedia->id_product = filter_var($_GET['id_product'] ?? null, FILTER_VALIDATE_INT);
        $this->dataProductMedia->id_media = filter_var($_GET['id_media'] ?? null, FILTER_VALIDATE_INT);
        $this->dataProductMedia->get();
        $this->HTMLProductMedia->view($this->dataProductMedia,  $this->message);
    }


    /**
     * Display a list of row from product_media with only the view button
     * For consultation without editing
     * @return void
     */
    function viewList($textSearch = ''){
        // Display items list



        $nbItems = $this->dataProductMedia->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLProductMedia->viewList($this->dataProductMedia->list,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLProductMedia->pagination($nbPages, $this->page, "?component=productmedia&task=viewlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * Set the order and filter private variable depending of the browser request
     * @return void
     */
    protected function setOrderAndFilters($defaultOrderBy = '', $defaultOrder = 'asc'):void{
        // FK Filter(s)
        if(isset($_GET['filters']['id_media'])){
            $this->filters['id_media'] = intval($_GET['filters']['id_media']);
        }

        if(isset($_GET['filters']['id_product'])){
            $this->filters['id_product'] = intval($_GET['filters']['id_product']);
        }

        if(isset($_GET['field_search'])){
            $this->filters[$_GET['field_search']] = $_GET['field_search_value'];
        }

        parent::setOrderAndFilters('id_product', 'desc');
    }



}
