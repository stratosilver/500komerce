<?php
/**
 * Pages of the access by code: code, form of 15 fields, email, link sent, form opened by the link.
 * The values typed are html-escaped by the controller and displayed as they are, like in the other views.
 */

declare(strict_types=1);
namespace Apgenic\Accesscode;

class ViewAccesscode extends \Apgenic\Classes\ViewTemplate
{
    // No "Edit list / View list / +" of the back office
    protected bool $listLinks = false;


    private function csrf():void{
        ?><input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" /><?php
    }


    /**
     * Beginning of a narrow centered card: icon, title, text
     * @param string $color color of the icon: primary, success, warning
     * @return void
     */
    private function cardStart(string $icon, string $title, string $text, string $color = 'primary'):void{
        ?>
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <section class="card shadow-sm text-center p-4 p-sm-5">
                    <div class="d-inline-flex rounded-circle bg-<?=$color?>-subtle text-<?=$color?>-emphasis mx-auto mb-3 p-3"><?=self::icon($icon, 24)?></div>
                    <h2 class="h4 fw-bold"><?=$title?></h2>
                    <p class="text-body-secondary mb-4"><?=$text?></p>
        <?php
    }


    private function cardEnd():void{
        ?>
                </section>
            </div>
        </div>
        <?php
    }


    public function code(string $code, array $message = null):void{
        $this->cardStart('key', T_ACCESS_CODE_TITLE, T_ACCESS_CODE_INTRO);
        $this->message($message);
        ?>
        <form class="text-start" method="POST" action="<?=BASE_URL?>/index.php?component=accesscode&task=code">
            <?php $this->csrf(); ?>
            <div class="mb-4">
                <label class="form-label" for="code"><?=T_CODE?>&nbsp;*</label>
                <input class="form-control form-control-lg font-monospace text-center" type="text" name="code" id="code" value="<?=$code?>" maxlength="64" required autofocus autocomplete="off"/>
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center gap-2"><?=T_NEXT_STEP?> <?=self::icon('arrow-right', 18)?></button>
        </form>
        <?php
        $this->cardEnd();
    }


    public function form(array $fields, array $values, array $errors, int $maxLength, array $message = null):void{
        $this->message($message);
        ?>
        <form class="card card-body shadow-sm p-4" method="POST" action="<?=BASE_URL?>/index.php?component=accesscode&task=form">
            <?php $this->csrf(); ?>
            <div class="row g-3">
                <?php foreach($fields as $name => $label){ ?>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="<?=$name?>"><?=$label?></label>
                    <input class="form-control <?php if(isset($errors[$name])) echo 'is-invalid';?>" type="text" name="<?=$name?>" id="<?=$name?>" value="<?=$values[$name] ?? ''?>" maxlength="<?=$maxLength?>" <?php if(isset($errors[$name])) echo 'aria-invalid="true" aria-describedby="'.$name.'_error"';?>/>
                    <?php if(isset($errors[$name])){ ?><div class="invalid-feedback" id="<?=$name?>_error"><?=$errors[$name]?></div><?php } ?>
                </div>
                <?php } ?>
            </div>
            <div class="d-flex justify-content-end border-top pt-4 mt-4">
                <button type="submit" class="btn btn-primary btn-lg d-inline-flex align-items-center gap-2"><?=T_NEXT_STEP?> <?=self::icon('arrow-right', 18)?></button>
            </div>
        </form>
        <?php
    }


    public function email(string $email, array $message = null):void{
        $this->cardStart('envelope', T_YOUR_EMAIL, T_ACCESS_EMAIL_INTRO);
        $this->message($message);
        ?>
        <form class="text-start" method="POST" action="<?=BASE_URL?>/index.php?component=accesscode&task=email">
            <?php $this->csrf(); ?>
            <div class="mb-4">
                <label class="form-label" for="email"><?=T_EMAIL?>&nbsp;*</label>
                <input class="form-control form-control-lg" type="email" name="email" id="email" value="<?=$email?>" maxlength="255" required autofocus autocomplete="email"/>
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center gap-2"><?=self::icon('envelope', 18)?> <?=T_SEND_LINK?></button>
            <div class="text-center mt-4">
                <a class="icon-link link-secondary link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=accesscode&task=form"><?=self::icon('arrow-left', 16)?> <?=T_BACK_TO_FORM?></a>
            </div>
        </form>
        <?php
        $this->cardEnd();
    }


    public function sent(string $email, bool $mailSent, string $devLink, array $devCodes = array()):void{
        $this->cardStart(
            $mailSent ? 'check-lg' : 'exclamation-triangle',
            $mailSent ? T_CHECK_INBOX : T_FORM_SAVED,
            sprintf($mailSent ? T_FORM_SAVED_SENT : T_FORM_SAVED_NOT_SENT, '<strong class="text-body">'.$email.'</strong>'),
            $mailSent ? 'success' : 'warning'
        );
        if($devLink != ''){
            ?>
            <div class="alert alert-secondary d-flex align-items-start gap-2 text-start mb-0">
                <?=self::icon('gear', 20)?>
                <div class="text-break"><?=T_DEV_MODE?> (AUTH_SHOW_RESET_LINK): <a class="alert-link" href="<?=htmlspecialchars($devLink)?>"><?=htmlspecialchars($devLink)?></a>
                <br/><?=T_CODES?> <?=htmlspecialchars(implode(', ', $devCodes))?></div>
            </div>
            <?php
        }
        $this->cardEnd();
    }


    public function link(array $fields, ?array $row):void{
        if($row === null){
            self::emptyState('link-45deg', T_LINK_INVALID, '', array(BASE_URL.'/index.php?component=accesscode&task=code', T_ENTER_CODE));
            return;
        }
        ?>
        <section class="card card-body shadow-sm p-4">
            <dl class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 mb-0">
                <?php foreach($fields as $name => $label){ ?>
                <div class="col">
                    <dt class="small text-uppercase fw-semibold text-body-secondary"><?=$label?></dt>
                    <dd class="mb-0 text-break"><?=($row[$name] ?? '') != '' ? $row[$name] : '&mdash;'?></dd>
                </div>
                <?php } ?>
                <div class="col">
                    <dt class="small text-uppercase fw-semibold text-body-secondary"><?=T_EMAIL?></dt>
                    <dd class="mb-0 text-break"><?=htmlspecialchars((string)$row['email'])?></dd>
                </div>
            </dl>
        </section>
        <?php
    }
}
