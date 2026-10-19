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
        echo '<main id="main" class="flex-grow-1 py-4 py-lg-5"><div class="container">';
        ?>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <h1 class="h2 fw-bold mb-0"><?=ucfirst($title)?></h1>
            <ol class="list-unstyled d-flex flex-wrap align-items-center gap-2 mb-0" aria-label="<?=T_STEPS_LABEL?>">
                <?php $n = 0; foreach(self::STEPS as $step => $label){
                    $badge = $n < $current ? 'text-bg-success' : ($n == $current ? 'text-bg-primary' : 'bg-body text-body-secondary border'); ?>
                <li class="d-flex align-items-center gap-2 <?=$n == $current ? 'fw-semibold' : 'text-body-secondary'?>" <?php if($n == $current) echo 'aria-current="step"';?>>
                    <span class="badge rounded-pill <?=$badge?>"><?=$n < $current ? self::icon('check-lg', 12, '0 0 16 16', 'align-baseline') : $n + 1?></span>
                    <span class="small"><?=$label?></span>
                    <?php if($n < count(self::STEPS) - 1){ ?><span class="d-inline-flex text-body-tertiary mx-1"><?=self::icon('chevron-right', 12)?></span><?php } ?>
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
     * Image of a line of a cart or of an order
     * @param int $size pixels
     * @return string
     */
    private static function lineImage(string $url, int $size):string{
        if($url != ''){
            return '<img class="d-block rounded-3 border bg-body object-fit-cover" width="'.$size.'" height="'.$size.'" src="'.htmlspecialchars($url).'" alt="" loading="lazy">';
        }
        return '<span class="d-flex rounded-3 border bg-body-secondary text-body-tertiary p-3">'.self::icon('image', $size - 34).'</span>';
    }


    /**
     * Amounts of a cart or of an order
     * @return void
     */
    private function totals(int $subtotal, int $tax, int $total):void{
        ?>
        <dl class="mb-0">
            <div class="d-flex justify-content-between gap-3 py-1"><dt class="fw-normal text-body-secondary"><?=T_SUBTOTAL_EXCL_TAX?></dt><dd class="mb-0 text-nowrap"><?=self::money($subtotal)?></dd></div>
            <div class="d-flex justify-content-between gap-3 py-1"><dt class="fw-normal text-body-secondary"><?=sprintf(T_VAT, self::taxRateText())?></dt><dd class="mb-0 text-nowrap"><?=self::money($tax)?></dd></div>
            <div class="d-flex justify-content-between align-items-baseline gap-3 border-top pt-3 mt-2"><dt><?=T_TOTAL_INCL_TAX?></dt><dd class="mb-0 fs-4 fw-bold text-nowrap"><?=self::money($total)?></dd></div>
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
        <ul class="list-unstyled mb-3">
            <?php foreach($items as $item){
                $thumb = $thumbnails[(int)($item['id_product'] ?? 0)] ?? ''; ?>
            <li class="d-flex align-items-center gap-3 py-2 border-bottom">
                <span class="position-relative flex-shrink-0">
                    <?=self::lineImage($thumb, 56)?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-secondary"><span class="visually-hidden"><?=T_QUANTITY?> </span><?=(int)$item['quantity']?></span>
                </span>
                <span class="flex-grow-1 small fw-semibold"><?=$item['name']?></span>
                <span class="text-nowrap fw-semibold"><?=self::money((int)$item['quantity'] * (int)$item['unit_price_amount'])?></span>
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
            self::emptyState('bag', T_CART_EMPTY, T_CART_EMPTY_HINT, array(BASE_URL.'/index.php?component=product&task=viewlist', T_SEE_PRODUCTS));
            return;
        }
        $thumbnails = $this->thumbnails($cart->items);
        $count = 0;
        foreach($cart->items as $item){ $count += (int)$item['quantity']; }
        ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <form class="card shadow-sm overflow-hidden" method="POST" action="<?=BASE_URL?>/index.php?component=shop&task=update">
                    <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
                    <!-- Default button: the key Enter in a quantity updates the cart (instead of removing the first line) -->
                    <button type="submit" class="visually-hidden" tabindex="-1" aria-hidden="true"><?=T_UPDATE_CART?></button>

                    <div class="card-header bg-body py-3 px-4">
                        <h2 class="h5 fw-bold mb-0"><?=sprintf($count > 1 ? T_ITEMS : T_ITEMS_ONE, $count)?></h2>
                    </div>

                    <ul class="list-group list-group-flush">
                        <?php foreach($cart->items as $item){
                            $idItem = (int)$item['id_cart_item'];
                            $url = BASE_URL.'/index.php?component=product&task=view&id_product='.(int)$item['id_product'];
                            $thumb = $thumbnails[(int)$item['id_product']] ?? ''; ?>
                        <li class="list-group-item py-3 px-4">
                            <div class="row g-3 align-items-center">
                                <div class="col-auto">
                                    <a href="<?=$url?>" tabindex="-1" aria-hidden="true"><?=self::lineImage($thumb, 72)?></a>
                                </div>
                                <div class="col">
                                    <a class="fw-semibold link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=$url?>"><?=$item['name']?></a>
                                    <div class="small text-body-secondary"><?=sprintf(T_PRICE_PER_UNIT, self::money((int)$item['unit_price_amount']))?></div>
                                </div>
                                <div class="w-100 d-md-none"></div>
                                <div class="col-4 col-md-2">
                                    <label class="visually-hidden" for="quantity_<?=$idItem?>"><?=sprintf(T_QUANTITY_OF, $item['name'])?></label>
                                    <input class="form-control text-center" type="number" min="0" max="<?=ModelCart::MAX_QUANTITY?>" step="1" name="quantity[<?=$idItem?>]" id="quantity_<?=$idItem?>" value="<?=(int)$item['quantity']?>" inputmode="numeric"/>
                                </div>
                                <div class="col col-md-2 text-end fw-bold text-nowrap"><?=self::money((int)$item['quantity'] * (int)$item['unit_price_amount'])?></div>
                                <div class="col-auto">
                                    <button type="submit" name="remove" value="<?=$idItem?>" class="btn btn-sm btn-outline-danger" title="<?=T_REMOVE?>" aria-label="<?=sprintf(T_REMOVE_ITEM, $item['name'])?>"><?=self::icon('trash3', 16)?></button>
                                </div>
                            </div>
                        </li>
                        <?php } ?>
                    </ul>

                    <div class="card-footer bg-body d-flex flex-wrap align-items-center justify-content-between gap-2 py-3 px-4">
                        <a class="icon-link link-secondary link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=product&task=viewlist"><?=self::icon('arrow-left', 16)?> <?=T_CONTINUE_SHOPPING?></a>
                        <button type="submit" class="btn btn-outline-primary d-inline-flex align-items-center gap-2"><?=self::icon('arrow-repeat', 16)?> <?=T_UPDATE_CART?></button>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <aside class="card shadow-sm" aria-label="<?=T_SUMMARY?>">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3"><?=T_SUMMARY?></h2>
                        <?php $this->totals((int)$cart->subtotal, (int)$cart->tax, (int)$cart->total); ?>
                        <a class="btn btn-warning btn-lg fw-semibold w-100 d-flex align-items-center justify-content-center gap-2 mt-4" href="<?=BASE_URL?>/index.php?component=shop&task=checkout"><?=T_CHECKOUT?> <?=self::icon('arrow-right', 18)?></a>
                        <p class="small text-body-secondary d-flex align-items-center gap-2 mt-3 mb-0"><?=self::icon('bag-check', 16)?> <?=T_NO_ACCOUNT_NEEDED?></p>
                    </div>
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
        <div class="<?=$col?>">
            <label class="form-label" for="<?=$name?>"><?=$label?></label>
            <input class="form-control <?php if(isset($errors[$name])) echo 'is-invalid';?>" type="<?=$type?>" name="<?=$name?>" id="<?=$name?>" value="<?=$type == 'password' ? '' : ($values[$name] ?? '')?>" <?=$attributes?> <?php if(isset($errors[$name])) echo 'aria-invalid="true" aria-describedby="'.$name.'_error"';?>/>
            <?php if(isset($errors[$name])){ ?><div class="invalid-feedback" id="<?=$name?>_error"><?=$errors[$name]?></div><?php } ?>
        </div>
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
            $choices = array();
            foreach($addresses as $row){
                $choices[(string)(int)$row['id_address']] = array('geo-alt', ModelAddress::line($row));
            }
            $choices['new'] = array('plus-lg', T_NEW_ADDRESS);
            ?>
            <div class="list-group mb-3" role="radiogroup" aria-label="<?=$type == 'delivery' ? T_DELIVERY_ADDRESS : T_POSTAL_ADDRESS?>">
                <?php foreach($choices as $value => $choice){ ?>
                <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3" for="address_<?=$type?>_<?=$value?>">
                    <input class="form-check-input flex-shrink-0 mt-0" data-address-choice="<?=$type?>" type="radio" name="id_address_<?=$type?>" id="address_<?=$type?>_<?=$value?>" value="<?=$value?>" <?php if($chosen == $value) echo 'checked';?>>
                    <span class="d-inline-flex text-body-secondary"><?=self::icon($choice[0], 16)?></span>
                    <span><?=$choice[1]?></span>
                </label>
                <?php } ?>
            </div>
            <?php
        }
        else{
            ?><input type="hidden" name="id_address_<?=$type?>" value="new"/><?php
        }
        ?>
        <div class="row g-3 <?php if($chosen != 'new') echo 'd-none';?>" id="new_address_<?=$type?>">
            <?php
            $this->field($type.'_street', T_STREET.'&nbsp;*', $values, $errors, 'text', 'maxlength="200" autocomplete="address-line1"', 'col-md-8');
            $this->field($type.'_number', T_NUMBER.'&nbsp;*', $values, $errors, 'text', 'maxlength="20"', 'col-md-2 col-6');
            $this->field($type.'_box', T_BOX, $values, $errors, 'text', 'maxlength="20"', 'col-md-2 col-6');
            $this->field($type.'_postal_code', T_POSTAL_CODE.'&nbsp;*', $values, $errors, 'text', 'maxlength="20" autocomplete="postal-code"', 'col-md-3');
            $this->field($type.'_city', T_CITY.'&nbsp;*', $values, $errors, 'text', 'maxlength="100" autocomplete="address-level2"', 'col-md-5');
            ?>
            <div class="col-md-4">
                <label class="form-label" for="<?=$type?>_country"><?=T_COUNTRY?>&nbsp;*</label>
                <select class="form-select" name="<?=$type?>_country" id="<?=$type?>_country">
                    <?php foreach(ModelAddress::COUNTRIES as $code => $name){ ?>
                    <option value="<?=$code?>" <?php if(($values[$type.'_country'] ?? 'BE') == $code) echo 'selected="selected"';?>><?=$name?></option>
                    <?php } ?>
                </select>
            </div>
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
        $legend = 'fs-5 fw-bold d-flex align-items-center gap-2 mb-4';
        ?>
        <div class="row g-4">
        <div class="col-lg-7 col-xl-8">

            <?php if(!$logged){ ?>
            <section class="card shadow-sm border-primary-subtle mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <span class="d-inline-flex rounded-circle bg-primary-subtle text-primary p-2"><?=self::icon('person-circle', 22)?></span>
                        <div>
                            <h2 class="h5 fw-bold mb-1"><?=T_ALREADY_CUSTOMER?></h2>
                            <p class="small text-body-secondary mb-0"><?=T_SIGN_IN_ADDRESSES?></p>
                        </div>
                    </div>
                    <form method="POST" action="<?=BASE_URL?>/index.php?component=shop&task=login">
                        <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label" for="login_email"><?=T_EMAIL?></label>
                                <input required class="form-control" type="email" name="email" id="login_email" autocomplete="username"/>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="login_password"><?=T_PASSWORD?></label>
                                <input required class="form-control" type="password" name="password" id="login_password" autocomplete="current-password"/>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-outline-primary w-100"><?=T_SIGN_IN?></button>
                            </div>
                        </div>
                        <a class="d-inline-block small mt-3" href="<?=BASE_URL?>/index.php?component=user&task=forgot"><?=T_FORGOT_PASSWORD_Q?></a>
                    </form>
                </div>
            </section>
            <?php } ?>

            <form method="POST" action="<?=BASE_URL?>/index.php?component=shop&task=checkout" novalidate>
                <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />

                <fieldset class="card card-body shadow-sm p-4 mb-4">
                    <legend class="<?=$legend?>"><span class="badge rounded-pill text-bg-primary">1</span> <?=$logged ? T_PERSONAL_DATA : T_PERSONAL_DATA_NEW?></legend>
                    <div class="row g-3">
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

                <fieldset class="card card-body shadow-sm p-4 mb-4">
                    <legend class="<?=$legend?>"><span class="badge rounded-pill text-bg-primary">2</span> <?=T_DELIVERY_ADDRESS?></legend>
                    <?php $this->address('delivery', $values, $errors, $byType['delivery']); ?>
                </fieldset>

                <fieldset class="card card-body shadow-sm p-4 mb-4">
                    <legend class="<?=$legend?>"><span class="badge rounded-pill text-bg-primary">3</span> <?=T_POSTAL_ADDRESS?></legend>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="postal_same" id="postal_same" value="1" <?php if(!empty($values['postal_same'])) echo 'checked';?>>
                        <label class="form-check-label" for="postal_same"><?=T_SAME_AS_DELIVERY?></label>
                    </div>
                    <div id="postal_address" class="<?php if(!empty($values['postal_same'])) echo 'd-none';?>">
                        <?php $this->address('postal', $values, $errors, $byType['postal']); ?>
                    </div>
                </fieldset>

                <div class="d-flex flex-wrap-reverse align-items-center justify-content-between gap-3">
                    <a class="icon-link link-secondary link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=shop&task=cart"><?=self::icon('arrow-left', 16)?> <?=T_BACK_TO_CART?></a>
                    <button type="submit" class="btn btn-warning btn-lg fw-semibold d-inline-flex align-items-center gap-2"><?=self::icon('lock', 18)?> <?=T_CONFIRM_ORDER?></button>
                </div>
            </form>
        </div>

        <div class="col-lg-5 col-xl-4">
            <aside class="card shadow-sm" aria-label="<?=T_YOUR_ORDER?>">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                        <h2 class="h5 fw-bold mb-0"><?=T_YOUR_ORDER?></h2>
                        <a class="small" href="<?=BASE_URL?>/index.php?component=shop&task=cart"><?=T_EDIT?></a>
                    </div>
                    <?php $this->lines($cart->items); ?>
                    <?php $this->totals((int)$cart->subtotal, (int)$cart->tax, (int)$cart->total); ?>
                </div>
            </aside>
        </div>
        </div>

        <script>
            // Fields of a new address: displayed only when "A new address" is chosen; postal address: only when it is different
            document.querySelectorAll('[data-address-choice]').forEach(function(radio){
                radio.addEventListener('change', function(){
                    document.getElementById('new_address_' + radio.dataset.addressChoice).classList.toggle('d-none', radio.value != 'new');
                });
            });
            document.getElementById('postal_same').addEventListener('change', function(){
                document.getElementById('postal_address').classList.toggle('d-none', this.checked);
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
            self::emptyState('receipt', T_ORDER_NOT_FOUND, '', array(BASE_URL.'/index.php?component=product&task=viewlist', T_BACK_TO_PRODUCTS));
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
        <div class="card shadow-sm text-center p-4 p-lg-5 mb-4">
            <div class="d-inline-flex rounded-circle text-bg-success mx-auto mb-3 p-3"><?=self::icon('check-lg', 32)?></div>
            <h2 class="h3 fw-bold"><?=T_ORDER_THANKS?></h2>
            <p class="text-body-secondary mb-0"><?=T_ORDER_NUMBER?> <strong class="text-body"><?=(int)$order->id_customer_order?></strong><?php if(($order->order['created_at'] ?? '') != '') echo ' &middot; '.$this->getDate((string)$order->order['created_at']);?></p>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <section class="card shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3"><?=T_MENU_PRODUCT?></h2>
                        <?php $this->lines($items); ?>
                        <?php $this->totals($subtotal, $tax, $subtotal + $tax); ?>
                    </div>
                </section>
            </div>
            <div class="col-lg-5 d-flex flex-column gap-4">
                <?php foreach(array('delivery' => array(T_DELIVERY_ADDRESS, 'truck'), 'postal' => array(T_POSTAL_ADDRESS, 'envelope')) as $type => $label){
                    if(!isset($addresses[$type])) continue; ?>
                <section class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h6 fw-bold d-flex align-items-center gap-2 mb-2"><span class="d-inline-flex text-primary"><?=self::icon($label[1], 18)?></span> <?=$label[0]?></h2>
                        <p class="mb-0"><?=ModelAddress::line($addresses[$type])?><?php if(($addresses[$type]['phone'] ?? '') != '') echo '<br>'.$addresses[$type]['phone'];?></p>
                    </div>
                </section>
                <?php } ?>
                <a class="btn btn-primary w-100" href="<?=BASE_URL?>/index.php?component=product&task=viewlist"><?=T_BACK_TO_PRODUCTS?></a>
            </div>
        </div>
        <?php
    }
}
