<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
  
declare(strict_types=1);
namespace Apgenic\Category;


class ControllerCategory  extends \Apgenic\Classes\Controller {
    
    private ViewCategory $HTMLCategory;
    private ModelCategory $dataCategory;
    
    // Accepted sort fields
    public static array $fieldsNames = array('id_category','id_parent','name','slug','position','created_at','updated_at','deleted_at');
    
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

        $oModelCategory = new \Apgenic\Category\ModelCategory();
        $oModelCategory->getList();
  
        $this->HTMLCategory = new ViewCategory($oModelCategory->list);
        $this->dataCategory = new ModelCategory(); 
        
        
       
        // Task to execute
        $task = filter_input(INPUT_GET, 'task', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'editlist'; ;
        
        $this->setOrderAndFilters();
        $this->HTMLCategory->filters = $this->filters; 
        $this->HTMLCategory->filtersGet = $this->filtersGet; 
        
        
        $textSearch = filter_input(INPUT_GET, 'text_search', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            $this->HTMLCategory->header('Category');
            if($task == 'viewlist' || $task == 'editlist' || $task == 'trashedlist'){
                $this->HTMLCategory->search(self::$fieldsNames, $task);
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
            $this->HTMLCategory->footer();
        }
    
    }
    
    /**
     * Edit or add a row in category
     * @return void
     */
    function edit(string $task='edit'){
        // For when the edit is under a tab
        $this->dataCategory->id_parent = (int)($_GET['id_parent'] ?? 0);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            $this->dataCategory->id_category = filter_input(INPUT_POST, 'id_category', FILTER_VALIDATE_INT);
            if(filter_input(INPUT_POST, 'name', FILTER_UNSAFE_RAW) && 
                filter_input(INPUT_POST, 'slug', FILTER_UNSAFE_RAW)){

                $this->dataCategory->id_parent = $_POST['id_parent'] == '' ? null : (int)filter_input(INPUT_POST, 'id_parent', FILTER_SANITIZE_NUMBER_INT);
                $this->dataCategory->name = (string)filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataCategory->slug = (string)filter_input(INPUT_POST, 'slug', FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataCategory->position = (int)filter_input(INPUT_POST, 'position', FILTER_SANITIZE_NUMBER_INT);

                $this->dataCategory->save();
                $this->message['type'] = 'success';
                $this->message['text'] = 'The entry has been saved';
            }
        
            else{
                $this->message['type'] = 'danger';
                $this->message['text'] = 'Fill all mandatory fields';
            }                        
        }        
        
        else{
            $this->dataCategory->id_category = filter_input(INPUT_GET, 'id_category', FILTER_VALIDATE_INT);

        }        
        

        if( 1 && $this->dataCategory->id_category > 0 ){
            $this->dataCategory->get();
        }
        else{
            // Do not show tabs when wee create the entry 
            $this->showTabs = 0;
        }
            
            
        if($task == 'edit'){  
            $this->HTMLCategory->edit($this->dataCategory,  $this->showTabs, $this->message, 'edit');
        }
        else{
            $this->dataCategory = new ModelCategory();
            $this->HTMLCategory->edit($this->dataCategory,  $this->showTabs, $this->message, 'childlist');
        }
    }
    
    
    
    /**
     * Delete a row in category and display the list of elements
     * @return void
     */
    function del(){
        $this->dataCategory->id_category = filter_input(INPUT_POST, 'id_category', FILTER_VALIDATE_INT);
    
        if($this->dataCategory->id_category){
            $this->dataCategory->del();
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
     * Delete a row in category and return a JSON response
     * @return void
     */
    function delHtmx(int $logical=0){
        $this->dataCategory->id_category = filter_input(INPUT_GET, 'id_category', FILTER_VALIDATE_INT);

        if( 1  && $this->dataCategory->id_category ){

            if($logical == 1){
                $this->dataCategory->logicalDel(1);
            }
            else{
                $this->dataCategory->del();
            }    
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }
        
        if(count($this->message)){
            //http_response_code(204);
            $this->HTMLCategory->message($this->message);
        }
    }
    
    /**
     * Display a list of row from category with add and del buttons
     * @return void
     */
    function editlist($textSearch = ''){
        // Display items list for edition
        $this->filters['deleted_at'] = NULL; 
        $nbItems = $this->dataCategory->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLCategory->editList($this->dataCategory->list ,  $this->orderBy, $this->revertOrder, $this->message);
    
        $orderGet = 'orderBy='.$this->orderBy.'&';    
        $orderGet .= 'order='.$this->order;    
    
        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLCategory->pagination($nbPages, $this->page, "?component=category&task=editlist&".$this->filtersGet."&".$orderGet."&");
    }
    
    
    /**
     * Display tabs above the edit form if the element have data from others tables
     * @return void
     */
    function childlist(){
        $this->edit('childlist');
        $this->dataCategory->getList(0 , 0, $this->filters, $this->orderBy, strtoupper($this->order));
        $this->HTMLCategory->childList($this->dataCategory->list ,  $this->filters, $this->orderBy, $this->revertOrder);
    }    
    
       
    /**
     * Display element details
     * @return void
     */
    function view(){
        // Display selected trainings
        $this->dataCategory->id_category = filter_input(INPUT_GET, 'id_category', FILTER_VALIDATE_INT);
        $this->dataCategory->get();
        $this->HTMLCategory->view($this->dataCategory,  $this->message);
    } 
    

    /**
     * Display a list of row from category with only the view button 
     * For consultation without editing
     * @return void
     */
    function viewList($textSearch = ''){
        // Display items list 
        $this->filters['deleted_at'] = NULL; 

        
        $nbItems = $this->dataCategory->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);
               
        $this->HTMLCategory->viewList($this->dataCategory->list,  $this->orderBy, $this->revertOrder, $this->message);
    
        $orderGet = 'orderBy='.$this->orderBy.'&';    
        $orderGet .= 'order='.$this->order;      
       
        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLCategory->pagination($nbPages, $this->page, "?component=category&task=viewlist&".$this->filtersGet."&".$orderGet."&");
    }
    
         
    /**
     * Display a list of row from category with only the view button 
     * For consultation without editing
     * @return void
     */
    function trashedList($textSearch = ''){
        // Display items list         
        $nbItems = $this->dataCategory->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , 
                                                                 $this->itemsByPage, array('deleted_at' => 1), 
                                                                 $this->orderBy, 
                                                                 strtoupper($this->order), 
                                                                 $textSearch);
               
        $this->HTMLCategory->trashedList($this->dataCategory->list,  $this->orderBy, $this->revertOrder, $this->message);
    
        $orderGet = 'orderBy='.$this->orderBy.'&';    
        $orderGet .= 'order='.$this->order;      
       
        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLCategory->pagination($nbPages, $this->page, "?component=category&task=trashedlist&".$this->filtersGet."&".$orderGet."&");
    }    
    
    
    /**
     * UnDelete a row in category and display the list of elements
     * @return void
     */
    function undelHTMX(){
        $this->dataCategory->id_category = filter_input(INPUT_GET, 'id_category', FILTER_VALIDATE_INT);
    
        if($this->dataCategory->id_category){
            $this->dataCategory->logicalUnDel();
            $this->message['type'] = 'success';
            $this->message['text'] = 'Done';
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }
        if(count($this->message)){
            //http_response_code(204);
            //$this->HTMLCategory->message($this->message);
        }
    }
    
    
    /**
     * Delete a row in category and display the list of elements
     * @return void
     */
    function logicaldeleteHtmx(){
        $this->dataCategory->id_category = filter_input(INPUT_GET, 'id_category', FILTER_VALIDATE_INT);
    
        if($this->dataCategory->id_category){
            $this->dataCategory->logicalDel();
            $this->message['type'] = 'success';
            $this->message['text'] = 'Done';
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }
        if(count($this->message)){
            //http_response_code(204);
            //$this->HTMLCategory->message($this->message);
        }
    }    
        
    
    
    /**
     * Set the order and filter private variable depending of the browser request
     * @return void
     */
    protected function setOrderAndFilters($defaultOrderBy = '', $defaultOrder = 'asc'):void{
        // FK Filter(s)
        if(isset($_GET['filters']['id_parent'])){
            $this->filters['id_parent'] = intval($_GET['filters']['id_parent']);
        }      
        
        if(isset($_GET['field_search'])){
            $this->filters[$_GET['field_search']] = $_GET['field_search_value'];
        }        
        
        parent::setOrderAndFilters('id_category', 'desc');
    }
    
    
    
}
