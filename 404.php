<?php
/**
 * Page not found, displayed by index.php when a controller throws an exception.
 * $errorMessage: message of the exception (optional).
 * When the page has already started (header of the layout sent), only the content is added, then the end of the page.
 */
$errorMessage = isset($errorMessage) ? (string)$errorMessage : '';
// A request of HTMX only receives the content, without the layout
$htmx = ($_SERVER['PHP_SELF'] ?? '') == '/htmx.php';
$pageStarted = headers_sent() || ob_get_length();
$oView404 = new Apgenic\Classes\ViewTemplate();

if(!$pageStarted){
    http_response_code(404);
    if(!$htmx){
        $oView404->header(T_PAGE_NOT_FOUND);
    }
}
?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card card-body shadow-sm text-center py-5 px-4">
            <div class="display-1 fw-bold text-primary lh-1 mb-3">404</div>
            <h2 class="h4 fw-bold"><?=T_404_TITLE?></h2>
            <?php if($errorMessage != '' && $errorMessage != T_PAGE_NOT_FOUND){ ?>
            <p class="text-body-secondary"><?=htmlspecialchars($errorMessage)?></p>
            <?php } else { ?>
            <p class="text-body-secondary"><?=T_404_TEXT?></p>
            <?php } ?>
            <div><a class="btn btn-primary d-inline-flex align-items-center gap-2" href="<?=Apgenic\Classes\Url::home()?>"><?=Apgenic\Classes\ViewTemplate::icon('house', 16)?> <?=T_BACK_HOME?></a></div>
        </div>
    </div>
</div>
<?php
if(!$htmx){
    $oView404->footer();
}
