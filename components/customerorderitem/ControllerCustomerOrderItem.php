<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
  
declare(strict_types=1);
namespace Apgenic\CustomerOrderItem;


class ControllerCustomerOrderItem  extends \Apgenic\Classes\Controller {
    
    private ViewCustomerOrderItem $HTMLCustomerOrderItem;
    private ModelCustomerOrderItem $dataCustomerOrderItem;
    
    // Accepted sort fields
    public static array $fieldsNames = array('id_customer_order_item','id_customer_order','id_product_variant','product_name','sku','quantity','unit_price_amount','discount_amount','tax_amount','line_total_amount','created_at');
    
    function __construct(){
        if(filter_input(INPUT_GET, 'showTabs', FILTER_VALIDATE_INT) === 0){
            $this->showTabs = 0;    
        } 
    }
    
    /**
     * Controller default method, validate inputs, create object and dispatch the request to other method depending of the request 
     * @return void
     */
    function render(){

        $oModelCustomerOrder = new \Apgenic\Customerorder\ModelCustomerOrder();
        $oModelCustomerOrder->getList();


        $oModelProductVariant = new \Apgenic\Productvariant\ModelProductVariant();
        $oModelProductVariant->getList();
  
        $this->HTMLCustomerOrderItem = new ViewCustomerOrderItem($oModelCustomerOrder->list, $oModelProductVariant->list);
        $this->dataCustomerOrderItem = new ModelCustomerOrderItem(); 
        
        
       
        // Task to execute
        $task = filter_input(INPUT_GET, 'task', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'editlist'; ;
        
        $this->setOrderAndFilters();
        $this->HTMLCustomerOrderItem->filters = $this->filters; 
        $this->HTMLCustomerOrderItem->filtersGet = $this->filtersGet; 
        
        
        $textSearch = filter_input(INPUT_GET, 'text_search', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            $this->HTMLCustomerOrderItem->header('Customer order item');
            if($task == 'viewlist' || $task == 'editlist' || $task == 'trashedlist'){
                $this->HTMLCustomerOrderItem->search(self::$fieldsNames, $task);
            }
            echo '<div id="core_content">';
                        
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
            echo '</div>';
            $this->HTMLCustomerOrderItem->footer();
        }
    
    }
    
    /**
     * Edit or add a row in customer_order_item
     * @return void
     */
    function edit(string $task='edit'){
        // For when the edit is under a tab
        $this->dataCustomerOrderItem->id_customer_order = (int)($_GET['id_customer_order'] ?? 0);

        // For when the edit is under a tab
        $this->dataCustomerOrderItem->id_product_variant = (int)($_GET['id_product_variant'] ?? 0);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            $this->dataCustomerOrderItem->id_customer_order_item = filter_input(INPUT_POST, 'id_customer_order_item', FILTER_VALIDATE_INT);
            if(filter_input(INPUT_POST, 'id_customer_order', FILTER_UNSAFE_RAW) && 
                filter_input(INPUT_POST, 'product_name', FILTER_UNSAFE_RAW) && 
                filter_input(INPUT_POST, 'sku', FILTER_UNSAFE_RAW) && 
                filter_input(INPUT_POST, 'quantity', FILTER_UNSAFE_RAW) && 
                filter_input(INPUT_POST, 'unit_price_amount', FILTER_UNSAFE_RAW) && 
                filter_input(INPUT_POST, 'line_total_amount', FILTER_UNSAFE_RAW)){

                $this->dataCustomerOrderItem->id_customer_order = (int)filter_input(INPUT_POST, 'id_customer_order', FILTER_SANITIZE_NUMBER_INT);
                $this->dataCustomerOrderItem->id_product_variant = $_POST['id_product_variant'] == '' ? null : (int)filter_input(INPUT_POST, 'id_product_variant', FILTER_SANITIZE_NUMBER_INT);
                $this->dataCustomerOrderItem->product_name = (string)filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataCustomerOrderItem->sku = (string)filter_input(INPUT_POST, 'sku', FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataCustomerOrderItem->quantity = (int)filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);
                $this->dataCustomerOrderItem->unit_price_amount = (int)filter_input(INPUT_POST, 'unit_price_amount', FILTER_SANITIZE_NUMBER_INT);
                $this->dataCustomerOrderItem->discount_amount = (int)filter_input(INPUT_POST, 'discount_amount', FILTER_SANITIZE_NUMBER_INT);
                $this->dataCustomerOrderItem->tax_amount = (int)filter_input(INPUT_POST, 'tax_amount', FILTER_SANITIZE_NUMBER_INT);
                $this->dataCustomerOrderItem->line_total_amount = (int)filter_input(INPUT_POST, 'line_total_amount', FILTER_SANITIZE_NUMBER_INT);

                $this->dataCustomerOrderItem->save();
                $this->message['type'] = 'success';
                $this->message['text'] = 'The entry has been saved';
            }
        
            else{
                $this->message['type'] = 'danger';
                $this->message['text'] = 'Fill all mandatory fields';
            }                        
        }        
        
        else{
            $this->dataCustomerOrderItem->id_customer_order_item = filter_input(INPUT_GET, 'id_customer_order_item', FILTER_VALIDATE_INT);

        }        
        

        if( 1 && $this->dataCustomerOrderItem->id_customer_order_item > 0 ){
            $this->dataCustomerOrderItem->get();
        }
        else{
            // Do not show tabs when wee create the entry 
            $this->showTabs = 0;
        }
            
            
        if($task == 'edit'){  
            $this->HTMLCustomerOrderItem->edit($this->dataCustomerOrderItem,  $this->showTabs, $this->message, 'edit');
        }
        else{
            $this->dataCustomerOrderItem = new ModelCustomerOrderItem();
            $this->HTMLCustomerOrderItem->edit($this->dataCustomerOrderItem,  $this->showTabs, $this->message, 'childlist');
        }
    }
    
    
    
