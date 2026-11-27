<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

declare(strict_types=1);
namespace Apgenic\Post;


class ControllerPost  extends \Apgenic\Classes\Controller {

    private ViewPost $HTMLPost;
    private ModelPost $dataPost;

    // Accepted sort fields
    public static array $fieldsNames = array('id_post','id_user','lang','title','id_media','slug','excerpt','body','status','published_at','created_at','updated_at','deleted_at');

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

        $this->HTMLPost = new ViewPost($oModelUser->list);
        $this->dataPost = new ModelPost();



        // Task to execute
        $task = filter_var($_GET['task'] ?? 'editlist', FILTER_SANITIZE_SPECIAL_CHARS); ;

        $this->setOrderAndFilters();
        $this->HTMLPost->filters = $this->filters;
        $this->HTMLPost->filtersGet = $this->filtersGet;


        $textSearch = filter_var($_GET['text_search'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            $this->HTMLPost->header(T_COMPONENT_POST);
            if($task == 'viewlist' || $task == 'editlist' || $task == 'trashedlist'){
                $this->HTMLPost->search(self::$fieldsNames, $task);
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


                default: throw new \ErrorException(T_PAGE_NOT_FOUND, 404, E_ERROR);
            }
        }
        catch(Exception $e){
            $this->message['type'] = 'danger';
            $this->message['text'] = $e->getMessage();
        }

        if($_SERVER['PHP_SELF'] != '/htmx.php'){
            //echo '</div>';
            $this->HTMLPost->footer();
        }

    }

