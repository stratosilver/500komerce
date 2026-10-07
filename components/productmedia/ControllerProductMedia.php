<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

declare(strict_types=1);
namespace Apgenic\ProductMedia;


class ControllerProductMedia  extends \Apgenic\Classes\Controller {

    private ViewProductMedia $HTMLProductMedia;
    private ModelProductMedia $dataProductMedia;

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

        $this->HTMLProductMedia = new ViewProductMedia($oModelMedia->list, $oModelProduct->list);
        $this->dataProductMedia = new ModelProductMedia();



        // Task to execute
        $task = filter_var($_GET['task'] ?? 'editlist', FILTER_SANITIZE_SPECIAL_CHARS); ;

        $this->setOrderAndFilters();
        $this->HTMLProductMedia->filters = $this->filters;
        $this->HTMLProductMedia->filtersGet = $this->filtersGet;


        $textSearch = filter_var($_GET['text_search'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            $this->HTMLProductMedia->header('Product media');
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


                default: throw new \ErrorException('Page not found', 404, E_ERROR);
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

            $this->dataProductMedia->id_product = filter_var($_POST['id_product'] ?? null, FILTER_VALIDATE_INT);            $this->dataProductMedia->id_media = filter_var($_POST['id_media'] ?? null, FILTER_VALIDATE_INT);
            if(filter_var($_POST['id_product'] ?? null, FILTER_UNSAFE_RAW) &&
                filter_var($_POST['id_media'] ?? null, FILTER_UNSAFE_RAW)){

                $this->dataProductMedia->id_product = (int)filter_var($_POST['id_product'] ?? null, FILTER_SANITIZE_NUMBER_INT);
                $this->dataProductMedia->id_media = (int)filter_var($_POST['id_media'] ?? null, FILTER_SANITIZE_NUMBER_INT);
                $this->dataProductMedia->position = (int)filter_var($_POST['position'] ?? null, FILTER_SANITIZE_NUMBER_INT);

                $this->dataProductMedia->add();
                $this->message['type'] = 'success';
                $this->message['text'] = 'The entry has been added successfully';
            }

            else{
                $this->message['type'] = 'danger';
                $this->message['text'] = 'Fill all mandatory fields';
            }
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
     * Delete a row in product_media and display the list of elements
     * @return void
     */
    function del(){
        $this->dataProductMedia->id_product = filter_var($_POST['id_product'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataProductMedia->id_product){
            $this->dataProductMedia->del();
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
            $this->message['text'] = 'Missing parameter';
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
