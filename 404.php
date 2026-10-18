<?php
/**
 * Page not found, displayed by index.php when a controller throws an exception.
 * $errorMessage: message of the exception (optional).
 * When the page has already started (header of the layout sent), only the content is added, then the end of the page.
 */
$errorMessage = isset($errorMessage) ? (string)$errorMessage : '';
$pageStarted = headers_sent() || ob_get_length();
$oView404 = new Apgenic\Classes\ViewTemplate();

if(!$pageStarted){
    http_response_code(404);
    $oView404->header(T_PAGE_NOT_FOUND);
}
?>
<div class="k-empty k-empty-page">
    <div class="k-empty-code">404</div>
    <h2 class="k-empty-title"><?=T_404_TITLE?></h2>
    <?php if($errorMessage != '' && $errorMessage != T_PAGE_NOT_FOUND){ ?>
    <p><?=htmlspecialchars($errorMessage)?></p>
    <?php } else { ?>
    <p><?=T_404_TEXT?></p>
    <?php } ?>
    <a class="btn btn-primary" href="<?=BASE_URL?>/"><?=Apgenic\Classes\ViewTemplate::icon('house', 16)?> <?=T_BACK_HOME?></a>
</div>
<?php
$oView404->footer();
