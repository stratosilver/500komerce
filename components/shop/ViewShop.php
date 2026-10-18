<?php
/**
 * Pages of the shop: cart, checkout, confirmation of the order.
 * The texts coming from the database are stored html-escaped and displayed as they are, like in the other views.
 */

declare(strict_types=1);
namespace Apgenic\Shop;

use Apgenic\Address\ModelAddress;

class ViewShop extends \Apgenic\Classes\ViewTemplate
{
    // No "Edit list / View list / +" of the back office on the pages of the shop
    protected bool $listLinks = false;

    /** Steps of an order, displayed above the cart, the checkout and the confirmation: task => label */
    const STEPS = array('cart' => T_STEP_CART, 'checkout' => T_STEP_DETAILS, 'confirmation' => T_STEP_CONFIRMATION);


    /**
     * Header of the pages of the shop: title and steps of the order
     * @return void
     */
    function mainHeader($title){
        $task = (string)($_GET['task'] ?? 'cart');
        if(!isset(self::STEPS[$task])){
            parent::mainHeader($title);
            return;
        }
        $current = array_search($task, array_keys(self::STEPS), true);
        echo '<main id="main" class="k-shop-main"><div class="container k-container">';
        ?>
        <div class="k-page-header k-checkout-header">
            <h1 class="k-page-title"><?=ucfirst($title)?></h1>
            <ol class="k-steps" aria-label="<?=T_STEPS_LABEL?>">
                <?php $n = 0; foreach(self::STEPS as $step => $label){ ?>
                <li class="k-step <?=$n < $current ? 'done' : ($n == $current ? 'current' : '')?>" <?php if($n == $current) echo 'aria-current="step"';?>>
                    <span class="k-step-number"><?=$n < $current ? self::icon('check-lg', 14) : $n + 1?></span>
                    <span class="k-step-label"><?=$label?></span>
                </li>
                <?php $n++; } ?>
            </ol>
        </div>
        <div id="main_content">
        <?php
    }


    /**
     * Images of the products of the lines: id_product => url of the thumbnail
     * @param array $items lines with id_product
     * @return array
     */
    private function thumbnails(array $items):array{
        $ids = array();
        foreach($items as $item){
            if(isset($item['id_product'])) $ids[] = (int)$item['id_product'];
        }
        $urls = array();
        if(!count($ids)){
            return $urls;
        }
        $oView = new \Apgenic\Product\ViewProduct(array());
        $oView->images = (new \Apgenic\Product\ModelProduct())->getImages($ids);
        foreach($ids as $id){
            $urls[$id] = $oView->imageUrl($id, 0, 'thumb');
        }
        return $urls;
    }


    /**
     * Amounts of a cart or of an order
     * @return void
     */
    private function totals(int $subtotal, int $tax, int $total):void{
        ?>
        <dl class="k-totals">
            <div><dt><?=T_SUBTOTAL_EXCL_TAX?></dt><dd><?=self::money($subtotal)?></dd></div>
            <div><dt><?=sprintf(T_VAT, self::taxRateText())?></dt><dd><?=self::money($tax)?></dd></div>
            <div class="k-totals-grand"><dt><?=T_TOTAL_INCL_TAX?></dt><dd><?=self::money($total)?></dd></div>
        </dl>
        <?php
    }


    /**
     * Short list of the lines of a cart or of an order: image, name, quantity, amount
     * @param array $items lines: name, quantity, unit_price_amount, id_product (optional)
     * @return void
     */
    private function lines(array $items):void{
        $thumbnails = $this->thumbnails($items);
        ?>
        <ul class="k-mini-lines">
            <?php foreach($items as $item){
                $thumb = $thumbnails[(int)($item['id_product'] ?? 0)] ?? ''; ?>
            <li>
                <span class="k-mini-media">
                    <?php if($thumb != ''){ ?><img src="<?=htmlspecialchars($thumb)?>" alt="" loading="lazy"><?php } else { echo self::icon('image', 18); } ?>
                    <span class="k-mini-qty" aria-label="<?=T_QUANTITY?>"><?=(int)$item['quantity']?></span>
                </span>
                <span class="k-mini-name"><?=$item['name']?></span>
                <span class="k-mini-amount"><?=self::money((int)$item['quantity'] * (int)$item['unit_price_amount'])?></span>
            </li>
            <?php } ?>
        </ul>
        <?php
    }