    /**
     * Delete a row in customer_order_item and display the list of elements
     * @return void
     */
    function del(){
        $this->dataCustomerOrderItem->id_customer_order_item = filter_input(INPUT_POST, 'id_customer_order_item', FILTER_VALIDATE_INT);
    
        if($this->dataCustomerOrderItem->id_customer_order_item){
            $this->dataCustomerOrderItem->del();
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
     * Delete a row in customer_order_item and return a JSON response
     * @return void
     */
    function delHtmx(int $logical=0){
        $this->dataCustomerOrderItem->id_customer_order_item = filter_input(INPUT_GET, 'id_customer_order_item', FILTER_VALIDATE_INT);

        if( 1  && $this->dataCustomerOrderItem->id_customer_order_item ){

            $this->dataCustomerOrderItem->del();
                
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }
        
        if(count($this->message)){
            //http_response_code(204);
            $this->HTMLCustomerOrderItem->message($this->message);
        }
    }
    
    /**
     * Display a list of row from customer_order_item with add and del buttons
     * @return void
     */
    function editlist($textSearch = ''){
        // Display items list for edition
         
        $nbItems = $this->dataCustomerOrderItem->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLCustomerOrderItem->editList($this->dataCustomerOrderItem->list ,  $this->orderBy, $this->revertOrder, $this->message);
    
        $orderGet = 'orderBy='.$this->orderBy.'&';    
        $orderGet .= 'order='.$this->order;    
    
        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLCustomerOrderItem->pagination($nbPages, $this->page, "?component=customerorderitem&task=editlist&".$this->filtersGet."&".$orderGet."&");
    }
    
    
    /**
     * Display tabs above the edit form if the element have data from others tables
     * @return void
     */
    function childlist(){
        $this->edit('childlist');
        $this->dataCustomerOrderItem->getList(0 , 0, $this->filters, $this->orderBy, strtoupper($this->order));
        $this->HTMLCustomerOrderItem->childList($this->dataCustomerOrderItem->list ,  $this->filters, $this->orderBy, $this->revertOrder);
    }    
    
       
    /**
     * Display element details
     * @return void
     */
    function view(){
        // Display selected trainings
        $this->dataCustomerOrderItem->id_customer_order_item = filter_input(INPUT_GET, 'id_customer_order_item', FILTER_VALIDATE_INT);
        $this->dataCustomerOrderItem->get();
        $this->HTMLCustomerOrderItem->view($this->dataCustomerOrderItem,  $this->message);
    } 
    

    /**
     * Display a list of row from customer_order_item with only the view button 
     * For consultation without editing
     * @return void
     */
    function viewList($textSearch = ''){
        // Display items list 
         

        
        $nbItems = $this->dataCustomerOrderItem->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);
               
        $this->HTMLCustomerOrderItem->viewList($this->dataCustomerOrderItem->list,  $this->orderBy, $this->revertOrder, $this->message);
    
        $orderGet = 'orderBy='.$this->orderBy.'&';    
        $orderGet .= 'order='.$this->order;      
       
        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLCustomerOrderItem->pagination($nbPages, $this->page, "?component=customerorderitem&task=viewlist&".$this->filtersGet."&".$orderGet."&");
    }
    
        
    /**
     * Set the order and filter private variable depending of the browser request
     * @return void
     */
    protected function setOrderAndFilters($defaultOrderBy = '', $defaultOrder = 'asc'):void{
        // FK Filter(s)
        if(isset($_GET['filters']['id_customer_order'])){
            $this->filters['id_customer_order'] = intval($_GET['filters']['id_customer_order']);
        }      
        
        if(isset($_GET['filters']['id_product_variant'])){
            $this->filters['id_product_variant'] = intval($_GET['filters']['id_product_variant']);
        }      
        
        if(isset($_GET['field_search'])){
            $this->filters[$_GET['field_search']] = $_GET['field_search_value'];
        }        
        
        parent::setOrderAndFilters('id_customer_order_item', 'desc');
    }
    
    
    
}
