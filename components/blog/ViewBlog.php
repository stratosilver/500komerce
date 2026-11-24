<?php
/**
 * Pages of the blog: list of the articles, page of an article.
 * Bootstrap 5 classes only, like the rest of the site (see _dev/design-system.md).
 * The texts coming from the database are stored html-escaped and displayed as they are, like in the other views.
 */

declare(strict_types=1);
namespace Apgenic\Blog;

use Apgenic\Classes\Auth;

class ViewBlog extends \Apgenic\Classes\ViewTemplate
{
    // No "Edit list / View list / +" of the back office on the pages of the blog
    protected bool $listLinks = false;

    /** Words read in a minute, for the reading time */
    const WORDS_PER_MINUTE = 200;


    /**
     * Header of the pages of the blog: banner above the list, path above an article
     * @return void
     */
    function mainHeader($title){
        $task = (string)($_GET['task'] ?? 'viewlist');
        echo '<main id="main" class="flex-grow-1 py-4 py-lg-5"><div class="container">';

        if($task == 'viewlist'){
            ?>
            <div class="bg-primary text-white rounded-4 shadow p-4 p-lg-5 mb-4 mb-lg-5">
                <div class="small text-uppercase fw-semibold text-warning mb-2"><?=T_BLOG?></div>
                <h1 class="display-6 fw-bold mb-2"><?=T_BLOG_TITLE?></h1>
                <p class="col-lg-8 text-white text-opacity-75 mb-0"><?=T_BLOG_INTRO?></p>
            </div>
            <?php
        }
        else{
            // The title of the article is displayed by view()
            ?>
            <nav aria-label="<?=T_BREADCRUMB?>">
                <ol class="breadcrumb small mb-4">
                    <li class="breadcrumb-item"><a class="link-secondary link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('product', 'viewlist')?>"><?=T_HOME?></a></li>
                    <li class="breadcrumb-item"><a class="link-secondary link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::listUrl()?>"><?=T_BLOG?></a></li>
                    <li class="breadcrumb-item active text-truncate" aria-current="page"><?=$title?></li>
                </ol>
            </nav>
            <?php
        }
        echo '<div id="main_content">';
    }


    // Helpers
    // ------------------------------------------------------------------------------------------------

    /**
     * Url of the list of the articles
     * @return string
     */
    public static function listUrl():string{
        return self::url('blog', 'viewlist');
    }


    /**
     * Url of an article: by its slug, by its id when it has none
     * @param array $post row of ModelBlog
     * @return string
     */
    public static function postUrl(array $post):string{
        return self::url('blog', 'view', array('slug' => html_entity_decode((string)$post['slug'], ENT_QUOTES), 'id_post' => (int)$post['id_post']));
    }


    /**
     * Url of the image of an article: the copy of the asked size, else the original file, else another size
     * @param string $size one of the sizes of \Apgenic\Media\MediaImage::SIZES
     * @return string '' when the article has no image file
     */
    public static function imageUrl($filename, string $size = 'large'):string{
        $filename = (string)$filename;
        if($filename == '' || !preg_match('/^[A-Za-z0-9._-]+$/', $filename)){
            return '';
        }
        $dir = \Apgenic\Media\MediaImage::dir().DIRECTORY_SEPARATOR;
        $sizes = array_reverse(array_keys(\Apgenic\Media\MediaImage::SIZES));
        foreach(array_merge(array($size, ''), $sizes) as $try){
            $name = $try == '' ? $filename : \Apgenic\Media\MediaImage::variant($filename, $try);
            if(is_file($dir.$name)){
                return \Apgenic\Media\MediaImage::url($filename, $try);
            }
        }
        return '';
    }


    /**
     * Date in words, in the language of the page: 18 août 2026, August 18, 2026
     * @return string
     */
    public static function longDate($datetime):string{
        $time = strtotime((string)$datetime);
        if($time === false){
            return '';
        }
        $months = explode(',', T_MONTHS);
        return sprintf(T_DATE_LONG, (int)date('j', $time), $months[(int)date('n', $time) - 1] ?? '', (int)date('Y', $time));
    }


    /**
     * Text of an article without the html escaping of the forms, ex: to count its words
     * @return string
     */
    private static function plainText(string $html):string{
        return trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }


    /**
     * Reading time in minutes, at least 1
     * @return int
     */
    public static function readingTime(string $body):int{
        $words = preg_split('/\s+/u', self::plainText($body), -1, PREG_SPLIT_NO_EMPTY);
        return max(1, (int)round(count($words ?: array()) / self::WORDS_PER_MINUTE));
    }