    /**
     * The cart
     * @return void
     */
    public function cart(ModelCart $cart, array $message = null){
        $this->message($message);

        if(!count($cart->items)){
            ?>
            <div class="k-empty">
                <div class="k-empty-icon"><?=self::icon('bag', 28)?></div>
                <h2 class="k-empty-title"><?=T_CART_EMPTY?></h2>
                <p><?=T_CART_EMPTY_HINT?></p>
                <a class="btn btn-primary" href="<?=BASE_URL?>/index.php?component=product&task=viewlist"><?=T_SEE_PRODUCTS?></a>
            </div>
            <?php
            return;
        }
        $thumbnails = $this->thumbnails($cart->items);
        $count = 0;
        foreach($cart->items as $item){ $count += (int)$item['quantity']; }
        ?>
        <div class="row">
            <div class="col-lg-8 mb-4">
                <form class="k-card k-cart" method="POST" action="<?=BASE_URL?>/index.php?component=shop&task=update">
                    <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
                    <!-- Default button: the key Enter in a quantity updates the cart (instead of removing the first line) -->
                    <button type="submit" class="k-default-submit" tabindex="-1" aria-hidden="true"><?=T_UPDATE_CART?></button>

                    <div class="k-card-header">
                        <h2 class="k-card-title"><?=sprintf($count > 1 ? T_ITEMS : T_ITEMS_ONE, $count)?></h2>
                    </div>

                    <ul class="k-cart-lines">
                        <?php foreach($cart->items as $item){
                            $idItem = (int)$item['id_cart_item'];
                            $url = BASE_URL.'/index.php?component=product&task=view&id_product='.(int)$item['id_product'];
                            $thumb = $thumbnails[(int)$item['id_product']] ?? ''; ?>
                        <li class="k-cart-line">
                            <a class="k-line-media" href="<?=$url?>" tabindex="-1" aria-hidden="true">
                                <?php if($thumb != ''){ ?><img src="<?=htmlspecialchars($thumb)?>" alt="" loading="lazy"><?php } else { echo self::icon('image', 24); } ?>
                            </a>
                            <div class="k-line-info">
                                <a class="k-line-name" href="<?=$url?>"><?=$item['name']?></a>
                                <div class="k-line-meta"><?=sprintf(T_PRICE_PER_UNIT, self::money((int)$item['unit_price_amount']))?></div>
                            </div>
                            <div class="k-line-qty">
                                <label class="sr-only" for="quantity_<?=$idItem?>"><?=sprintf(T_QUANTITY_OF, $item['name'])?></label>
                                <input class="form-control" type="number" min="0" max="<?=ModelCart::MAX_QUANTITY?>" step="1" name="quantity[<?=$idItem?>]" id="quantity_<?=$idItem?>" value="<?=(int)$item['quantity']?>" inputmode="numeric"/>
                            </div>
                            <div class="k-line-total"><?=self::money((int)$item['quantity'] * (int)$item['unit_price_amount'])?></div>
                            <button type="submit" name="remove" value="<?=$idItem?>" class="k-icon-button k-line-remove" title="<?=T_REMOVE?>" aria-label="<?=sprintf(T_REMOVE_ITEM, $item['name'])?>"><?=self::icon('trash3', 16)?></button>
                        </li>
                        <?php } ?>
                    </ul>

                    <div class="k-card-footer k-cart-actions">
                        <a class="k-link-muted" href="<?=BASE_URL?>/index.php?component=product&task=viewlist"><?=self::icon('arrow-left', 16)?> <?=T_CONTINUE_SHOPPING?></a>
                        <button type="submit" class="btn btn-outline-primary"><?=self::icon('arrow-repeat', 16)?> <?=T_UPDATE_CART?></button>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <aside class="k-card k-summary" aria-label="<?=T_SUMMARY?>">
                    <h2 class="k-card-title"><?=T_SUMMARY?></h2>
                    <?php $this->totals((int)$cart->subtotal, (int)$cart->tax, (int)$cart->total); ?>
                    <a class="btn btn-accent btn-lg btn-block" href="<?=BASE_URL?>/index.php?component=shop&task=checkout"><?=T_CHECKOUT?> <?=self::icon('arrow-right', 18)?></a>
                    <p class="k-summary-note"><?=self::icon('bag-check', 16)?> <?=T_NO_ACCOUNT_NEEDED?></p>
                </aside>
            </div>
        </div>
        <?php
    }


