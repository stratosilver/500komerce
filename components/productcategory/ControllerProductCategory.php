<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
  
declare(strict_types=1);
namespace Apgenic\ProductCategory;


class ControllerProductCategory  extends \Apgenic\Classes\Controller {
    
    private ViewProductCategory $HTMLProductCategory;
    private ModelProductCategory $dataProductCategory;
    
    // Accepted sort fields
    public static array $fieldsNames = array('id_product','id_category');
    
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

        $oModelProduct = new \Apgenic\Product\ModelProduct();
        $oModelProduct->getList();


        $oModelCategory = new \Apgenic\Category\ModelCategory();
        $oModelCategory->getList();
  
        $this->HTMLProductCategory = new ViewProductCategory($oModelProduct->list, $oModelCategory->list);
        $this->dataProductCategory = new ModelProductCategory(); 
        
        
       
        // Task to execute
        $task = filter_input(INPUT_GET, 'task', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'editlist'; ;
        
        $this->setOrderAndFilters();
        $this->HTMLProductCategory->filters = $this->filters; 
        $this->HTMLProductCategory->filtersGet = $this->filtersGet; 
        
        
        $textSearch = filter_input(INPUT_GET, 'text_search', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            $this->HTMLProductCategory->header('Product category');
            if($task == 'viewlist' || $task == 'editlist' || $task == 'trashedlist'){
                $this->HTMLProductCategory->search(self::$fieldsNames, $task);
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
            $this->HTMLProductCategory->footer();
        }
    
    }
    
    /**
     * Edit or add a row in product_category
     * @return void
     */
    function edit(string $task='edit'){
        // For when the edit is under a tab
        $this->dataProductCategory->id_product = (int)($_GET['id_product'] ?? 0);

        // For when the edit is under a tab
        $this->dataProductCategory->id_category = (int)($_GET['id_category'] ?? 0);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            $this->dataProductCategory->id_product = filter_input(INPUT_POST, 'id_product', FILTER_VALIDATE_INT);            $this->dataProductCategory->id_category = filter_input(INPUT_POST, 'id_category', FILTER_VALIDATE_INT);
            if(filter_input(INPUT_POST, 'id_product', FILTER_UNSAFE_RAW) && 
                filter_input(INPUT_POST, 'id_category', FILTER_UNSAFE_RAW)){

                $this->dataProductCategory->id_product = (int)filter_input(INPUT_POST, 'id_product', FILTER_SANITIZE_NUMBER_INT);
                $this->dataProductCategory->id_category = (int)filter_input(INPUT_POST, 'id_category', FILTER_SANITIZE_NUMBER_INT);

                $this->dataProductCategory->add();
                $this->message['type'] = 'success';
                $this->message['text'] = 'The entry has been added successfully';
            }
        
            else{
                $this->message['type'] = 'danger';
                $this->message['text'] = 'Fill all mandatory fields';
            }                        
        }        
        
        else{
            $this->dataProductCategory->id_product = filter_input(INPUT_GET, 'id_product', FILTER_VALIDATE_INT);
            $this->dataProductCategory->id_category = filter_input(INPUT_GET, 'id_category', FILTER_VALIDATE_INT);

        }        
        

        if( 1 && $this->dataProductCategory->id_product > 0 && $this->dataProductCategory->id_category > 0 ){
            $this->dataProductCategory->get();
        }
        else{
            // Do not show tabs when wee create the entry 
            $this->showTabs = 0;
        }
            
            
        if($task == 'edit'){  
            $this->HTMLProductCategory->edit($this->dataProductCategory,  $this->showTabs, $this->message, 'edit');
        }
        else{
            $this->dataProductCategory = new ModelProductCategory();
            $this->HTMLProductCategory->edit($this->dataProductCategory,  $this->showTabs, $this->message, 'childlist');
        }
    }
    
    
    
    /**
     * Delete a row in product_category and display the list of elements
     * @return void
     */
    function del(){
        $this->dataProductCategory->id_product = filter_input(INPUT_POST, 'id_product', FILTER_VALIDATE_INT);
    
        if($this->dataProductCategory->id_product){
            $this->dataProductCategory->del();
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
     * Delete a row in product_category and return a JSON response
     * @return void
     */
    function delHtmx(int $logical=0){
        $this->dataProductCategory->id_product = filter_input(INPUT_GET, 'id_product', FILTER_VALIDATE_INT);
        $this->dataProductCategory->id_category = filter_input(INPUT_GET, 'id_category', FILTER_VALIDATE_INT);

        if( 1  && $this->dataProductCategory->id_product  && $this->dataProductCategory->id_category ){

            $this->dataProductCategory->del();
                
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }
        
        if(count($this->message)){
            //http_response_code(204);
            $this->HTMLProductCategory->message($this->message);
        }
    }
    
    /**
     * Display a list of row from product_category with add and del buttons
     * @return void
     */
    function editlist($textSearch = ''){
        // Display items list for edition
         
        $nbItems = $this->dataProductCategory->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLProductCategory->editList($this->dataProductCategory->list ,  $this->orderBy, $this->revertOrder, $this->message);
    
        $orderGet = 'orderBy='.$this->orderBy.'&';    
        $orderGet .= 'order='.$this->order;    
    
        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLProductCategory->pagination($nbPages, $this->page, "?component=productcategory&task=editlist&".$this->filtersGet."&".$orderGet."&");
    }
    
    
    /**
     * Display tabs above the edit form if the element have data from others tables
     * @return void
     */
    function childlist(){
        $this->edit('childlist');
        $this->dataProductCategory->getList(0 , 0, $this->filters, $this->orderBy, strtoupper($this->order));
        $this->HTMLProductCategory->childList($this->dataProductCategory->list ,  $this->filters, $this->orderBy, $this->revertOrder);
    }    
    
       
    /**
     * Display element details
     * @return void
     */
    function view(){
        // Display selected trainings
        $this->dataProductCategory->id_product = filter_input(INPUT_GET, 'id_product', FILTER_VALIDATE_INT);
        $this->dataProductCategory->get();
        $this->HTMLProductCategory->view($this->dataProductCategory,  $this->message);
    } 
    

    /**
     * Display a list of row from product_category with only the view button 
     * For consultation without editing
     * @return void
     */
    function viewList($textSearch = ''){
        // Display items list 
         

        
        $nbItems = $this->dataProductCategory->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);
               
        $this->HTMLProductCategory->viewList($this->dataProductCategory->list,  $this->orderBy, $this->revertOrder, $this->message);
    
        $orderGet = 'orderBy='.$this->orderBy.'&';    
        $orderGet .= 'order='.$this->order;      
       
        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLProductCategory->pagination($nbPages, $this->page, "?component=productcategory&task=viewlist&".$this->filtersGet."&".$orderGet."&");
    }
    
        
    /**
     * Set the order and filter private variable depending of the browser request
     * @return void
     */
    protected function setOrderAndFilters($defaultOrderBy = '', $defaultOrder = 'asc'):void{
        // FK Filter(s)
        if(isset($_GET['filters']['id_product'])){
            $this->filters['id_product'] = intval($_GET['filters']['id_product']);
        }      
        
        if(isset($_GET['filters']['id_category'])){
            $this->filters['id_category'] = intval($_GET['filters']['id_category']);
        }      
        
        if(isset($_GET['field_search'])){
            $this->filters[$_GET['field_search']] = $_GET['field_search_value'];
        }        
        
        parent::setOrderAndFilters('id_product', 'desc');
    }
    
    
    
}
