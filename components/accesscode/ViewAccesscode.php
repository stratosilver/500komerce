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


    public function code(string $code, array $message = null):void{
        ?>
        <div class="k-narrow">
            <section class="k-card k-card-centered">
                <div class="k-card-icon"><?=self::icon('key', 24)?></div>
                <h2 class="k-card-title"><?=T_ACCESS_CODE_TITLE?></h2>
                <p class="k-card-subtitle"><?=T_ACCESS_CODE_INTRO?></p>
                <?php $this->message($message); ?>
                <form method="POST" action="<?=BASE_URL?>/index.php?component=accesscode&task=code">
                    <?php $this->csrf(); ?>
                    <div class="form-group text-left">
                        <label class="control-label" for="code"><?=T_CODE?>&nbsp;*</label>
                        <input class="form-control form-control-lg k-code-input" type="text" name="code" id="code" value="<?=$code?>" maxlength="64" required autofocus autocomplete="off"/>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block"><?=T_NEXT_STEP?> <?=self::icon('arrow-right', 18)?></button>
                </form>
            </section>
        </div>
        <?php
    }


    public function form(array $fields, array $values, array $errors, int $maxLength, array $message = null):void{
        $this->message($message);
        ?>
        <form class="k-card k-fieldset" method="POST" action="<?=BASE_URL?>/index.php?component=accesscode&task=form">
            <?php $this->csrf(); ?>
            <div class="row">
                <?php foreach($fields as $name => $label){ ?>
                <div class="col-md-6 col-lg-4"><div class="form-group">
                    <label class="control-label" for="<?=$name?>"><?=$label?></label>
                    <input class="form-control <?php if(isset($errors[$name])) echo 'is-invalid';?>" type="text" name="<?=$name?>" id="<?=$name?>" value="<?=$values[$name] ?? ''?>" maxlength="<?=$maxLength?>" <?php if(isset($errors[$name])) echo 'aria-invalid="true" aria-describedby="'.$name.'_error"';?>/>
                    <?php if(isset($errors[$name])){ ?><div class="invalid-feedback" id="<?=$name?>_error"><?=$errors[$name]?></div><?php } ?>
                </div></div>
                <?php } ?>
            </div>
            <div class="k-form-footer justify-content-end">
                <button type="submit" class="btn btn-primary btn-lg"><?=T_NEXT_STEP?> <?=self::icon('arrow-right', 18)?></button>
            </div>
        </form>
        <?php
    }


    public function email(string $email, array $message = null):void{
        ?>
        <div class="k-narrow">
            <section class="k-card k-card-centered">
                <div class="k-card-icon"><?=self::icon('envelope', 24)?></div>
                <h2 class="k-card-title"><?=T_YOUR_EMAIL?></h2>
                <p class="k-card-subtitle"><?=T_ACCESS_EMAIL_INTRO?></p>
                <?php $this->message($message); ?>
                <form method="POST" action="<?=BASE_URL?>/index.php?component=accesscode&task=email">
                    <?php $this->csrf(); ?>
                    <div class="form-group text-left">
                        <label class="control-label" for="email"><?=T_EMAIL?>&nbsp;*</label>
                        <input class="form-control form-control-lg" type="email" name="email" id="email" value="<?=$email?>" maxlength="255" required autofocus autocomplete="email"/>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block"><?=self::icon('envelope', 18)?> <?=T_SEND_LINK?></button>
                    <a class="k-link-muted mt-3" href="<?=BASE_URL?>/index.php?component=accesscode&task=form"><?=self::icon('arrow-left', 16)?> <?=T_BACK_TO_FORM?></a>
                </form>
            </section>
        </div>
        <?php
    }


    public function sent(string $email, bool $mailSent, string $devLink, array $devCodes = array()):void{
        ?>
        <div class="k-narrow">
            <section class="k-card k-card-centered">
                <div class="k-card-icon <?=$mailSent ? 'k-card-icon-success' : 'k-card-icon-warning'?>"><?=self::icon($mailSent ? 'check-lg' : 'exclamation-triangle', 24)?></div>
                <h2 class="k-card-title"><?=$mailSent ? T_CHECK_INBOX : T_FORM_SAVED?></h2>
                <p class="k-card-subtitle">
                    <?php if($mailSent){ ?>
                    <?=sprintf(T_FORM_SAVED_SENT, '<strong>'.$email.'</strong>')?>
                    <?php } else { ?>
                    <?=sprintf(T_FORM_SAVED_NOT_SENT, '<strong>'.$email.'</strong>')?>
                    <?php } ?>
                </p>
                <?php if($devLink != ''){ ?>
                <div class="alert alert-secondary k-alert text-left">
                    <?=self::icon('gear', 20)?>
                    <div><?=T_DEV_MODE?> (AUTH_SHOW_RESET_LINK): <a href="<?=htmlspecialchars($devLink)?>"><?=htmlspecialchars($devLink)?></a>
                    <br/><?=T_CODES?> <?=htmlspecialchars(implode(', ', $devCodes))?></div>
                </div>
                <?php } ?>
            </section>
        </div>
        <?php
    }


    public function link(array $fields, ?array $row):void{
        if($row === null){
            ?>
            <div class="k-empty">
                <div class="k-empty-icon"><?=self::icon('link-45deg', 28)?></div>
                <h2 class="k-empty-title"><?=T_LINK_INVALID?></h2>
                <a class="btn btn-primary" href="<?=BASE_URL?>/index.php?component=accesscode&task=code"><?=T_ENTER_CODE?></a>
            </div>
            <?php
            return;
        }
        ?>
        <section class="k-card">
            <dl class="k-meta-list k-meta-list-columns">
                <?php foreach($fields as $name => $label){ ?>
                <div><dt><?=$label?></dt><dd><?=($row[$name] ?? '') != '' ? $row[$name] : '&mdash;'?></dd></div>
                <?php } ?>
                <div><dt><?=T_EMAIL?></dt><dd><?=htmlspecialchars((string)$row['email'])?></dd></div>
            </dl>
        </section>
        <?php
    }
}