    /**
     * One field of the checkout form, with its error message
     * @return void
     */
    private function field(string $name, string $label, array $values, array $errors, string $type = 'text', string $attributes = '', string $col = 'col-md-6'):void{
        ?>
        <div class="<?=$col?>"><div class="form-group">
            <label class="control-label" for="<?=$name?>"><?=$label?></label>
            <input class="form-control <?php if(isset($errors[$name])) echo 'is-invalid';?>" type="<?=$type?>" name="<?=$name?>" id="<?=$name?>" value="<?=$type == 'password' ? '' : ($values[$name] ?? '')?>" <?=$attributes?> <?php if(isset($errors[$name])) echo 'aria-invalid="true" aria-describedby="'.$name.'_error"';?>/>
            <?php if(isset($errors[$name])){ ?><div class="invalid-feedback" id="<?=$name?>_error"><?=$errors[$name]?></div><?php } ?>
        </div></div>
        <?php
    }


    /**
     * Choice of an address already saved, or the fields of a new one
     * @param string $type delivery or postal
     * @param array $addresses addresses of the user, rows of the table address
     * @return void
     */
    private function address(string $type, array $values, array $errors, array $addresses):void{
        $chosen = (string)($values['id_address_'.$type] ?? 'new');
        if(count($addresses)){
            ?>
            <div class="k-choice-list" role="radiogroup" aria-label="<?=$type == 'delivery' ? T_DELIVERY_ADDRESS : T_POSTAL_ADDRESS?>">
                <?php foreach($addresses as $row){ ?>
                <div class="custom-control custom-radio k-choice">
                    <input class="custom-control-input address-choice" data-type="<?=$type?>" type="radio" name="id_address_<?=$type?>" id="address_<?=$type?>_<?=(int)$row['id_address']?>" value="<?=(int)$row['id_address']?>" <?php if($chosen == (string)$row['id_address']) echo 'checked';?>>
                    <label class="custom-control-label" for="address_<?=$type?>_<?=(int)$row['id_address']?>">
                        <?=self::icon('geo-alt', 16)?> <?=ModelAddress::line($row)?>
                    </label>
                </div>
                <?php } ?>
                <div class="custom-control custom-radio k-choice">
                    <input class="custom-control-input address-choice" data-type="<?=$type?>" type="radio" name="id_address_<?=$type?>" id="address_<?=$type?>_new" value="new" <?php if($chosen == 'new') echo 'checked';?>>
                    <label class="custom-control-label" for="address_<?=$type?>_new"><?=self::icon('plus-lg', 16)?> <?=T_NEW_ADDRESS?></label>
                </div>
            </div>
            <?php
        }
        else{
            ?><input type="hidden" name="id_address_<?=$type?>" value="new"/><?php
        }
        ?>
        <div class="row k-new-address" id="new_address_<?=$type?>" <?php if($chosen != 'new') echo 'style="display:none"';?>>
            <?php
            $this->field($type.'_street', T_STREET.'&nbsp;*', $values, $errors, 'text', 'maxlength="200" autocomplete="address-line1"', 'col-md-8');
            $this->field($type.'_number', T_NUMBER.'&nbsp;*', $values, $errors, 'text', 'maxlength="20"', 'col-md-2 col-6');
            $this->field($type.'_box', T_BOX, $values, $errors, 'text', 'maxlength="20"', 'col-md-2 col-6');
            $this->field($type.'_postal_code', T_POSTAL_CODE.'&nbsp;*', $values, $errors, 'text', 'maxlength="20" autocomplete="postal-code"', 'col-md-3');
            $this->field($type.'_city', T_CITY.'&nbsp;*', $values, $errors, 'text', 'maxlength="100" autocomplete="address-level2"', 'col-md-5');
            ?>
            <div class="col-md-4"><div class="form-group">
                <label class="control-label" for="<?=$type?>_country"><?=T_COUNTRY?>&nbsp;*</label>
                <select class="custom-select" name="<?=$type?>_country" id="<?=$type?>_country">
                    <?php foreach(ModelAddress::COUNTRIES as $code => $name){ ?>
                    <option value="<?=$code?>" <?php if(($values[$type.'_country'] ?? 'BE') == $code) echo 'selected="selected"';?>><?=$name?></option>
                    <?php } ?>
                </select>
            </div></div>
        </div>
        <?php
    }


