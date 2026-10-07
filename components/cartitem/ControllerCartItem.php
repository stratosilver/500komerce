<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

declare(strict_types=1);
namespace Apgenic\CartItem;


class ControllerCartItem  extends \Apgenic\Classes\Controller {

    private ViewCartItem $HTMLCartItem;
    private ModelCartItem $dataCartItem;

    // Accepted sort fields
    public static array $fieldsNames = array('id_cart_item','id_user','cookie_id','id_product','id_product_variant','quantity','created_at','updated_at');

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

        $oModelProductVariant = new \Apgenic\Productvariant\ModelProductVariant();
        $oModelProductVariant->getList();


        $oModelUser = new \Apgenic\User\ModelUser();
        $oModelUser->getList();


        $oModelProduct = new \Apgenic\Product\ModelProduct();
        $oModelProduct->getList();

        $this->HTMLCartItem = new ViewCartItem($oModelProductVariant->list, $oModelUser->list, $oModelProduct->list);
        $this->dataCartItem = new ModelCartItem();



        // Task to execute
        $task = filter_var($_GET['task'] ?? 'editlist', FILTER_SANITIZE_SPECIAL_CHARS); ;

        $this->setOrderAndFilters();
        $this->HTMLCartItem->filters = $this->filters;
        $this->HTMLCartItem->filtersGet = $this->filtersGet;


