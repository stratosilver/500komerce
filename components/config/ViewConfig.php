<?php
/**
 * Page Paramètres of the back office: form of the settings of the table config
 */

declare(strict_types=1);
namespace Apgenic\Config;


class ViewConfig extends \Apgenic\Classes\ViewTemplate {

    // One form, no list: no links Edit list, View list, Trash, New in the header of the page
    protected bool $listLinks = false;


    /**
     * Form of the settings
     * @param array $values key => value, not escaped
     * @return void
     */
    public function edit(array $values, array $message = null){
        ?>
        <div id="core_content">
        <?php $this->message($message); ?>

        <form class="card card-body shadow-sm p-lg-4 mb-4" method="POST" action="<?=self::url('config', 'edit')?>">
        <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
        <fieldset>
        <div class="row">

        <div class="col-lg-6"><div class="mb-3">
            <label class="form-label" for="site_name"><?=T_CONFIG_SITE_NAME?>&nbsp;*</label>
            <input required class="form-control" type="text" name="site_name" id="site_name" maxlength="<?=ControllerConfig::SITE_NAME_MAX?>" value="<?=htmlspecialchars((string)($values[ModelConfig::SITE_NAME] ?? ''))?>" aria-describedby="site_name_help"/>
            <div class="form-text" id="site_name_help"><?=T_CONFIG_SITE_NAME_HELP?></div>
        </div></div>

        <div class="col-lg-3 col-md-6"><div class="mb-3">
            <label class="form-label" for="tax_rate"><?=T_CONFIG_TAX_RATE?>&nbsp;*</label>
            <div class="input-group">
                <input required class="form-control text-end" type="text" inputmode="decimal" pattern="\d{1,3}([.,]\d{1,2})?" name="tax_rate" id="tax_rate" value="<?=htmlspecialchars((string)($values[ModelConfig::TAX_RATE] ?? ''))?>" aria-describedby="tax_rate_help"/>
                <span class="input-group-text">%</span>
            </div>
        </div></div>

        <div class="col-lg-3 col-md-6"><div class="mb-3">
            <label class="form-label" for="default_language"><?=T_CONFIG_DEFAULT_LANGUAGE?>&nbsp;*</label>
            <select required class="form-select" name="default_language" id="default_language" aria-describedby="default_language_help">
            <?php foreach(LANGUAGES as $code => $name){ ?>
                <option value="<?=htmlspecialchars((string)$code)?>" <?php if(($values[ModelConfig::DEFAULT_LANGUAGE] ?? '') === $code) echo 'selected="selected"';?>><?=htmlspecialchars((string)$name)?></option>
            <?php } ?>
            </select>
        </div></div>

        <div class="col-12"><div class="mb-3">
            <div class="form-text" id="tax_rate_help"><?=T_CONFIG_TAX_RATE_HELP?></div>
            <div class="form-text" id="default_language_help"><?=T_CONFIG_DEFAULT_LANGUAGE_HELP?></div>
        </div></div>

        <div class="col-12">
            <div class="border-top pt-3">
                <input type="submit" value="<?=T_SAVE?>" class="btn btn-primary">
            </div>
        </div>

        </div>
        </fieldset>
        </form>
        </div>
        <?php
    }
}