    /**
     * Checkout: sign in, personal data, delivery and postal addresses, summary of the order
     * @param array $values values of the fields
     * @param array $errors field => message
     * @param array $addresses addresses already saved by the user
     * @return void
     */
    public function checkout(ModelCart $cart, array $values, array $errors, array $addresses, bool $logged, array $message = null, int $passwordMinLength = 8){
        $this->message($message);
        $byType = array('delivery' => array(), 'postal' => array());
        foreach($addresses as $row){
            $byType[$row['type']][] = $row;
        }
        ?>
        <div class="row">
        <div class="col-lg-7 col-xl-8 mb-4">

            <?php if(!$logged){ ?>
            <section class="k-card k-signin-box">
                <div class="k-signin-box-intro">
                    <span class="k-signin-box-icon"><?=self::icon('person-circle', 22)?></span>
                    <div>
                        <h2 class="k-card-title"><?=T_ALREADY_CUSTOMER?></h2>
                        <p class="k-card-subtitle"><?=T_SIGN_IN_ADDRESSES?></p>
                    </div>
                </div>
                <form method="POST" action="<?=BASE_URL?>/index.php?component=shop&task=login">
                    <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
                    <div class="form-row">
                        <div class="col-md-5"><div class="form-group">
                            <label class="control-label" for="login_email"><?=T_EMAIL?></label>
                            <input required class="form-control" type="email" name="email" id="login_email" autocomplete="username"/>
                        </div></div>
                        <div class="col-md-4"><div class="form-group">
                            <label class="control-label" for="login_password"><?=T_PASSWORD?></label>
                            <input required class="form-control" type="password" name="password" id="login_password" autocomplete="current-password"/>
                        </div></div>
                        <div class="col-md-3 d-flex align-items-end"><div class="form-group w-100">
                            <button type="submit" class="btn btn-outline-primary btn-block"><?=T_SIGN_IN?></button>
                        </div></div>
                    </div>
                    <a class="k-link-small" href="<?=BASE_URL?>/index.php?component=user&task=forgot"><?=T_FORGOT_PASSWORD_Q?></a>
                </form>
            </section>
            <?php } ?>

            <form class="k-checkout-form" method="POST" action="<?=BASE_URL?>/index.php?component=shop&task=checkout" novalidate>
                <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />

                <fieldset class="k-card k-fieldset">
                    <legend class="k-fieldset-legend"><span class="k-fieldset-number">1</span> <?=$logged ? T_PERSONAL_DATA : T_PERSONAL_DATA_NEW?></legend>
                    <div class="row">
                        <?php
                        $this->field('first_name', T_FIRST_NAME.'&nbsp;*', $values, $errors, 'text', 'maxlength="100" autocomplete="given-name"');
                        $this->field('last_name', T_LAST_NAME.'&nbsp;*', $values, $errors, 'text', 'maxlength="100" autocomplete="family-name"');
                        $this->field('email', T_EMAIL.'&nbsp;*', $values, $errors, 'email', $logged ? 'disabled' : 'maxlength="255" autocomplete="email"');
                        $this->field('phone', T_PHONE, $values, $errors, 'tel', 'maxlength="30" autocomplete="tel"');
                        if(!$logged){
                            $this->field('password', T_PASSWORD.'&nbsp;* '.T_PASSWORD_TO_CREATE_ACCOUNT, $values, $errors, 'password', 'minlength="'.$passwordMinLength.'" maxlength="72" autocomplete="new-password"');
                            $this->field('password_confirmation', T_PASSWORD_CONFIRMATION.'&nbsp;*', $values, $errors, 'password', 'maxlength="72" autocomplete="new-password"');
                        }
                        ?>
                    </div>
                </fieldset>

                <fieldset class="k-card k-fieldset">
                    <legend class="k-fieldset-legend"><span class="k-fieldset-number">2</span> <?=T_DELIVERY_ADDRESS?></legend>
                    <?php $this->address('delivery', $values, $errors, $byType['delivery']); ?>
                </fieldset>

                <fieldset class="k-card k-fieldset">
                    <legend class="k-fieldset-legend"><span class="k-fieldset-number">3</span> <?=T_POSTAL_ADDRESS?></legend>
                    <div class="custom-control custom-checkbox mb-3">
                        <input class="custom-control-input" type="checkbox" name="postal_same" id="postal_same" value="1" <?php if(!empty($values['postal_same'])) echo 'checked';?>>
                        <label class="custom-control-label" for="postal_same"><?=T_SAME_AS_DELIVERY?></label>
                    </div>
                    <div id="postal_address" <?php if(!empty($values['postal_same'])) echo 'style="display:none"';?>>
                        <?php $this->address('postal', $values, $errors, $byType['postal']); ?>
                    </div>
                </fieldset>

                <div class="k-form-footer">
                    <a class="k-link-muted" href="<?=BASE_URL?>/index.php?component=shop&task=cart"><?=self::icon('arrow-left', 16)?> <?=T_BACK_TO_CART?></a>
                    <button type="submit" class="btn btn-accent btn-lg"><?=self::icon('lock', 18)?> <?=T_CONFIRM_ORDER?></button>
                </div>
            </form>
        </div>

        <div class="col-lg-5 col-xl-4">
            <aside class="k-card k-summary k-summary-sticky" aria-label="<?=T_YOUR_ORDER?>">
                <div class="k-summary-head">
                    <h2 class="k-card-title"><?=T_YOUR_ORDER?></h2>
                    <a class="k-link-small" href="<?=BASE_URL?>/index.php?component=shop&task=cart"><?=T_EDIT?></a>
                </div>
                <?php $this->lines($cart->items); ?>
                <?php $this->totals((int)$cart->subtotal, (int)$cart->tax, (int)$cart->total); ?>
            </aside>
        </div>
        </div>

        <script>
            // Fields of a new address: displayed only when "A new address" is chosen; postal address: only when it is different
            document.querySelectorAll('.address-choice').forEach(function(radio){
                radio.addEventListener('change', function(){
                    document.getElementById('new_address_' + radio.dataset.type).style.display = radio.value == 'new' ? '' : 'none';
                });
            });
            document.getElementById('postal_same').addEventListener('change', function(){
                document.getElementById('postal_address').style.display = this.checked ? 'none' : '';
            });
        </script>
        <?php
    }


