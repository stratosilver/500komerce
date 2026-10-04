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
    
    // Accepted sort fields
    public static array $fieldsNames = array('id_media','filename','mime_type','size_bytes','width','height','alt_text','caption','created_at','updated_at','deleted_at');
    
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
        $this->HTMLMedia = new ViewMedia();
        $this->dataMedia = new ModelMedia(); 
        
        
       
        // Task to execute
        $task = filter_input(INPUT_GET, 'task', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'editlist'; ;
        
        $this->setOrderAndFilters();
        $this->HTMLMedia->filters = $this->filters; 
        $this->HTMLMedia->filtersGet = $this->filtersGet; 
        
        
        $textSearch = filter_input(INPUT_GET, 'text_search', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            $this->HTMLMedia->header('Media');
            if($task == 'viewlist' || $task == 'editlist' || $task == 'trashedlist'){
                $this->HTMLMedia->search(self::$fieldsNames, $task);
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
            $this->HTMLMedia->footer();
        }
    
    }
    
    /**
     * Edit or add a row in media
     * @return void
     */
    function edit(string $task='edit'){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            $this->dataMedia->id_media = filter_input(INPUT_POST, 'id_media', FILTER_VALIDATE_INT);
            if(filter_input(INPUT_POST, 'filename', FILTER_UNSAFE_RAW) && 
                filter_input(INPUT_POST, 'mime_type', FILTER_UNSAFE_RAW)){

                $this->dataMedia->filename = (string)filter_input(INPUT_POST, 'filename', FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataMedia->mime_type = (string)filter_input(INPUT_POST, 'mime_type', FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataMedia->size_bytes = $_POST['size_bytes'] == '' ? null : (int)filter_input(INPUT_POST, 'size_bytes', FILTER_SANITIZE_NUMBER_INT);
                $this->dataMedia->width = $_POST['width'] == '' ? null : (int)filter_input(INPUT_POST, 'width', FILTER_SANITIZE_NUMBER_INT);
                $this->dataMedia->height = $_POST['height'] == '' ? null : (int)filter_input(INPUT_POST, 'height', FILTER_SANITIZE_NUMBER_INT);
                $this->dataMedia->alt_text = (string)filter_input(INPUT_POST, 'alt_text', FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataMedia->caption = (string)filter_input(INPUT_POST, 'caption', FILTER_SANITIZE_SPECIAL_CHARS);

                $this->dataMedia->save();
                $this->message['type'] = 'success';
                $this->message['text'] = 'The entry has been saved';
            }
        
            else{
                $this->message['type'] = 'danger';
                $this->message['text'] = 'Fill all mandatory fields';
            }                        
        }        
        
        else{
            $this->dataMedia->id_media = filter_input(INPUT_GET, 'id_media', FILTER_VALIDATE_INT);

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
        $this->dataMedia->id_media = filter_input(INPUT_POST, 'id_media', FILTER_VALIDATE_INT);
    
        if($this->dataMedia->id_media){
            $this->dataMedia->del();
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
        $this->dataMedia->id_media = filter_input(INPUT_GET, 'id_media', FILTER_VALIDATE_INT);

        if( 1  && $this->dataMedia->id_media ){

            if($logical == 1){
                $this->dataMedia->logicalDel(1);
            }
            else{
                $this->dataMedia->del();
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
        $this->dataMedia->id_media = filter_input(INPUT_GET, 'id_media', FILTER_VALIDATE_INT);
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
        $this->dataMedia->id_media = filter_input(INPUT_GET, 'id_media', FILTER_VALIDATE_INT);
    
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
        $this->dataMedia->id_media = filter_input(INPUT_GET, 'id_media', FILTER_VALIDATE_INT);
    
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
    
    
    
}
