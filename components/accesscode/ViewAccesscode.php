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
        $this->message($message);
        ?>
        <form method="POST" action="<?=BASE_URL?>/index.php?component=accesscode&task=code" style="max-width: 400px">
            <?php $this->csrf(); ?>
            <div class="form-group">
                <label class="control-label" for="code">Code&nbsp;*</label>
                <input class="form-control" type="text" name="code" id="code" value="<?=$code?>" maxlength="64" required autofocus autocomplete="off"/>
            </div>
            <button type="submit" class="btn btn-primary">Continue</button>
        </form>
        <?php
    }


    public function form(array $fields, array $values, array $errors, int $maxLength, array $message = null):void{
        $this->message($message);
        ?>
        <form method="POST" action="<?=BASE_URL?>/index.php?component=accesscode&task=form">
            <?php $this->csrf(); ?>
            <div class="row">
                <?php foreach($fields as $name => $label){ ?>
                <div class="col-md-4"><div class="form-group">
                    <label class="control-label" for="<?=$name?>"><?=$label?></label>
                    <input class="form-control <?php if(isset($errors[$name])) echo 'is-invalid';?>" type="text" name="<?=$name?>" id="<?=$name?>" value="<?=$values[$name] ?? ''?>" maxlength="<?=$maxLength?>"/>
                    <?php if(isset($errors[$name])){ ?><div class="invalid-feedback"><?=$errors[$name]?></div><?php } ?>
                </div></div>
                <?php } ?>
            </div>
            <button type="submit" class="btn btn-primary">Continue</button>
        </form>
        <?php
    }


    public function email(string $email, array $message = null):void{
        $this->message($message);
        ?>
        <p>Enter your email address, we will send you a link to your form.</p>
        <form method="POST" action="<?=BASE_URL?>/index.php?component=accesscode&task=email" style="max-width: 400px">
            <?php $this->csrf(); ?>
            <div class="form-group">
                <label class="control-label" for="email">Email&nbsp;*</label>
                <input class="form-control" type="email" name="email" id="email" value="<?=$email?>" maxlength="255" required autofocus autocomplete="email"/>
            </div>
            <a class="btn btn-outline btn-outline-secondary" href="<?=BASE_URL?>/index.php?component=accesscode&task=form">Back to the form</a>
            <button type="submit" class="btn btn-primary">Send the link</button>
        </form>
        <?php
    }


    public function sent(string $email, bool $mailSent, string $devLink, array $devCodes = array()):void{
        if($mailSent){
            $this->message(array('type' => 'success', 'text' => 'Your form has been saved, a link and new codes have been sent to '.$email));
        }
        else{
            $this->message(array('type' => 'warning', 'text' => 'Your form has been saved, but the email to '.$email.' could not be sent'));
        }
        if($devLink != ''){
            ?>
            <div class="alert alert-secondary">
                Development (AUTH_SHOW_RESET_LINK): <a href="<?=htmlspecialchars($devLink)?>"><?=htmlspecialchars($devLink)?></a>
                <br/>Codes: <?=htmlspecialchars(implode(', ', $devCodes))?>
            </div>
            <?php
        }
    }


    public function link(array $fields, ?array $row):void{
        if($row === null){
            $this->message(array('type' => 'danger', 'text' => 'This link is not valid or has expired'));
            return;
        }
        ?>
        <table class="table table-bordered" style="max-width: 800px">
            <tbody>
            <?php foreach($fields as $name => $label){ ?>
            <tr><th scope="row" class="shrink"><?=$label?></th><td><?=$row[$name] ?? ''?></td></tr>
            <?php } ?>
            <tr><th scope="row" class="shrink">Email</th><td><?=htmlspecialchars((string)$row['email'])?></td></tr>
            </tbody>
        </table>
        <?php
    }
}