    /**
     * Edit or add a row in post
     * @return void
     */
    function edit(string $task='edit'){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $this->dataPost->id_post = filter_var($_POST['id_post'] ?? null, FILTER_VALIDATE_INT);
            $isNew = !($this->dataPost->id_post > 0);
            // The slug is not required: it is made from the title when it is empty
            if(filter_var($_POST['lang'] ?? null, FILTER_UNSAFE_RAW) &&
                filter_var($_POST['title'] ?? null, FILTER_UNSAFE_RAW) &&
                filter_var($_POST['body'] ?? null, FILTER_UNSAFE_RAW)){

                // The author: the logged user, never a value of the form
                $this->dataPost->id_user = $this->postUser((int)$this->dataPost->id_post);
                // A code of language (fr, en, nl: column of 3 letters), the default language otherwise. The list of
                // the form has the languages of the site, and the one of the article when it is another one
                $lang = (string)($_POST['lang'] ?? '');
                $this->dataPost->lang = preg_match('/^[a-z]{2,3}$/', $lang) ? $lang : LANG_DEFAULT;
                $this->dataPost->title = (string)filter_var($_POST['title'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataPost->id_media = ($_POST['id_media'] ?? '') == '' ? null : (int)filter_var($_POST['id_media'] ?? null, FILTER_SANITIZE_NUMBER_INT);
                // Slug: the one typed (cleaned), or the one of the title; never the one of another article of the language
                $idPost = (int)$this->dataPost->id_post;
                $slug = \Apgenic\Classes\Url::slug((string)($_POST['slug'] ?? '')) ?: \Apgenic\Classes\Url::slug($this->dataPost->title) ?: 'article';
                $this->dataPost->slug = self::uniqueSlug($slug, fn(string $s): bool => $this->dataPost->slugTaken($s, $this->dataPost->lang, $idPost));
                $this->dataPost->excerpt = (string)filter_var($_POST['excerpt'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                $this->dataPost->body = (string)filter_var($_POST['body'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
                $status = (string)($_POST['status'] ?? '');
                $this->dataPost->status = in_array($status, array('draft', 'published', 'archived'), true) ? $status : 'draft';
                // Date of publication: today at 00:00 for a new article without date
                $this->dataPost->published_at = self::publishedAt((string)($_POST['published_at'] ?? ''), $isNew);

                $this->dataPost->save();
                $this->message['type'] = 'success';
                $this->message['text'] = T_MSG_SAVED;
            }

            else{
                $this->message['type'] = 'danger';
                $this->message['text'] = T_MSG_FILL_MANDATORY;
            }
        }

        else{
            $this->dataPost->id_post = filter_var($_GET['id_post'] ?? null, FILTER_VALIDATE_INT);

        }


        if( 1 && $this->dataPost->id_post > 0 ){
            $this->dataPost->get();
        }
        else{
            // Do not show tabs when wee create the entry
            $this->showTabs = 0;
            // New article: written by the logged user, in the default language, a draft published today at 00:00
            $this->dataPost->id_user = $this->postUser(0);
            if((string)$this->dataPost->lang === ''){
                $this->dataPost->lang = LANG_DEFAULT;
            }
            if($this->dataPost->published_at === null || $this->dataPost->published_at === ''){
                $this->dataPost->published_at = ModelPost::today().' 00:00:00';
            }
        }


        if($task == 'edit'){
            $this->HTMLPost->edit($this->dataPost,  $this->showTabs, $this->message, 'edit');
        }
        else{
            $this->dataPost = new ModelPost();
            $this->HTMLPost->edit($this->dataPost,  $this->showTabs, $this->message, 'childlist');
        }
    }



    /**
     * Author of an article: the logged user is the one of a new article. An article that already has an author
     * keeps it (like the products, ControllerProduct::productUser()).
     * @param int $idPost 0 for a new article
     * @return int|null
     */
    private function postUser(int $idPost):?int{
        if($idPost > 0){
            $post = new ModelPost();
            $post->id_post = $idPost;
            if($post->get() && (int)$post->id_user > 0){
                return (int)$post->id_user;
            }
        }
        $logged = (int)($_SESSION['user']['id_user'] ?? 0);
        return $logged > 0 ? $logged : null;
    }



    /**
     * Delete a row in post and display the list of elements
     * @return void
     */
    function del(){
        $this->dataPost->id_post = filter_var($_POST['id_post'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataPost->id_post){
            $this->dataPost->del();
            $this->message['type'] = 'success';
            $this->message['text'] = T_MSG_DONE;
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = T_MSG_MISSING_PARAMETER;
        }

        $this->editlist();
    }



    /**
     * Delete a row in post and return a JSON response
     * @return void
     */
    function delHtmx(int $logical=0){
        $this->dataPost->id_post = filter_var($_GET['id_post'] ?? null, FILTER_VALIDATE_INT);

        if( 1  && $this->dataPost->id_post ){

            if($logical == 1){
                $this->dataPost->logicalDel(1);
            }
            else{
                $this->dataPost->del();
            }
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = T_MSG_MISSING_PARAMETER;
        }

        if(count($this->message)){
            //http_response_code(204);
            $this->HTMLPost->message($this->message);
        }
    }

    /**
     * Display a list of row from post with add and del buttons
     * @return void
     */
    function editlist($textSearch = ''){
        // Display items list for edition
        $this->filters['deleted_at'] = 0; // not the rows of the trash (only trashedlist shows them)
        $nbItems = $this->dataPost->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLPost->editList($this->dataPost->list ,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLPost->pagination($nbPages, $this->page, "?component=post&task=editlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * Display tabs above the edit form if the element have data from others tables
     * @return void
     */
    function childlist(){
        $this->edit('childlist');
        // Not the rows of the trash (0 = not deleted); the filters of the tab stay as they are for the view
        $this->dataPost->getList(0 , 0, array_merge($this->filters, array('deleted_at' => 0)), $this->orderBy, strtoupper($this->order));
        $this->HTMLPost->childList($this->dataPost->list ,  $this->filters, $this->orderBy, $this->revertOrder);
    }


    /**
     * Display element details
     * @return void
     */
    function view(){
        // Display selected trainings
        $this->dataPost->id_post = filter_var($_GET['id_post'] ?? null, FILTER_VALIDATE_INT);
        $this->dataPost->get();
        // The customers only see the published posts
        if(!\Apgenic\Classes\Auth::canEdit() && ($this->dataPost->status != 'published' || $this->dataPost->deleted_at !== null)){
            $this->dataPost = new ModelPost();
        }
        $this->HTMLPost->view($this->dataPost,  $this->message);
    }


    /**
     * Display a list of row from post with only the view button
     * For consultation without editing
     * @return void
     */
    function viewList($textSearch = ''){
        // Display items list
        $this->filters['deleted_at'] = 0; // not the rows of the trash (only trashedlist shows them)
        // The customers only see the published posts
        if(!\Apgenic\Classes\Auth::canEdit()){
            $this->filters['status'] = 'published';
        }


        $nbItems = $this->dataPost->getList(($this->page * $this->itemsByPage)-$this->itemsByPage , $this->itemsByPage, $this->filters, $this->orderBy, strtoupper($this->order), $textSearch);

        $this->HTMLPost->viewList($this->dataPost->list,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLPost->pagination($nbPages, $this->page, "?component=post&task=viewlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * Display a list of row from post with only the view button
     * For consultation without editing
     * @return void
     */
    function trashedList($textSearch = ''){
        // Display items list
        $nbItems = $this->dataPost->getList(($this->page * $this->itemsByPage)-$this->itemsByPage ,
                                                                 $this->itemsByPage, array('deleted_at' => 1),
                                                                 $this->orderBy,
                                                                 strtoupper($this->order),
                                                                 $textSearch);

        $this->HTMLPost->trashedList($this->dataPost->list,  $this->orderBy, $this->revertOrder, $this->message);

        $orderGet = 'orderBy='.$this->orderBy.'&';
        $orderGet .= 'order='.$this->order;

        $nbPages = ceil($nbItems/$this->itemsByPage);
        $this->HTMLPost->pagination($nbPages, $this->page, "?component=post&task=trashedlist&".$this->filtersGet."&".$orderGet."&");
    }


    /**
     * UnDelete a row in post and display the list of elements
     * @return void
     */
    function undelHTMX(){
        $this->dataPost->id_post = filter_var($_GET['id_post'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataPost->id_post){
            $this->dataPost->logicalUnDel();
            $this->message['type'] = 'success';
            $this->message['text'] = T_MSG_DONE;
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = T_MSG_MISSING_PARAMETER;
        }
        if(count($this->message)){
            //http_response_code(204);
            //$this->HTMLPost->message($this->message);
        }
    }


    /**
     * Delete a row in post and display the list of elements
     * @return void
     */
    function logicaldeleteHtmx(){
        $this->dataPost->id_post = filter_var($_GET['id_post'] ?? null, FILTER_VALIDATE_INT);

        if($this->dataPost->id_post){
            $this->dataPost->logicalDel();
            $this->message['type'] = 'success';
            $this->message['text'] = T_MSG_DONE;
        }
        else{
            $this->message['type'] = 'danger';
            $this->message['text'] = T_MSG_MISSING_PARAMETER;
        }
        if(count($this->message)){
            //http_response_code(204);
            //$this->HTMLPost->message($this->message);
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

        parent::setOrderAndFilters('id_post', 'desc');
    }



}
