<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

declare(strict_types=1);
namespace Apgenic\Media;


class ControllerMedia  extends \Apgenic\Classes\Controller {

    private ViewMedia $HTMLMedia;
    private ModelMedia $dataMedia;
    private array $productList = array();

    // Accepted sort fields
    public static array $fieldsNames = array('id_media','filename','mime_type','size_bytes','width','height','alt_text','caption','created_at','updated_at','deleted_at');

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
        // Products proposed in the form: an image can be named after a product
        $oModelProduct = new \Apgenic\Product\ModelProduct();
        $oModelProduct->getList();
        $this->productList = $oModelProduct->list;

        $this->HTMLMedia = new ViewMedia($this->productList);
        $this->dataMedia = new ModelMedia();



        // Task to execute
        $task = filter_var($_GET['task'] ?? 'editlist', FILTER_SANITIZE_SPECIAL_CHARS); ;

        $this->setOrderAndFilters();
        $this->HTMLMedia->filters = $this->filters;
        $this->HTMLMedia->filtersGet = $this->filtersGet;


        $textSearch = filter_var($_GET['text_search'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            $this->HTMLMedia->header('Media');
            if($task == 'viewlist' || $task == 'editlist' || $task == 'trashedlist'){
                $this->HTMLMedia->search(self::$fieldsNames, $task);
            }
            //echo '<div id="core_content">';

        }

        try{
            switch ($task) {
                case 'edit':       $this->edit(); break;
                case 'del':        $this->del(); break;
                case 'delHtmx':    $this->delHTMX(); break;
                case 'editlist':   $this->editlist($textSearch); break;
                case 'childlist':  $this->childlist(); break;

                case 'undelHtmx':  $this->undelHTMX(); break;
                case 'trashedlist':$this->trashedlist(); break;
                case 'logicaldeleteHtmx':    $this->delHTMX(1); break;

                case 'view':       $this->view(); break;
                case 'viewlist':   $this->viewList($textSearch); break;


                default: throw new \ErrorException('Page not found', 404, E_ERROR);
            }
        }
        catch(Exception $e){
            $this->message['type'] = 'danger';
            $this->message['text'] = $e->getMessage();
        }

        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            //echo '</div>';
            $this->HTMLMedia->footer();
        }

    }

    /**
     * Edit or add a row in media
     * @return void
     */
    function edit(string $task='edit'){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $this->dataMedia->id_media = filter_var($_POST['id_media'] ?? null, FILTER_VALIDATE_INT);
            $isNew = !($this->dataMedia->id_media > 0);
            $hasFile = MediaImage::isUploaded($_FILES['file'] ?? null);
            $idProduct = (int)filter_var($_POST['id_product'] ?? null, FILTER_SANITIZE_NUMBER_INT);

            if(!$isNew && !$this->dataMedia->get()){
                $this->message['type'] = 'danger';
                $this->message['text'] = 'This media does not exist anymore';
            }
            elseif($isNew && !$hasFile){
                $this->message['type'] = 'danger';
                $this->message['text'] = 'Choose an image';
            }
            else{
                // The file of the image before the change: filename, mime type, size and dimensions come from the file
                $oldFilename = $isNew ? '' : (string)$this->dataMedia->filename;
                $stored = null;

                $this->dataMedia->alt_text = (string)filter_var($_POST['alt_text'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataMedia->caption = (string)filter_var($_POST['caption'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);

                try{
                    if($hasFile){
                        // Name of the file: the product, else the alt text, else the name of the uploaded file
                        if(isset($this->productList[$idProduct])){
                            $name = (string)$this->productList[$idProduct]['name'];
                        }
                        elseif($this->dataMedia->alt_text != ''){
                            $name = $this->dataMedia->alt_text;
                        }
                        else{
                            $name = pathinfo((string)($_FILES['file']['name'] ?? ''), PATHINFO_FILENAME);
                        }

                        $stored = MediaImage::store($_FILES['file'], $name);
                        $this->dataMedia->filename = $stored['filename'];
                        $this->dataMedia->mime_type = $stored['mime_type'];
                        $this->dataMedia->size_bytes = $stored['size_bytes'];
                        $this->dataMedia->width = $stored['width'];
                        $this->dataMedia->height = $stored['height'];
                    }

                    if(!$this->dataMedia->save()){
                        throw new \RuntimeException('The entry could not be saved');
                    }

                    // The files of the replaced image are not used anymore
                    if($stored !== null && $oldFilename != '' && $oldFilename != $stored['filename']){
                        MediaImage::delete($oldFilename);
                    }

                    $this->message['type'] = 'success';
                    $this->message['text'] = 'The entry has been saved';
                }
                catch(\Throwable $e){
                    // The new files are useless if the entry is not saved
                    if($stored !== null){
                        MediaImage::delete($stored['filename']);
                    }
                    if(!($e instanceof \RuntimeException)){
                        error_log('Media upload: '.$e->getMessage());
                    }
                    $this->message['type'] = 'danger';
                    $this->message['text'] = $e instanceof \RuntimeException && !($e instanceof \PDOException) ? $e->getMessage() : 'The entry could not be saved';
                    if($isNew){
                        $this->dataMedia->id_media = null;
                    }
                }

                // A new image named after a product is added to the images of this product
                if($isNew && $this->message['type'] == 'success' && isset($this->productList[$idProduct])){
                    try{
                        $oProductMedia = new \Apgenic\ProductMedia\ModelProductMedia();
                        $position = $oProductMedia->getList(0, 0, array('id_product' => $idProduct)) + 1;
                        $oProductMedia->id_product = $idProduct;
                        $oProductMedia->id_media = $this->dataMedia->id_media;
                        $oProductMedia->position = $position;
                        $oProductMedia->add();
                    }
                    catch(\Throwable $e){
                        error_log('Media upload, link to the product: '.$e->getMessage());
                        $this->message['type'] = 'warning';
                        $this->message['text'] = 'The image has been saved, but it could not be added to the product';
                    }
                }
            }
        }

        else{
            $this->dataMedia->id_media = filter_var($_GET['id_media'] ?? null, FILTER_VALIDATE_INT);

        }


        if( 1 && $this->dataMedia->id_media > 0 ){
            $this->dataMedia->get();
        }
        else{
            // Do not show tabs when wee create the entry
            $this->showTabs = 0;
        }


        if($task == 'edit'){
            $this->HTMLMedia->edit($this->dataMedia,  $this->showTabs, $this->message, 'edit');
        }
        else{
            $this->dataMedia = new ModelMedia();
            $this->HTMLMedia->edit($this->dataMedia,  $this->showTabs, $this->message, 'childlist');
        }
    }



    /**
     * Delete a row in media and display the list of elements
     * @return void
     */
    function del(){
        $this->dataMedia->id_media = filter_var($_POST['id_media'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataMedia->id_media){
            $this->delWithFiles();
            $this->message['type'] = 'success';
            $this->message['text'] = 'Done';
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }

        $this->editlist();
    }



    /**
     * Delete a row in media and return a JSON response
     * @return void
     */
    function delHtmx(int $logical=0){
        $this->dataMedia->id_media = filter_var($_GET['id_media'] ?? null, FILTER_VALIDATE_INT);

        if( 1  && $this->dataMedia->id_media ){

            if($logical == 1){
                $this->dataMedia->logicalDel(1);
            }
            else{
                $this->delWithFiles();
            }
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }

        if(count($this->message)){
            //http_response_code(204);
            $this->HTMLMedia->message($this->message);
        }
    }

    /**
     * Display a list of row from media with add and del buttons
     * @return void
     */
    function editlist($textSearch = ''){
        // Display items list for edition
        $this->filters['deleted_at'] = NULL;
        $nbItems = $this->dataMedia->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLMedia->editList($this->dataMedia->list ,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLMedia->pagination($nbPages, $this->page, "?component=media&task=editlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * Display tabs above the edit form if the element have data from others tables
     * @return void
     */
    function childlist(){
        $this->edit('childlist');
        $this->dataMedia->getList(0 , 0, $this->filters, $this->orderBy, strtoupper($this->order));
        $this->HTMLMedia->childList($this->dataMedia->list ,  $this->filters, $this->orderBy, $this->revertOrder);
    }


    /**
     * Display element details
     * @return void
     */
    function view(){
        // Display selected trainings
        $this->dataMedia->id_media = filter_var($_GET['id_media'] ?? null, FILTER_VALIDATE_INT);
        $this->dataMedia->get();
        $this->HTMLMedia->view($this->dataMedia,  $this->message);
    }


    /**
     * Display a list of row from media with only the view button
     * For consultation without editing
     * @return void
     */
    function viewList($textSearch = ''){
        // Display items list
        $this->filters['deleted_at'] = NULL;


        $nbItems = $this->dataMedia->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLMedia->viewList($this->dataMedia->list,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLMedia->pagination($nbPages, $this->page, "?component=media&task=viewlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * Display a list of row from media with only the view button
     * For consultation without editing
     * @return void
     */
    function trashedList($textSearch = ''){
        // Display items list
        $nbItems = $this->dataMedia->getList(($this->page * $this->itemsByPage)-$this->itemsByPage ,
                                                                 $this->itemsByPage, array('deleted_at' => 1),
                                                                 $this->orderBy,
                                                                 strtoupper($this->order),
                                                                 $textSearch);

        $this->HTMLMedia->trashedList($this->dataMedia->list,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLMedia->pagination($nbPages, $this->page, "?component=media&task=trashedlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * UnDelete a row in media and display the list of elements
     * @return void
     */
    function undelHTMX(){
        $this->dataMedia->id_media = filter_var($_GET['id_media'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataMedia->id_media){
            $this->dataMedia->logicalUnDel();
            $this->message['type'] = 'success';
            $this->message['text'] = 'Done';
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }
        if(count($this->message)){
            //http_response_code(204);
            //$this->HTMLMedia->message($this->message);
        }
    }


    /**
     * Delete a row in media and display the list of elements
     * @return void
     */
    function logicaldeleteHtmx(){
        $this->dataMedia->id_media = filter_var($_GET['id_media'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataMedia->id_media){
            $this->dataMedia->logicalDel();
            $this->message['type'] = 'success';
            $this->message['text'] = 'Done';
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }
        if(count($this->message)){
            //http_response_code(204);
            //$this->HTMLMedia->message($this->message);
        }
    }



    /**
     * Set the order and filter private variable depending of the browser request
     * @return void
     */
    protected function setOrderAndFilters($defaultOrderBy = '', $defaultOrder = 'asc'):void{
        // FK Filter(s)
        if(isset($_GET['field_search'])){
            $this->filters[$_GET['field_search']] = $_GET['field_search_value'];
        }

        parent::setOrderAndFilters('id_media', 'desc');
    }


    /**
     * Delete the row in media, then the files of its image
     * @return void
     */
    private function delWithFiles():void{
        $this->dataMedia->get();
        $filename = (string)$this->dataMedia->filename;

        if($this->dataMedia->del() && $filename != ''){
            MediaImage::delete($filename);
        }
    }

}
