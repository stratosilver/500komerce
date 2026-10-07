<?php
/**
 * Created by PhpStorm.
 * User: TILMED199
 * Date: 11-05-21
 * Time: 19:18
 * Project: exsilicon
 */
namespace Apgenic\Classes;

class ViewTemplate
{
    use ViewTrait;

    const ICONS = [
            'plus-lg' => '<path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>',
            'x-lg' => '<path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>',
            'check2' => '<path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>',
            'view' => '<path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z"/><path d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 8m0 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5"/>',
            'pencil' => '<path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>',
            'trash3' => '<path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>',
            'screwdriver' => '<path d="M0 .995.995 0l3.064 2.19a1 1 0 0 1 .417.809v.07c0 .264.105.517.291.704l5.677 5.676.909-.303a1 1 0 0 1 1.018.24l3.338 3.339a.995.995 0 0 1 0 1.406L14.13 15.71a.995.995 0 0 1-1.406 0l-3.337-3.34a1 1 0 0 1-.24-1.018l.302-.909-5.676-5.677a1 1 0 0 0-.704-.291H3a1 1 0 0 1-.81-.417zm11.293 9.595a.497.497 0 1 0-.703.703l2.984 2.984a.497.497 0 0 0 .703-.703z"/>',
            'undel' => '<path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2z"/><path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466"/>',
            'robot' => '<path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5M3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.6 26.6 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.93.93 0 0 1-.765.935c-.845.147-2.34.346-4.235.346s-3.39-.2-4.235-.346A.93.93 0 0 1 3 9.219zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a25 25 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25 25 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135"/><path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2zM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5"/>',
    ];

    static function icon(string $name, int $size = 20, string $viewBox = '0 0 16 16'): string{
        return '<svg width="'.$size.'" height="'.$size.'" fill="currentColor" viewBox="'.$viewBox.'">'.(self::ICONS[$name] ?? '').'</svg>';
    }

    /*
     *
     */
    function header(string $title){
        if(strtolower($_SERVER['PHP_SELF']) == '/ajax.php') return '';
        echo '<!doctype html>
        <html lang="fr">
          <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <meta name="description" content="">
            <meta http-equiv="X-UA-Compatible" content="IE=edge" />
            <title>'.$title.'</title>
            <!-- Bootstrap core CSS -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/htmx/2.0.4/htmx.min.js"></script>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
            <link href="'.BASE_URL.'/css/styles.css" rel="stylesheet" media="screen">
            <style>
                body{
                margin-top:60px;
                }
                /*
                .btn-outline {
                    border-width: 2px;
                }*/
            </style>
          </head>
          <body style="min-height: 100%">
        ';
        $this->topNav();
        $this->leftNav();
        $this->mainHeader($title);
    }


    function topNav(){
        if(strtolower($_SERVER['PHP_SELF']) == '/ajax.php') return '';
        ?>
        <nav class="fixed-top navbar navbar-expand-lg bg-light">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">

                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="<?=BASE_URL?>/">
                            <svg class="mr-2 bi bi-robot" fill="currentColor" height="35"  width="35" viewBox="0 0 16 18"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5M3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.6 26.6 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.93.93 0 0 1-.765.935c-.845.147-2.34.346-4.235.346s-3.39-.2-4.235-.346A.93.93 0 0 1 3 9.219zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a25 25 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25 25 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135"/>
                                <path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2zM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5"/>
                            </svg>
                            100Komerce</a>
                    </li>

                </ul>

                <?php if(isset($_SESSION['user'])){ ?>
                    <a class="nav-link text-dark mr-2" href="<?=BASE_URL?>/index.php?component=user&task=profile" title="My profile"><?=$_SESSION['user']['first_name'].' '.$_SESSION['user']['last_name']?></a>
                    <form class="form-inline" method="POST" action="<?=BASE_URL?>/index.php?component=user&task=logout">
                        <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
                        <button type="submit" class="btn btn-outline-secondary btn-sm">Sign out</button>
                    </form>
                <?php } else { ?>
                    <a class="btn btn-outline-secondary btn-sm" href="<?=BASE_URL?>/index.php?component=user&task=login">Sign in</a>
                <?php } ?>

            </div>
        </nav>
        <?php
    }


