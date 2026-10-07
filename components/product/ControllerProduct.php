<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

declare(strict_types=1);
namespace Apgenic\Product;


class ControllerProduct  extends \Apgenic\Classes\Controller {

    private ViewProduct $HTMLProduct;
    private ModelProduct $dataProduct;

    // Accepted sort fields
    public static array $fieldsNames = array('id_product','id_user','name','slug','summary','description','status','published_at','created_at','updated_at','deleted_at');

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

        $oModelUser = new \Apgenic\User\ModelUser();
        $oModelUser->getList();

        $this->HTMLProduct = new ViewProduct($oModelUser->list);
        $this->dataProduct = new ModelProduct();



        // Task to execute
        $task = filter_var($_GET['task'] ?? 'editlist', FILTER_SANITIZE_SPECIAL_CHARS); ;

        $this->setOrderAndFilters();
        $this->HTMLProduct->filters = $this->filters;
        $this->HTMLProduct->filtersGet = $this->filtersGet;


        $textSearch = filter_var($_GET['text_search'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            $this->HTMLProduct->header('Product');
            if($task == 'viewlist' || $task == 'editlist' || $task == 'trashedlist'){
                $this->HTMLProduct->search(self::$fieldsNames, $task);
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
            $this->HTMLProduct->footer();
        }

    }

    /**
     * Edit or add a row in product
     * @return void
     */
    function edit(string $task='edit'){
        // For when the edit is under a tab
        $this->dataProduct->id_user = (int)($_GET['id_user'] ?? 0);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $this->dataProduct->id_product = filter_var($_POST['id_product'] ?? null, FILTER_VALIDATE_INT);
            if(filter_var($_POST['name'] ?? null, FILTER_UNSAFE_RAW)){

                $this->dataProduct->id_user = $_POST['id_user'] == '' ? null : (int)filter_var($_POST['id_user'] ?? null, FILTER_SANITIZE_NUMBER_INT);
                $this->dataProduct->name = (string)filter_var($_POST['name'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataProduct->slug = (string)filter_var($_POST['slug'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataProduct->summary = (string)filter_var($_POST['summary'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataProduct->description = (string)filter_var($_POST['description'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataProduct->status = (string)filter_var($_POST['status'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataProduct->published_at = (string)filter_var($_POST['published_at'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);

                $this->dataProduct->save();
                $this->message['type'] = 'success';
                $this->message['text'] = 'The entry has been saved';
            }

            else{
                $this->message['type'] = 'danger';
                $this->message['text'] = 'Fill all mandatory fields';
            }
        }

        else{
            $this->dataProduct->id_product = filter_var($_GET['id_product'] ?? null, FILTER_VALIDATE_INT);

        }


        if( 1 && $this->dataProduct->id_product > 0 ){
            $this->dataProduct->get();
        }
        else{
            // Do not show tabs when wee create the entry
            $this->showTabs = 0;
        }


        if($task == 'edit'){
            $this->HTMLProduct->edit($this->dataProduct,  $this->showTabs, $this->message, 'edit');
        }
        else{
            $this->dataProduct = new ModelProduct();
            $this->HTMLProduct->edit($this->dataProduct,  $this->showTabs, $this->message, 'childlist');
        }
    }



    /**
     * Delete a row in product and display the list of elements
     * @return void
     */
    function del(){
        $this->dataProduct->id_product = filter_var($_POST['id_product'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataProduct->id_product){
            $this->dataProduct->del();
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
     * Delete a row in product and return a JSON response
     * @return void
     */
    function delHtmx(int $logical=0){
        $this->dataProduct->id_product = filter_var($_GET['id_product'] ?? null, FILTER_VALIDATE_INT);

        if( 1  && $this->dataProduct->id_product ){

            if($logical == 1){
                $this->dataProduct->logicalDel(1);
            }
            else{
                $this->dataProduct->del();
            }
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }

        if(count($this->message)){
            //http_response_code(204);
            $this->HTMLProduct->message($this->message);
        }
    }

    /**
     * Display a list of row from product with add and del buttons
     * @return void
     */
    function editlist($textSearch = ''){
        // Display items list for edition
        $this->filters['deleted_at'] = NULL;
        $nbItems = $this->dataProduct->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLProduct->editList($this->dataProduct->list ,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLProduct->pagination($nbPages, $this->page, "?component=product&task=editlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * Display tabs above the edit form if the element have data from others tables
     * @return void
     */
    function childlist(){
        $this->edit('childlist');
        $this->dataProduct->getList(0 , 0, $this->filters, $this->orderBy, strtoupper($this->order));
        $this->HTMLProduct->childList($this->dataProduct->list ,  $this->filters, $this->orderBy, $this->revertOrder);
    }


    /**
     * Display element details
     * @return void
     */
    function view(){
        // Display selected trainings
        $this->dataProduct->id_product = filter_var($_GET['id_product'] ?? null, FILTER_VALIDATE_INT);
        $this->dataProduct->get();
        $this->HTMLProduct->view($this->dataProduct,  $this->message);
    }


    /**
     * Display a list of row from product with only the view button
     * For consultation without editing
     * @return void
     */
    function viewList($textSearch = ''){
        // Display items list
        $this->filters['deleted_at'] = NULL;


        $nbItems = $this->dataProduct->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLProduct->viewList($this->dataProduct->list,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLProduct->pagination($nbPages, $this->page, "?component=product&task=viewlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * Display a list of row from product with only the view button
     * For consultation without editing
     * @return void
     */
    function trashedList($textSearch = ''){
        // Display items list
        $nbItems = $this->dataProduct->getList(($this->page * $this->itemsByPage)-$this->itemsByPage ,
                                                                 $this->itemsByPage, array('deleted_at' => 1),
                                                                 $this->orderBy,
                                                                 strtoupper($this->order),
                                                                 $textSearch);

        $this->HTMLProduct->trashedList($this->dataProduct->list,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLProduct->pagination($nbPages, $this->page, "?component=product&task=trashedlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * UnDelete a row in product and display the list of elements
     * @return void
     */
    function undelHTMX(){
        $this->dataProduct->id_product = filter_var($_GET['id_product'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataProduct->id_product){
            $this->dataProduct->logicalUnDel();
            $this->message['type'] = 'success';
            $this->message['text'] = 'Done';
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }
        if(count($this->message)){
            //http_response_code(204);
            //$this->HTMLProduct->message($this->message);
        }
    }


    /**
     * Delete a row in product and display the list of elements
     * @return void
     */
    function logicaldeleteHtmx(){
        $this->dataProduct->id_product = filter_var($_GET['id_product'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataProduct->id_product){
            $this->dataProduct->logicalDel();
            $this->message['type'] = 'success';
            $this->message['text'] = 'Done';
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }
        if(count($this->message)){
            //http_response_code(204);
            //$this->HTMLProduct->message($this->message);
        }
    }



    /**
     * Set the order and filter private variable depending of the browser request
     * @return void
     */
    protected function setOrderAndFilters($defaultOrderBy = '', $defaultOrder = 'asc'):void{
        // FK Filter(s)
        if(isset($_GET['filters']['id_user'])){
            $this->filters['id_user'] = intval($_GET['filters']['id_user']);
        }

        if(isset($_GET['field_search'])){
            $this->filters[$_GET['field_search']] = $_GET['field_search_value'];
        }

        parent::setOrderAndFilters('id_product', 'desc');
    }



}