    /**
     * Summary of an article: its excerpt, else the beginning of its text
     * @return string html-escaped
     */
    public static function summary(array $post, int $length = 160):string{
        if(trim((string)$post['excerpt']) != ''){
            return (string)$post['excerpt'];
        }
        $text = self::plainText((string)$post['body']);
        if(mb_strlen($text) > $length){
            $text = rtrim(mb_substr($text, 0, $length)).'…';
        }
        return htmlspecialchars($text);
    }


    /**
     * Text of an article as paragraphs: an empty line separates two paragraphs, a line break is kept.
     * The forms store the line breaks escaped (&#13;&#10;): they are turned back into line breaks.
     * @return string html
     */
    public static function paragraphs(string $body):string{
        $text = str_replace(array('&#13;&#10;', '&#10;', '&#13;', "\r\n", "\r"), "\n", $body);
        $parts = preg_split('/\n\s*\n/', trim($text)) ?: array();
        $html = '';
        foreach($parts as $n => $part){
            $html .= '<p'.($n == count($parts) - 1 ? ' class="mb-0"' : '').'>'.nl2br(trim($part)).'</p>';
        }
        return $html;
    }


    /**
     * Name of the author of an article
     * @return string '' when the author is unknown
     */
    public static function author(array $post):string{
        return trim((string)$post['first_name'].' '.(string)$post['last_name']);
    }


    /**
     * Date and reading time of an article
     * @return string html
     */
    private static function meta(array $post):string{
        return '<time datetime="'.date('Y-m-d', strtotime((string)$post['date']) ?: time()).'">'.self::longDate($post['date']).'</time>'
             .' <span aria-hidden="true">&middot;</span> '.sprintf(T_READING_TIME, self::readingTime((string)$post['body']));
    }


    /**
     * Image of an article in a frame of fixed proportions, or a tile with an icon when it has no image
     * @param string $ratio class of Bootstrap: ratio-16x9, ratio-21x9...
     * @return string html
     */
    private static function cover(array $post, string $ratio = 'ratio-16x9', string $size = 'medium', string $class = ''):string{
        $url = self::imageUrl($post['filename'] ?? '', $size);
        $html = '<div class="ratio '.$ratio.' bg-primary bg-gradient '.$class.'">';
        if($url != ''){
            $html .= '<img class="object-fit-cover" src="'.htmlspecialchars($url).'" alt="'.(string)($post['alt_text'] ?? '').'" loading="lazy">';
        }
        else{
            $html .= '<div class="d-flex align-items-center justify-content-center text-warning">'.self::icon('newspaper', 44).'</div>';
        }
        return $html.'</div>';
    }


    /**
     * Card of an article in a list: image, date, title, summary. The whole card is a link.
     * @param string $heading level of the title: h2 in the list, h3 under an article
     * @return void
     */
    public static function card(array $post, string $heading = 'h2'):void{
        ?>
        <article class="card h-100 shadow-sm overflow-hidden" <?php if($post['lang'] != LANG) echo 'lang="'.htmlspecialchars((string)$post['lang']).'"';?>>
            <?=self::cover($post)?>
            <div class="card-body d-flex flex-column p-4">
                <div class="small text-body-secondary mb-2"><?=self::meta($post)?></div>
                <<?=$heading?> class="h5 fw-bold mb-2"><a class="stretched-link link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::postUrl($post)?>"><?=$post['title']?></a></<?=$heading?>>
                <p class="text-body-secondary mb-4"><?=self::summary($post)?></p>
                <span class="mt-auto fw-semibold text-primary d-inline-flex align-items-center gap-2" aria-hidden="true"><?=T_READ_POST?> <?=self::icon('arrow-right', 16)?></span>
            </div>
        </article>
        <?php
    }


    // Pages
    // ------------------------------------------------------------------------------------------------

    /**
     * List of the articles. On the first page, the most recent article is displayed larger.
     * @param array $posts rows of ModelBlog
     * @return void
     */
    public function viewList(array $posts, bool $firstPage = true){
        if(!count($posts)){
            self::emptyState('newspaper', T_NO_POST, T_NO_POST_HINT, array(self::url('product', 'viewlist'), T_SEE_PRODUCTS));
            return;
        }

        if($firstPage){
            $post = array_shift($posts);
            ?>
            <article class="card shadow-sm overflow-hidden mb-4 mb-lg-5" <?php if($post['lang'] != LANG) echo 'lang="'.htmlspecialchars((string)$post['lang']).'"';?>>
                <div class="row g-0">
                    <div class="col-md-6 col-lg-7 bg-primary">
                        <?=self::cover($post, 'ratio-16x9', 'large', 'h-100')?>
                    </div>
                    <div class="col-md-6 col-lg-5">
                        <div class="card-body d-flex flex-column h-100 p-4 p-lg-5">
                            <span class="badge rounded-pill text-bg-warning align-self-start mb-3"><?=T_LATEST_POST?></span>
                            <div class="small text-body-secondary mb-2"><?=self::meta($post)?></div>
                            <h2 class="h3 fw-bold mb-3"><a class="stretched-link link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::postUrl($post)?>"><?=$post['title']?></a></h2>
                            <p class="text-body-secondary mb-4"><?=self::summary($post, 240)?></p>
                            <span class="mt-auto btn btn-primary align-self-start d-inline-flex align-items-center gap-2" aria-hidden="true"><?=T_READ_POST?> <?=self::icon('arrow-right', 16)?></span>
                        </div>
                    </div>
                </div>
            </article>
            <?php
        }

        if(count($posts)){
            ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach($posts as $post){ ?>
                <div class="col"><?php self::card($post); ?></div>
                <?php } ?>
            </div>
            <?php
        }
    }