    function leftNav($active = ''){
        if(strtolower($_SERVER['PHP_SELF']) == '/ajax.php') return '';
        if($active == ''){ $active = COMPONENT; }

        if(!isset($_GET['task'] )) $_GET['task'] = '';
        ?>
        <div class="container-fluid">
        <div class="row">
        <div class="col-md-2 bg-light" style="min-height: calc(100vh - 100px);">

            <ul class="nav flex-column left-menu">

                
                <li class="nav-item">
                    <a class="nav-link <?php if($active == 'product') echo 'active';?>" href="<?=BASE_URL?>?component=product">Product</a>
                </li>
            
                <li class="nav-item">
                    <a class="nav-link <?php if($active == 'category') echo 'active';?>" href="<?=BASE_URL?>?component=category">Category</a>
                </li>
            
                <li class="nav-item">
                    <a class="nav-link <?php if($active == 'post') echo 'active';?>" href="<?=BASE_URL?>?component=post">Post</a>
                </li>
            
                <li class="nav-item">
                    <a class="nav-link <?php if($active == 'discount') echo 'active';?>" href="<?=BASE_URL?>?component=discount">Discount</a>
                </li>
            
                <li class="nav-item">
                    <a class="nav-link <?php if($active == 'customerorder') echo 'active';?>" href="<?=BASE_URL?>?component=customerorder">Customer order</a>
                </li>
            
                <li class="nav-item">
                    <a class="nav-link <?php if($active == 'translation') echo 'active';?>" href="<?=BASE_URL?>?component=translation">Translation</a>
                </li>
            
                <li class="nav-item">
                    <a class="nav-link <?php if($active == 'media') echo 'active';?>" href="<?=BASE_URL?>?component=media">Media</a>
                </li>
            
                <?php // The users are managed by the administrators only
                if(Auth::can('user', 'editlist')){ ?>
                <li class="nav-item">
                    <a class="nav-link <?php if($active == 'user') echo 'active';?>" href="<?=BASE_URL?>?component=user">User</a>
                </li>
                <?php } ?>
            


            </ul>

        </div>

        <?php
    }



function mainHeader($title){
    if(strtolower($_SERVER['PHP_SELF']) == '/ajax.php') return '';
    ?>
    <main role="main" class="col-md-10 ml-sm-auto px-4  main-container">
    <?php
    // Only the links allowed by the permissions of the user are displayed (the access is checked again by index.php)
    $canEditList = Auth::can(COMPONENT, 'editlist');
    $canViewList = Auth::can(COMPONENT, 'viewlist');
    $listUrl = $canEditList ? 'editlist' : ($canViewList ? 'viewlist' : '');
    ?>
    <?php if($canEditList || $canViewList){ ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <?php if($canEditList){ ?>
            <li class="breadcrumb-item"><a class="text-dark" href="<?= BASE_URL ?>/index.php?component=<?=COMPONENT?>&task=editlist">Edit list</a></li>
            <?php } ?>
            <?php if($canViewList){ ?>
            <li class="breadcrumb-item"><a class="text-dark" href="<?= BASE_URL ?>/index.php?component=<?=COMPONENT?>&task=viewlist">View List</a></li>
            <?php } ?>
        </ol>
    </nav>
    <?php } ?>

    <div class="" style="display: flex; flex-wrap: wrap;">
        <div class="align-self-center" style="flex-basis: 80%">
            <?php if($listUrl != ''){ ?>
            <h1><a href="<?= BASE_URL ?>/index.php?component=<?=COMPONENT?>&task=<?=$listUrl?>"><?=ucfirst($title)?></a></h1>
            <?php } else { ?>
            <h1><?=ucfirst($title)?></h1>
            <?php } ?>
        </div>
        <div class="text-right" style="flex-basis: 20%">
            <?php if(Auth::can(COMPONENT, 'edit')){ ?>
            <a href="<?= BASE_URL ?>/index.php?component=<?=COMPONENT?>&task=edit" class="btn btn-outline btn-outline-success" style="margin-top: 10px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"></path>
                </svg>
            </a>
            <?php } ?>
        </div>
    </div>
    <div id="main_content">

    <?php
}


