<?php
/**
 * Blog of the shop: the articles written in the back office (component post).
 *
 * Tasks
 *   viewlist  the published articles of the language of the page, 9 by page (parameter page)   public
 *   view      one article (parameter slug, or id_post), with other articles under it          public
 * The users who can edit also see the drafts and the archived articles (preview), never in the list.
 * The rights are in \Apgenic\Classes\Auth (PUBLIC_TASKS), checked by index.php.
 */

declare(strict_types=1);
namespace Apgenic\Blog;

use Apgenic\Classes\Auth;

class ControllerBlog {

    private ViewBlog $HTMLBlog;
    private ModelBlog $dataBlog;


    function render(){
        $this->HTMLBlog = new ViewBlog();
        $this->dataBlog = new ModelBlog();

        $task = (string)filter_var($_GET['task'] ?? 'viewlist', FILTER_SANITIZE_SPECIAL_CHARS);
        switch($task){
            case 'viewlist': $this->viewList(); break;
            case 'view':     $this->view(); break;
            default: throw new \ErrorException(T_PAGE_NOT_FOUND, 404, E_ERROR);
        }
    }


    /**
     * List of the articles
     * @return void
     */
    private function viewList():void{
        $count = $this->dataBlog->count(LANG);
        $pages = max(1, (int)ceil($count / ModelBlog::PER_PAGE));
        $page = min($pages, max(1, (int)filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, array('options' => array('default' => 1)))));

        $this->HTMLBlog->header(T_BLOG);
        $this->HTMLBlog->viewList($this->dataBlog->getList(LANG, $page), $page == 1);
        $this->HTMLBlog->pagination($pages, $page, BASE_URL.'/index.php?component=blog&');
        $this->HTMLBlog->footer();
    }


    /**
     * One article
     * @return void
     */
    private function view():void{
        // The slugs are stored html-escaped by the forms: the one of the address is escaped the same way
        $slug = (string)filter_var($_GET['slug'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $idPost = (int)filter_var($_GET['id_post'] ?? 0, FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
        $preview = Auth::canEdit();

        $post = null;
        if($slug != ''){
            $post = $this->dataBlog->getBySlug($slug, LANG, $preview);
        }
        elseif($idPost > 0){
            $post = $this->dataBlog->getById($idPost, $preview);
        }

        if($post === null){
            http_response_code(404);
            $this->HTMLBlog->header(T_POST_NOT_FOUND);
            $this->HTMLBlog->notFound();
            $this->HTMLBlog->footer();
            return;
        }

        // The title is stored html-escaped: displayed as it is
        $this->HTMLBlog->header((string)$post['title']);
        $this->HTMLBlog->view($post, $this->dataBlog->getOthers((string)$post['lang'], (int)$post['id_post']));
        $this->HTMLBlog->footer();
    }
}