    /**
     * Page of an article, with other articles under it
     * @param array $post row of ModelBlog
     * @param array $others rows of ModelBlog
     * @return void
     */
    public function view(array $post, array $others = array()){
        $author = self::author($post);
        $published = $post['status'] == 'published' && strtotime((string)$post['date']) <= time();
        ?>
        <article <?php if($post['lang'] != LANG) echo 'lang="'.htmlspecialchars((string)$post['lang']).'"';?>>
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <?php if(!$published){
                        // Only the users who can edit reach an article that is not published
                        $this->message(array('type' => 'warning', 'text' => T_POST_PREVIEW.' '.self::badge(self::value((string)$post['status']), 'warning')));
                    } ?>
                    <header class="mb-4 mb-lg-5">
                        <h1 class="display-5 fw-bold mb-3"><?=$post['title']?></h1>
                        <?php if(trim((string)$post['excerpt']) != ''){ ?>
                        <p class="lead text-body-secondary mb-4"><?=$post['excerpt']?></p>
                        <?php } ?>
                        <ul class="list-unstyled d-flex flex-wrap align-items-center gap-3 small text-body-secondary mb-0">
                            <?php if($author != ''){ ?>
                            <li class="d-flex align-items-center gap-2"><?=self::icon('person-circle', 18)?> <?=sprintf(T_BY_AUTHOR, '<span class="fw-semibold text-body">'.$author.'</span>')?></li>
                            <?php } ?>
                            <li class="d-flex align-items-center gap-2"><?=self::icon('calendar3', 16)?> <time datetime="<?=date('Y-m-d', strtotime((string)$post['date']) ?: time())?>"><?=self::longDate($post['date'])?></time></li>
                            <li class="d-flex align-items-center gap-2"><?=self::icon('clock', 16)?> <?=sprintf(T_READING_TIME, self::readingTime((string)$post['body']))?></li>
                        </ul>
                    </header>
                </div>
            </div>

            <?php if(self::imageUrl($post['filename'] ?? '') != ''){ ?>
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <?=self::cover($post, 'ratio-21x9', 'large', 'rounded-4 overflow-hidden shadow-sm mb-4 mb-lg-5')?>
                </div>
            </div>
            <?php } ?>

            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <div class="card card-body shadow-sm p-4 p-lg-5 fs-5">
                        <?=self::paragraphs((string)$post['body'])?>
                    </div>
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-4">
                        <a class="icon-link link-secondary link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::listUrl()?>"><?=self::icon('arrow-left', 16)?> <?=T_BACK_TO_BLOG?></a>
                        <?php if(Auth::canEdit()){ ?>
                        <a class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-2" href="<?=self::url('post', 'edit', array('id_post' => (int)$post['id_post']))?>"><?=self::icon('pencil', 14)?> <?=T_EDIT_POST?></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </article>

        <?php if(count($others)){ ?>
        <section class="border-top pt-4 pt-lg-5 mt-5" aria-labelledby="more_posts">
            <div class="d-flex flex-wrap align-items-baseline justify-content-between gap-2 mb-4">
                <h2 class="h4 fw-bold mb-0" id="more_posts"><?=T_MORE_POSTS?></h2>
                <a class="fw-semibold link-primary link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::listUrl()?>"><?=T_ALL_POSTS?></a>
            </div>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach($others as $other){ ?>
                <div class="col"><?php self::card($other, 'h3'); ?></div>
                <?php } ?>
            </div>
        </section>
        <?php }
    }


    /**
     * The article does not exist, or is not published
     * @return void
     */
    public function notFound(){
        self::emptyState('newspaper', T_POST_NOT_FOUND, T_POST_NOT_FOUND_HINT, array(self::listUrl(), T_BACK_TO_BLOG));
    }
}
