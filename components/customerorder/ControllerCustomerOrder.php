<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

declare(strict_types=1);
namespace Apgenic\CustomerOrder;


class ControllerCustomerOrder  extends \Apgenic\Classes\Controller {

    private ViewCustomerOrder $HTMLCustomerOrder;
    private ModelCustomerOrder $dataCustomerOrder;

    // Accepted sort fields
    public static array $fieldsNames = array('id_customer_order','id_user','created_at');

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

        $oModelProduct = new \Apgenic\Product\ModelProduct();
        $oModelProduct->getList();

        $this->HTMLCustomerOrder = new ViewCustomerOrder($oModelProduct->list);
        $this->dataCustomerOrder = new ModelCustomerOrder();



        // Task to execute
        $task = filter_var($_GET['task'] ?? 'editlist', FILTER_SANITIZE_SPECIAL_CHARS); ;

        $this->setOrderAndFilters();
        $this->HTMLCustomerOrder->filters = $this->filters;
        $this->HTMLCustomerOrder->filtersGet = $this->filtersGet;


        $textSearch = filter_var($_GET['text_search'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            $this->HTMLCustomerOrder->header('Customer order');
            if($task == 'viewlist' || $task == 'editlist' || $task == 'trashedlist'){
                $this->HTMLCustomerOrder->search(self::$fieldsNames, $task);
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
            $this->HTMLCustomerOrder->footer();
        }

    }

    /**
     * Edit or add a row in customer_order
     * @return void
     */
    function edit(string $task='edit'){
        // For when the edit is under a tab
        $this->dataCustomerOrder->id_user = (int)($_GET['id_user'] ?? 0);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $this->dataCustomerOrder->id_customer_order = filter_var($_POST['id_customer_order'] ?? null, FILTER_VALIDATE_INT);
            if(filter_var($_POST['id_user'] ?? null, FILTER_UNSAFE_RAW)){

                $this->dataCustomerOrder->id_user = (string)filter_var($_POST['id_user'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);

                $this->dataCustomerOrder->save();
                $this->message['type'] = 'success';
                $this->message['text'] = 'The entry has been saved';
            }

            else{
                $this->message['type'] = 'danger';
                $this->message['text'] = 'Fill all mandatory fields';
            }
        }

        else{
            $this->dataCustomerOrder->id_customer_order = filter_var($_GET['id_customer_order'] ?? null, FILTER_VALIDATE_INT);

        }


        if( 1 && $this->dataCustomerOrder->id_customer_order > 0 ){
            $this->dataCustomerOrder->get();
        }
        else{
            // Do not show tabs when wee create the entry
            $this->showTabs = 0;
        }


        if($task == 'edit'){
            $this->HTMLCustomerOrder->edit($this->dataCustomerOrder,  $this->showTabs, $this->message, 'edit');
        }
        else{
            $this->dataCustomerOrder = new ModelCustomerOrder();
            $this->HTMLCustomerOrder->edit($this->dataCustomerOrder,  $this->showTabs, $this->message, 'childlist');
        }
    }



    /**
     * Delete a row in customer_order and display the list of elements
     * @return void
     */
    function del(){
        $this->dataCustomerOrder->id_customer_order = filter_var($_POST['id_customer_order'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataCustomerOrder->id_customer_order){
            $this->dataCustomerOrder->del();
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
     * Delete a row in customer_order and return a JSON response
     * @return void
     */
    function delHtmx(int $logical=0){
        $this->dataCustomerOrder->id_customer_order = filter_var($_GET['id_customer_order'] ?? null, FILTER_VALIDATE_INT);

        if( 1  && $this->dataCustomerOrder->id_customer_order ){

            $this->dataCustomerOrder->del();

        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }

        if(count($this->message)){
            //http_response_code(204);
            $this->HTMLCustomerOrder->message($this->message);
        }
    }

    /**
     * Display a list of row from customer_order with add and del buttons
     * @return void
     */
    function editlist($textSearch = ''){
        // Display items list for edition

        $nbItems = $this->dataCustomerOrder->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLCustomerOrder->editList($this->dataCustomerOrder->list ,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLCustomerOrder->pagination($nbPages, $this->page, "?component=customerorder&task=editlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * Display tabs above the edit form if the element have data from others tables
     * @return void
     */
    function childlist(){
        $this->edit('childlist');
        $this->dataCustomerOrder->getList(0 , 0, $this->filters, $this->orderBy, strtoupper($this->order));
        $this->HTMLCustomerOrder->childList($this->dataCustomerOrder->list ,  $this->filters, $this->orderBy, $this->revertOrder);
    }


    /**
     * Display element details
     * @return void
     */
    function view(){
        // Display selected trainings
        $this->dataCustomerOrder->id_customer_order = filter_var($_GET['id_customer_order'] ?? null, FILTER_VALIDATE_INT);
        $this->dataCustomerOrder->get();
        $this->HTMLCustomerOrder->view($this->dataCustomerOrder,  $this->message);
    }


    /**
     * Display a list of row from customer_order with only the view button
     * For consultation without editing
     * @return void
     */
    function viewList($textSearch = ''){
        // Display items list



        $nbItems = $this->dataCustomerOrder->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLCustomerOrder->viewList($this->dataCustomerOrder->list,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLCustomerOrder->pagination($nbPages, $this->page, "?component=customerorder&task=viewlist&".$this->filtersGet."&".$orderGet."&");
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

        parent::setOrderAndFilters('id_customer_order', 'desc');
    }



}
