<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
  
declare(strict_types=1);
namespace Apgenic\ProductVariant;


class ControllerProductVariant  extends \Apgenic\Classes\Controller {
    
    private ViewProductVariant $HTMLProductVariant;
    private ModelProductVariant $dataProductVariant;
    
    // Accepted sort fields
    public static array $fieldsNames = array('id_product_variant','name','created_at','updated_at','deleted_at');
    
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
        $this->HTMLProductVariant = new ViewProductVariant();
        $this->dataProductVariant = new ModelProductVariant(); 
        
        
       
        // Task to execute
        $task = filter_input(INPUT_GET, 'task', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'editlist'; ;
        
        $this->setOrderAndFilters();
        $this->HTMLProductVariant->filters = $this->filters; 
        $this->HTMLProductVariant->filtersGet = $this->filtersGet; 
        
        
        $textSearch = filter_input(INPUT_GET, 'text_search', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            $this->HTMLProductVariant->header('Product variant');
            if($task == 'viewlist' || $task == 'editlist' || $task == 'trashedlist'){
                $this->HTMLProductVariant->search(self::$fieldsNames, $task);
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
            echo '</div>';
            $this->HTMLProductVariant->footer();
        }
    
    }
    
    /**
     * Edit or add a row in product_variant
     * @return void
     */
    function edit(string $task='edit'){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            $this->dataProductVariant->id_product_variant = filter_input(INPUT_POST, 'id_product_variant', FILTER_VALIDATE_INT);
            if(filter_input(INPUT_POST, 'name', FILTER_UNSAFE_RAW)){

                $this->dataProductVariant->name = (string)filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);

                $this->dataProductVariant->save();
                $this->message['type'] = 'success';
                $this->message['text'] = 'The entry has been saved';
            }
        
            else{
                $this->message['type'] = 'danger';
                $this->message['text'] = 'Fill all mandatory fields';
            }                        
        }        
        
        else{
            $this->dataProductVariant->id_product_variant = filter_input(INPUT_GET, 'id_product_variant', FILTER_VALIDATE_INT);

        }        
        