    /**
     * Search bar
     * @return void
     */
    public function search(array $fieldsList = [], $task = 'viewlist'):void{
        $fieldSearch = $_GET['field_search'] ?? '';
        $fieldSearchValue = $_GET['field_search_value'] ?? '';
        $textSearch = $_GET['text_search'] ?? '';
        ?>
        <nav class="navbar navbar-light bg-light">
            <a class="navbar-brand">Search</a>
            <!--<form class="form-inline" hx-target="#core_content" hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php">-->
            <form class="form-inline" method="get" action="<?=BASE_URL?>/index.php">
                <select name="field_search" class="form-control mr-sm-2">
                    <option value="">Select Field</option>
                    <?php
                    foreach ($fieldsList as $field) {
                        ?>
                        <option <?php if($fieldSearch == $field) echo 'selected';?> value="<?=$field?>"><?=$field?></option>
                        <?php
                    }
                    ?>
                </select>
                <input class="form-control mr-sm-2" name="field_search_value" value="<?=$fieldSearchValue?>" type="search" placeholder="Filter Value" aria-label="Filter Value">


                <input class="form-control mr-sm-2" name="text_search"  value="<?=$textSearch?>" type="search" placeholder="Text Search" aria-label="Text Search">
                <input type="hidden" name="component" value="<?=COMPONENT?>">
                <input type="hidden" name="task" value="<?=$task?>">
                <button class="btn btn-light my-2 my-sm-0" type="submit">Search</button>
            </form>
        </nav>

        <?php
    }






    function mainFooter(){
        if(strtolower($_SERVER['PHP_SELF']) == '/ajax.php') return '';
        ?>
        </div>
        </main>

        </div><!-- end of row -->
        </div><!-- end of container-fluid -->
        <?php
    }

    function footer(){
        if(strtolower($_SERVER['PHP_SELF']) == '/ajax.php') return '';

        $this->mainFooter();
        echo '    <footer class="footer bg-light">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-3"></div>
                <div class="col-sm-3"></div>
                <div class="col-sm-3"></div>
            </div>
            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-10"></div>
            </div>
        </div>
    </footer>
        <script>
            document.addEventListener(\'htmx:afterSwap\', function(evt) {
                const items = document.querySelectorAll(\'#tabs_list a\');

                items.forEach(item => {
                    item.addEventListener(\'click\', () => {
                    items.forEach(i => i.classList.remove(\'active\'));
                        item.classList.add(\'active\');
                        console.log(\'activate \'+item.id);
                    });
                });

            });
</script>
</body>

</html>';
    }


    function pagination($numberOfPages, $curentPage, $link){
        //if($numberOfPages >1){
        echo '<nav aria-label="Pagination"><ul class="pagination  flex-wrap">';

        for($i=1; $i<=$numberOfPages; $i++){
            if($i == $curentPage){
                ?>
                <li class="page-item active"><a class="page-link" href="<?=$link?>page=<?=$i?>"><?=$i?></a></li>
                <?php
            }
            else{
                ?>
                <li class="page-item"><a  class="page-link" href="<?=$link?>page=<?=$i?>"><?=$i?></a></li>
                <?php
            }
        }
        echo '</ul></nav>';
        //}
    }

    function message($message = null){
        if(is_array($message) && isset($message['text'])){
            ?>
            <div class="alert alert-<?=$message['type']?>">
                <?=$message['text']?>
            </div>
            <?php
        }
    }


}