        $textSearch = filter_var($_GET['text_search'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            $this->HTMLCartItem->header('Cart item');
            if($task == 'viewlist' || $task == 'editlist' || $task == 'trashedlist'){
                $this->HTMLCartItem->search(self::$fieldsNames, $task);
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
            $this->HTMLCartItem->footer();
        }

    }

    /**
     * Edit or add a row in cart_item
     * @return void
     */
    function edit(string $task='edit'){
        // For when the edit is under a tab
        $this->dataCartItem->id_product_variant = (int)($_GET['id_product_variant'] ?? 0);

        // For when the edit is under a tab
        $this->dataCartItem->id_user = (int)($_GET['id_user'] ?? 0);

        // For when the edit is under a tab
        $this->dataCartItem->id_product = (int)($_GET['id_product'] ?? 0);

        // For when the edit is under a tab
        $this->dataCartItem->id_product_variant = (int)($_GET['id_product_variant'] ?? 0);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $this->dataCartItem->id_cart_item = filter_var($_POST['id_cart_item'] ?? null, FILTER_VALIDATE_INT);
            if(filter_var($_POST['id_cart_item'] ?? null, FILTER_UNSAFE_RAW) &&
                filter_var($_POST['id_user'] ?? null, FILTER_UNSAFE_RAW) &&
                filter_var($_POST['id_product'] ?? null, FILTER_UNSAFE_RAW) &&
                filter_var($_POST['quantity'] ?? null, FILTER_UNSAFE_RAW)){

                $this->dataCartItem->id_cart_item = (int)filter_var($_POST['id_cart_item'] ?? null, FILTER_SANITIZE_NUMBER_INT);
                $this->dataCartItem->id_user = (int)filter_var($_POST['id_user'] ?? null, FILTER_SANITIZE_NUMBER_INT);
                $this->dataCartItem->cookie_id = ($_POST['cookie_id'] ?? '') == '' ? null : (string)filter_var($_POST['cookie_id'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataCartItem->id_product = (int)filter_var($_POST['id_product'] ?? null, FILTER_SANITIZE_NUMBER_INT);
                $this->dataCartItem->id_product_variant = ($_POST['id_product_variant'] ?? '') == '' ? null : (int)filter_var($_POST['id_product_variant'] ?? null, FILTER_SANITIZE_NUMBER_INT);
                $this->dataCartItem->quantity = (int)filter_var($_POST['quantity'] ?? null, FILTER_SANITIZE_NUMBER_INT);

                // id_cart_item is not generated by the database: save() only updates, a new id must be inserted
                $existing = new ModelCartItem();
                $existing->id_cart_item = $this->dataCartItem->id_cart_item;
                if($existing->get()){
                    $this->dataCartItem->save();
                }
                else{
                    $this->dataCartItem->add();
                }
                $this->message['type'] = 'success';
                $this->message['text'] = 'The entry has been saved';
            }

            else{
                $this->message['type'] = 'danger';
                $this->message['text'] = 'Fill all mandatory fields';
            }
        }

        else{
            $this->dataCartItem->id_cart_item = filter_var($_GET['id_cart_item'] ?? null, FILTER_VALIDATE_INT);

        }


        if( 1 && $this->dataCartItem->id_cart_item > 0 ){
            $this->dataCartItem->get();
        }
        else{
            // Do not show tabs when wee create the entry
            $this->showTabs = 0;
        }


        if($task == 'edit'){
            $this->HTMLCartItem->edit($this->dataCartItem,  $this->showTabs, $this->message, 'edit');
        }
        else{
            $this->dataCartItem = new ModelCartItem();
            $this->HTMLCartItem->edit($this->dataCartItem,  $this->showTabs, $this->message, 'childlist');
        }
    }



    /**
     * Delete a row in cart_item and display the list of elements
     * @return void
     */
    function del(){
        $this->dataCartItem->id_cart_item = filter_var($_POST['id_cart_item'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataCartItem->id_cart_item){
            $this->dataCartItem->del();
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
     * Delete a row in cart_item and return a JSON response
     * @return void
     */
    function delHtmx(int $logical=0){
        $this->dataCartItem->id_cart_item = filter_var($_GET['id_cart_item'] ?? null, FILTER_VALIDATE_INT);

        if( 1  && $this->dataCartItem->id_cart_item ){

            $this->dataCartItem->del();

        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }

        if(count($this->message)){
            //http_response_code(204);
            $this->HTMLCartItem->message($this->message);
        }
    }

    /**
     * Display a list of row from cart_item with add and del buttons
     * @return void
     */
    function editlist($textSearch = ''){
        // Display items list for edition

        $nbItems = $this->dataCartItem->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLCartItem->editList($this->dataCartItem->list ,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLCartItem->pagination($nbPages, $this->page, "?component=cartitem&task=editlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * Display tabs above the edit form if the element have data from others tables
     * @return void
     */
    function childlist(){
        $this->edit('childlist');
        $this->dataCartItem->getList(0 , 0, $this->filters, $this->orderBy, strtoupper($this->order));
        $this->HTMLCartItem->childList($this->dataCartItem->list ,  $this->filters, $this->orderBy, $this->revertOrder);
    }


    /**
     * Display element details
     * @return void
     */
    function view(){
        // Display selected trainings
        $this->dataCartItem->id_cart_item = filter_var($_GET['id_cart_item'] ?? null, FILTER_VALIDATE_INT);
        $this->dataCartItem->get();
        $this->HTMLCartItem->view($this->dataCartItem,  $this->message);
    }


    /**
     * Display a list of row from cart_item with only the view button
     * For consultation without editing
     * @return void
     */
    function viewList($textSearch = ''){
        // Display items list



        $nbItems = $this->dataCartItem->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLCartItem->viewList($this->dataCartItem->list,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLCartItem->pagination($nbPages, $this->page, "?component=cartitem&task=viewlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * Set the order and filter private variable depending of the browser request
     * @return void
     */
    protected function setOrderAndFilters($defaultOrderBy = '', $defaultOrder = 'asc'):void{
        // FK Filter(s)
        if(isset($_GET['filters']['id_product_variant'])){
            $this->filters['id_product_variant'] = intval($_GET['filters']['id_product_variant']);
        }

        if(isset($_GET['filters']['id_user'])){
            $this->filters['id_user'] = intval($_GET['filters']['id_user']);
        }

        if(isset($_GET['filters']['id_product'])){
            $this->filters['id_product'] = intval($_GET['filters']['id_product']);
        }

        if(isset($_GET['filters']['id_product_variant'])){
            $this->filters['id_product_variant'] = intval($_GET['filters']['id_product_variant']);
        }

        if(isset($_GET['field_search'])){
            $this->filters[$_GET['field_search']] = $_GET['field_search_value'];
        }

        parent::setOrderAndFilters('id_cart_item', 'desc');
    }



}
