<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

declare(strict_types=1);
namespace Apgenic\Translation;


class ControllerTranslation  extends \Apgenic\Classes\Controller {

    private ViewTranslation $HTMLTranslation;
    private ModelTranslation $dataTranslation;

    // Accepted sort fields
    public static array $fieldsNames = array('id_translation','text_key','lang','text','created_at','updated_at','deleted_at');

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
        $this->HTMLTranslation = new ViewTranslation();
        $this->dataTranslation = new ModelTranslation();



        // Task to execute
        $task = filter_var($_GET['task'] ?? 'editlist', FILTER_SANITIZE_SPECIAL_CHARS); ;

        $this->setOrderAndFilters();
        $this->HTMLTranslation->filters = $this->filters;
        $this->HTMLTranslation->filtersGet = $this->filtersGet;


        $textSearch = filter_var($_GET['text_search'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            $this->HTMLTranslation->header('Translation');
            if($task == 'viewlist' || $task == 'editlist' || $task == 'trashedlist'){
                $this->HTMLTranslation->search(self::$fieldsNames, $task);
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
            $this->HTMLTranslation->footer();
        }

    }

    /**
     * Edit or add a row in translation
     * @return void
     */
    function edit(string $task='edit'){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $this->dataTranslation->id_translation = filter_var($_POST['id_translation'] ?? null, FILTER_VALIDATE_INT);
            if(filter_var($_POST['text_key'] ?? null, FILTER_UNSAFE_RAW) &&
                filter_var($_POST['lang'] ?? null, FILTER_UNSAFE_RAW) &&
                filter_var($_POST['text'] ?? null, FILTER_UNSAFE_RAW)){

                $this->dataTranslation->text_key = (string)filter_var($_POST['text_key'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataTranslation->lang = (string)filter_var($_POST['lang'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataTranslation->text = (string)filter_var($_POST['text'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);

                $this->dataTranslation->save();
                $this->message['type'] = 'success';
                $this->message['text'] = 'The entry has been saved';
            }

            else{
                $this->message['type'] = 'danger';
                $this->message['text'] = 'Fill all mandatory fields';
            }
        }

        else{
            $this->dataTranslation->id_translation = filter_var($_GET['id_translation'] ?? null, FILTER_VALIDATE_INT);

        }


        if( 1 && $this->dataTranslation->id_translation > 0 ){
            $this->dataTranslation->get();
        }
        else{
            // Do not show tabs when wee create the entry
            $this->showTabs = 0;
        }


        if($task == 'edit'){
            $this->HTMLTranslation->edit($this->dataTranslation,  $this->showTabs, $this->message, 'edit');
        }
        else{
            $this->dataTranslation = new ModelTranslation();
            $this->HTMLTranslation->edit($this->dataTranslation,  $this->showTabs, $this->message, 'childlist');
        }
    }



    /**
     * Delete a row in translation and display the list of elements
     * @return void
     */
    function del(){
        $this->dataTranslation->id_translation = filter_var($_POST['id_translation'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataTranslation->id_translation){
            $this->dataTranslation->del();
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
     * Delete a row in translation and return a JSON response
     * @return void
     */
    function delHtmx(int $logical=0){
        $this->dataTranslation->id_translation = filter_var($_GET['id_translation'] ?? null, FILTER_VALIDATE_INT);

        if( 1  && $this->dataTranslation->id_translation ){

            if($logical == 1){
                $this->dataTranslation->logicalDel(1);
            }
            else{
                $this->dataTranslation->del();
            }
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }

        if(count($this->message)){
            //http_response_code(204);
            $this->HTMLTranslation->message($this->message);
        }
    }

    /**
     * Display a list of row from translation with add and del buttons
     * @return void
     */
    function editlist($textSearch = ''){
        // Display items list for edition
        $this->filters['deleted_at'] = NULL;
        $nbItems = $this->dataTranslation->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLTranslation->editList($this->dataTranslation->list ,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLTranslation->pagination($nbPages, $this->page, "?component=translation&task=editlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * Display tabs above the edit form if the element have data from others tables
     * @return void
     */
    function childlist(){
        $this->edit('childlist');
        $this->dataTranslation->getList(0 , 0, $this->filters, $this->orderBy, strtoupper($this->order));
        $this->HTMLTranslation->childList($this->dataTranslation->list ,  $this->filters, $this->orderBy, $this->revertOrder);
    }


    /**
     * Display element details
     * @return void
     */
    function view(){
        // Display selected trainings
        $this->dataTranslation->id_translation = filter_var($_GET['id_translation'] ?? null, FILTER_VALIDATE_INT);
        $this->dataTranslation->get();
        $this->HTMLTranslation->view($this->dataTranslation,  $this->message);
    }


    /**
     * Display a list of row from translation with only the view button
     * For consultation without editing
     * @return void
     */
    function viewList($textSearch = ''){
        // Display items list
        $this->filters['deleted_at'] = NULL;


        $nbItems = $this->dataTranslation->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLTranslation->viewList($this->dataTranslation->list,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLTranslation->pagination($nbPages, $this->page, "?component=translation&task=viewlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * Display a list of row from translation with only the view button
     * For consultation without editing
     * @return void
     */
    function trashedList($textSearch = ''){
        // Display items list
        $nbItems = $this->dataTranslation->getList(($this->page * $this->itemsByPage)-$this->itemsByPage ,
                                                                 $this->itemsByPage, array('deleted_at' => 1),
                                                                 $this->orderBy,
                                                                 strtoupper($this->order),
                                                                 $textSearch);

        $this->HTMLTranslation->trashedList($this->dataTranslation->list,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLTranslation->pagination($nbPages, $this->page, "?component=translation&task=trashedlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * UnDelete a row in translation and display the list of elements
     * @return void
     */
    function undelHTMX(){
        $this->dataTranslation->id_translation = filter_var($_GET['id_translation'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataTranslation->id_translation){
            $this->dataTranslation->logicalUnDel();
            $this->message['type'] = 'success';
            $this->message['text'] = 'Done';
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }
        if(count($this->message)){
            //http_response_code(204);
            //$this->HTMLTranslation->message($this->message);
        }
    }


    /**
     * Delete a row in translation and display the list of elements
     * @return void
     */
    function logicaldeleteHtmx(){
        $this->dataTranslation->id_translation = filter_var($_GET['id_translation'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataTranslation->id_translation){
            $this->dataTranslation->logicalDel();
            $this->message['type'] = 'success';
            $this->message['text'] = 'Done';
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = 'Missing parameter';
        }
        if(count($this->message)){
            //http_response_code(204);
            //$this->HTMLTranslation->message($this->message);
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

        parent::setOrderAndFilters('id_translation', 'desc');
    }



}
