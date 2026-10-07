<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

declare(strict_types=1);
namespace Apgenic\Discount;


class ControllerDiscount  extends \Apgenic\Classes\Controller {

    private ViewDiscount $HTMLDiscount;
    private ModelDiscount $dataDiscount;

    // Accepted sort fields
    public static array $fieldsNames = array('id_discount','date_from','date_to','id_category','id_product','amount','percentage','created_at','updated_at','deleted_at');

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
        $this->HTMLDiscount = new ViewDiscount();
        $this->dataDiscount = new ModelDiscount();



        // Task to execute
        $task = filter_var($_GET['task'] ?? 'editlist', FILTER_SANITIZE_SPECIAL_CHARS); ;

        $this->setOrderAndFilters();
        $this->HTMLDiscount->filters = $this->filters;
        $this->HTMLDiscount->filtersGet = $this->filtersGet;


        $textSearch = filter_var($_GET['text_search'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            $this->HTMLDiscount->header('Discount');
            if($task == 'viewlist' || $task == 'editlist' || $task == 'trashedlist'){
                $this->HTMLDiscount->search(self::$fieldsNames, $task);
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
            $this->HTMLDiscount->footer();
        }

    }

    /**
     * Edit or add a row in discount
     * @return void
     */
    function edit(string $task='edit'){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $this->dataDiscount->id_discount = filter_var($_POST['id_discount'] ?? null, FILTER_VALIDATE_INT);
                $this->dataDiscount->date_from = (string)filter_var($_POST['date_from'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataDiscount->date_to = (string)filter_var($_POST['date_to'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataDiscount->id_category = $_POST['id_category'] == '' ? null : (int)filter_var($_POST['id_category'] ?? null, FILTER_SANITIZE_NUMBER_INT);
                $this->dataDiscount->id_product = $_POST['id_product'] == '' ? null : (int)filter_var($_POST['id_product'] ?? null, FILTER_SANITIZE_NUMBER_INT);
                $this->dataDiscount->amount = $_POST['amount'] == '' ? null : (int)filter_var($_POST['amount'] ?? null, FILTER_SANITIZE_NUMBER_INT);
                $this->dataDiscount->percentage = $_POST['percentage'] == '' ? null : (int)filter_var($_POST['percentage'] ?? null, FILTER_SANITIZE_NUMBER_INT);

                $this->dataDiscount->save();
                $this->message['type'] = 'success';
                $this->message['text'] = 'The entry has been saved';
            }

        else{
            $this->dataDiscount->id_discount = filter_var($_GET['id_discount'] ?? null, FILTER_VALIDATE_INT);

        }


        if( 1 && $this->dataDiscount->id_discount > 0 ){
            $this->dataDiscount->get();
        }
        else{
            // Do not show tabs when wee create the entry
            $this->showTabs = 0;
        }


        if($task == 'edit'){
            $this->HTMLDiscount->edit($this->dataDiscount,  $this->showTabs, $this->message, 'edit');
        }
        else{
            $this->dataDiscount = new ModelDiscount();
            $this->HTMLDiscount->edit($this->dataDiscount,  $this->showTabs, $this->message, 'childlist');
        }
    }



    /**
     * Delete a row in discount and display the list of elements
     * @return void
     */
    function del(){
        $this->dataDiscount->id_discount = filter_var($_POST['id_discount'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataDiscount->id_discount){
            $this->dataDiscount->del();
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
     * Delete a row in discount and return a JSON response
     * @return void
     */
    function delHtmx(int $logical=0){
        $this->dataDiscount->id_discount = filter_var($_GET['id_discount'] ?? null, FILTER_VALIDATE_INT);

        if( 1  && $this->dataDiscount->id_discount ){

            if($logical == 1){
                $this->dataDiscount->logicalDel(1);
            }
            else{
                $this->dataDiscount->del();
            }
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }

        if(count($this->message)){
            //http_response_code(204);
            $this->HTMLDiscount->message($this->message);
        }
    }

    /**
     * Display a list of row from discount with add and del buttons
     * @return void
     */
    function editlist($textSearch = ''){
        // Display items list for edition
        $this->filters['deleted_at'] = NULL;
        $nbItems = $this->dataDiscount->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLDiscount->editList($this->dataDiscount->list ,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLDiscount->pagination($nbPages, $this->page, "?component=discount&task=editlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * Display tabs above the edit form if the element have data from others tables
     * @return void
     */
    function childlist(){
        $this->edit('childlist');
        $this->dataDiscount->getList(0 , 0, $this->filters, $this->orderBy, strtoupper($this->order));
        $this->HTMLDiscount->childList($this->dataDiscount->list ,  $this->filters, $this->orderBy, $this->revertOrder);
    }


    /**
     * Display element details
     * @return void
     */
    function view(){
        // Display selected trainings
        $this->dataDiscount->id_discount = filter_var($_GET['id_discount'] ?? null, FILTER_VALIDATE_INT);
        $this->dataDiscount->get();
        $this->HTMLDiscount->view($this->dataDiscount,  $this->message);
    }


    /**
     * Display a list of row from discount with only the view button
     * For consultation without editing
     * @return void
     */
    function viewList($textSearch = ''){
        // Display items list
        $this->filters['deleted_at'] = NULL;


        $nbItems = $this->dataDiscount->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLDiscount->viewList($this->dataDiscount->list,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLDiscount->pagination($nbPages, $this->page, "?component=discount&task=viewlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * Display a list of row from discount with only the view button
     * For consultation without editing
     * @return void
     */
    function trashedList($textSearch = ''){
        // Display items list
        $nbItems = $this->dataDiscount->getList(($this->page * $this->itemsByPage)-$this->itemsByPage ,
                                                                 $this->itemsByPage, array('deleted_at' => 1),
                                                                 $this->orderBy,
                                                                 strtoupper($this->order),
                                                                 $textSearch);

        $this->HTMLDiscount->trashedList($this->dataDiscount->list,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLDiscount->pagination($nbPages, $this->page, "?component=discount&task=trashedlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * UnDelete a row in discount and display the list of elements
     * @return void
     */
    function undelHTMX(){
        $this->dataDiscount->id_discount = filter_var($_GET['id_discount'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataDiscount->id_discount){
            $this->dataDiscount->logicalUnDel();
            $this->message['type'] = 'success';
            $this->message['text'] = 'Done';
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }
        if(count($this->message)){
            //http_response_code(204);
            //$this->HTMLDiscount->message($this->message);
        }
    }


    /**
     * Delete a row in discount and display the list of elements
     * @return void
     */
    function logicaldeleteHtmx(){
        $this->dataDiscount->id_discount = filter_var($_GET['id_discount'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataDiscount->id_discount){
            $this->dataDiscount->logicalDel();
            $this->message['type'] = 'success';
            $this->message['text'] = 'Done';
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }
        if(count($this->message)){
            //http_response_code(204);
            //$this->HTMLDiscount->message($this->message);
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

        parent::setOrderAndFilters('id_discount', 'desc');
    }



}