    /**
     * Confirmation of the order
     * @param ModelOrder|null $order null if the order does not exist or belongs to somebody else
     * @param array $addresses delivery and postal => row of the table address
     * @return void
     */
    public function confirmation(?ModelOrder $order, array $addresses = array()){
        if($order === null){
            ?>
            <div class="k-empty">
                <div class="k-empty-icon"><?=self::icon('receipt', 28)?></div>
                <h2 class="k-empty-title"><?=T_ORDER_NOT_FOUND?></h2>
                <a class="btn btn-primary" href="<?=BASE_URL?>/index.php?component=product&task=viewlist"><?=T_BACK_TO_PRODUCTS?></a>
            </div>
            <?php
            return;
        }

        $subtotal = 0; $tax = 0;
        $items = array();
        foreach($order->items as $item){
            $subtotal += (int)$item['quantity'] * (int)$item['unit_price_amount'];
            $tax += (int)$item['tax_amount'];
            $items[] = array('name' => $item['product_name']) + $item;
        }
        ?>
        <div class="k-card k-confirmation">
            <div class="k-confirmation-icon"><?=self::icon('check-lg', 32)?></div>
            <h2 class="k-confirmation-title"><?=T_ORDER_THANKS?></h2>
            <p class="k-confirmation-number"><?=T_ORDER_NUMBER?> <strong><?=(int)$order->id_customer_order?></strong><?php if(($order->order['created_at'] ?? '') != '') echo ' &middot; '.$this->getDate((string)$order->order['created_at']);?></p>
        </div>

        <div class="row">
            <div class="col-lg-7 mb-4">
                <section class="k-card h-100">
                    <h2 class="k-card-title"><?=T_MENU_PRODUCT?></h2>
                    <?php $this->lines($items); ?>
                    <?php $this->totals($subtotal, $tax, $subtotal + $tax); ?>
                </section>
            </div>
            <div class="col-lg-5 mb-4">
                <?php foreach(array('delivery' => array(T_DELIVERY_ADDRESS, 'truck'), 'postal' => array(T_POSTAL_ADDRESS, 'envelope')) as $type => $label){
                    if(!isset($addresses[$type])) continue; ?>
                <section class="k-card k-address-card">
                    <h2 class="k-card-title"><?=self::icon($label[1], 18)?> <?=$label[0]?></h2>
                    <p><?=ModelAddress::line($addresses[$type])?><?php if(($addresses[$type]['phone'] ?? '') != '') echo '<br>'.$addresses[$type]['phone'];?></p>
                </section>
                <?php } ?>
                <a class="btn btn-primary btn-block" href="<?=BASE_URL?>/index.php?component=product&task=viewlist"><?=T_BACK_TO_PRODUCTS?></a>
            </div>
        </div>
        <?php
    }
}