        if( 1 && $this->dataProductVariant->id_product_variant > 0 ){
            $this->dataProductVariant->get();
        }
        else{
            // Do not show tabs when wee create the entry 
            $this->showTabs = 0;
        }
            
            
        if($task == 'edit'){  
            $this->HTMLProductVariant->edit($this->dataProductVariant,  $this->showTabs, $this->message, 'edit');
        }
        else{
            $this->dataProductVariant = new ModelProductVariant();
            $this->HTMLProductVariant->edit($this->dataProductVariant,  $this->showTabs, $this->message, 'childlist');
        }
    }
    
    
    
    /**
     * Delete a row in product_variant and display the list of elements
     * @return void
     */
    function del(){
        $this->dataProductVariant->id_product_variant = filter_input(INPUT_POST, 'id_product_variant', FILTER_VALIDATE_INT);
    
        if($this->dataProductVariant->id_product_variant){
            $this->dataProductVariant->del();
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
     * Delete a row in product_variant and return a JSON response
     * @return void
     */
    function delHtmx(int $logical=0){
        $this->dataProductVariant->id_product_variant = filter_input(INPUT_GET, 'id_product_variant', FILTER_VALIDATE_INT);

        if( 1  && $this->dataProductVariant->id_product_variant ){

            if($logical == 1){
                $this->dataProductVariant->logicalDel(1);
            }
            else{
                $this->dataProductVariant->del();
            }    
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }
        
        if(count($this->message)){
            //http_response_code(204);
            $this->HTMLProductVariant->message($this->message);
        }
    }
    
    /**
     * Display a list of row from product_variant with add and del buttons
     * @return void
     */
    function editlist($textSearch = ''){
        // Display items list for edition
        $this->filters['deleted_at'] = NULL; 
        $nbItems = $this->dataProductVariant->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLProductVariant->editList($this->dataProductVariant->list ,  $this->orderBy, $this->revertOrder, $this->message);
    
        $orderGet = 'orderBy='.$this->orderBy.'&';    
        $orderGet .= 'order='.$this->order;    
    
        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLProductVariant->pagination($nbPages, $this->page, "?component=productvariant&task=editlist&".$this->filtersGet."&".$orderGet."&");
    }
    
    
    /**
     * Display tabs above the edit form if the element have data from others tables
     * @return void
     */
    function childlist(){
        $this->edit('childlist');
        $this->dataProductVariant->getList(0 , 0, $this->filters, $this->orderBy, strtoupper($this->order));
        $this->HTMLProductVariant->childList($this->dataProductVariant->list ,  $this->filters, $this->orderBy, $this->revertOrder);
    }    
    
       
    /**
     * Display element details
     * @return void
     */
    function view(){
        // Display selected trainings
        $this->dataProductVariant->id_product_variant = filter_input(INPUT_GET, 'id_product_variant', FILTER_VALIDATE_INT);
        $this->dataProductVariant->get();
        $this->HTMLProductVariant->view($this->dataProductVariant,  $this->message);
    } 
    

    /**
     * Display a list of row from product_variant with only the view button 
     * For consultation without editing
     * @return void
     */
    function viewList($textSearch = ''){
        // Display items list 
        $this->filters['deleted_at'] = NULL; 

        
        $nbItems = $this->dataProductVariant->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);
               
        $this->HTMLProductVariant->viewList($this->dataProductVariant->list,  $this->orderBy, $this->revertOrder, $this->message);
    
        $orderGet = 'orderBy='.$this->orderBy.'&';    
        $orderGet .= 'order='.$this->order;      
       
        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLProductVariant->pagination($nbPages, $this->page, "?component=productvariant&task=viewlist&".$this->filtersGet."&".$orderGet."&");
    }
    
         
    /**
     * Display a list of row from product_variant with only the view button 
     * For consultation without editing
     * @return void
     */
    function trashedList($textSearch = ''){
        // Display items list         
        $nbItems = $this->dataProductVariant->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , 
                                                                 $this->itemsByPage, array('deleted_at' => 1), 
                                                                 $this->orderBy, 
                                                                 strtoupper($this->order), 
                                                                 $textSearch);
               
        $this->HTMLProductVariant->trashedList($this->dataProductVariant->list,  $this->orderBy, $this->revertOrder, $this->message);
    
        $orderGet = 'orderBy='.$this->orderBy.'&';    
        $orderGet .= 'order='.$this->order;      
       
        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLProductVariant->pagination($nbPages, $this->page, "?component=productvariant&task=trashedlist&".$this->filtersGet."&".$orderGet."&");
    }    
    
    
    /**
     * UnDelete a row in product_variant and display the list of elements
     * @return void
     */
    function undelHTMX(){
        $this->dataProductVariant->id_product_variant = filter_input(INPUT_GET, 'id_product_variant', FILTER_VALIDATE_INT);
    
        if($this->dataProductVariant->id_product_variant){
            $this->dataProductVariant->logicalUnDel();
            $this->message['type'] = 'success';
            $this->message['text'] = 'Done';
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }
        if(count($this->message)){
            //http_response_code(204);
            //$this->HTMLProductVariant->message($this->message);
        }
    }
    
    
    /**
     * Delete a row in product_variant and display the list of elements
     * @return void
     */
    function logicaldeleteHtmx(){
        $this->dataProductVariant->id_product_variant = filter_input(INPUT_GET, 'id_product_variant', FILTER_VALIDATE_INT);
    
        if($this->dataProductVariant->id_product_variant){
            $this->dataProductVariant->logicalDel();
            $this->message['type'] = 'success';
            $this->message['text'] = 'Done';
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }
        if(count($this->message)){
            //http_response_code(204);
            //$this->HTMLProductVariant->message($this->message);
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
        
        parent::setOrderAndFilters('id_product_variant', 'desc');
    }
    
    
    
}
