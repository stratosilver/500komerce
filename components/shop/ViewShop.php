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


    /**
     * Table of the lines of a cart or of an order, with the totals
     * @param array $items lines: name, quantity, unit_price_amount, line_total_amount
     * @param bool $editable quantities in a field and a button to remove the line
     * @return void
     */
    private function lines(array $items, int $subtotal, int $tax, int $total, bool $editable = false):void{
        ?>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th scope="col">Product</th>
                <th scope="col" class="text-right">Unit price</th>
                <th scope="col" class="text-right">Quantity</th>
                <th scope="col" class="text-right">Total excl. tax</th>
                <?php if($editable){ ?><th></th><?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach($items as $item){ ?>
            <tr>
                <td>
                    <?php if(isset($item['id_product'])){ ?>
                    <a href="<?=BASE_URL?>/index.php?component=product&task=view&id_product=<?=(int)$item['id_product']?>"><?=$item['name']?></a>
                    <?php } else { echo $item['name']; } ?>
                </td>
                <td class="text-right"><?=self::money((int)$item['unit_price_amount'])?></td>
                <td class="text-right">
                    <?php if($editable){ ?>
                    <input class="form-control form-control-sm d-inline-block text-right" style="width: 5em" type="number" min="0" max="<?=ModelCart::MAX_QUANTITY?>" step="1" name="quantity[<?=(int)$item['id_cart_item']?>]" value="<?=(int)$item['quantity']?>" aria-label="Quantity"/>
                    <?php } else { echo (int)$item['quantity']; } ?>
                </td>
                <td class="text-right"><?=self::money((int)$item['quantity'] * (int)$item['unit_price_amount'])?></td>
                <?php if($editable){ ?>
                <td class="shrink">
                    <button type="submit" name="remove" value="<?=(int)$item['id_cart_item']?>" class="btn btn-outline btn-outline-danger" title="Remove"><?=self::icon('x-lg')?></button>
                </td>
                <?php } ?>
            </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr><td colspan="3" class="text-right">Subtotal excl. tax</td><td class="text-right"><?=self::money($subtotal)?></td><?php if($editable){ ?><td></td><?php } ?></tr>
            <tr><td colspan="3" class="text-right">Tax</td><td class="text-right"><?=self::money($tax)?></td><?php if($editable){ ?><td></td><?php } ?></tr>
            <tr><td colspan="3" class="text-right"><strong>Total incl. tax</strong></td><td class="text-right"><strong><?=self::money($total)?></strong></td><?php if($editable){ ?><td></td><?php } ?></tr>
            </tfoot>
        </table>
        <?php
    }


    /**
     * The cart
     * @return void
     */
    public function cart(ModelCart $cart, array $message = null){
        $this->message($message);

        if(!count($cart->items)){
            $this->message(array('type' => 'info', 'text' => 'Your cart is empty'));
            ?>
            <a class="btn btn-outline btn-outline-primary" href="<?=BASE_URL?>/index.php?component=product&task=viewlist">See the products</a>
            <?php
            return;
        }
        ?>
        <form method="POST" action="<?=BASE_URL?>/index.php?component=shop&task=update">
            <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
            <?php $this->lines($cart->items, $cart->subtotal, $cart->tax, $cart->total, true); ?>
            <div class="d-flex justify-content-between flex-wrap">
                <div>
                    <a class="btn btn-outline btn-outline-secondary" href="<?=BASE_URL?>/index.php?component=product&task=viewlist">Continue shopping</a>
                    <button type="submit" class="btn btn-outline btn-outline-primary">Update the cart</button>
                </div>
                <a class="btn btn-success" href="<?=BASE_URL?>/index.php?component=shop&task=checkout">Checkout</a>
            </div>
        </form>
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
            <input class="form-control <?php if(isset($errors[$name])) echo 'is-invalid';?>" type="<?=$type?>" name="<?=$name?>" id="<?=$name?>" value="<?=$type == 'password' ? '' : ($values[$name] ?? '')?>" <?=$attributes?>/>
            <?php if(isset($errors[$name])){ ?><div class="invalid-feedback"><?=$errors[$name]?></div><?php } ?>
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
            <div class="form-group">
                <?php foreach($addresses as $row){ ?>
                <div class="form-check">
                    <input class="form-check-input address-choice" data-type="<?=$type?>" type="radio" name="id_address_<?=$type?>" id="address_<?=$type?>_<?=(int)$row['id_address']?>" value="<?=(int)$row['id_address']?>" <?php if($chosen == (string)$row['id_address']) echo 'checked';?>>
                    <label class="form-check-label" for="address_<?=$type?>_<?=(int)$row['id_address']?>"><?=ModelAddress::line($row)?></label>
                </div>
                <?php } ?>
                <div class="form-check">
                    <input class="form-check-input address-choice" data-type="<?=$type?>" type="radio" name="id_address_<?=$type?>" id="address_<?=$type?>_new" value="new" <?php if($chosen == 'new') echo 'checked';?>>
                    <label class="form-check-label" for="address_<?=$type?>_new">A new address</label>
                </div>
            </div>
            <?php
        }
        else{
            ?><input type="hidden" name="id_address_<?=$type?>" value="new"/><?php
        }
        ?>
        <div class="row" id="new_address_<?=$type?>" <?php if($chosen != 'new') echo 'style="display:none"';?>>
            <?php
            $this->field($type.'_street', 'Street&nbsp;*', $values, $errors, 'text', 'maxlength="200" autocomplete="address-line1"', 'col-md-8');
            $this->field($type.'_number', 'Number&nbsp;*', $values, $errors, 'text', 'maxlength="20"', 'col-md-2 col-6');
            $this->field($type.'_box', 'Box', $values, $errors, 'text', 'maxlength="20"', 'col-md-2 col-6');
            $this->field($type.'_postal_code', 'Postal code&nbsp;*', $values, $errors, 'text', 'maxlength="20" autocomplete="postal-code"', 'col-md-3');
            $this->field($type.'_city', 'City&nbsp;*', $values, $errors, 'text', 'maxlength="100" autocomplete="address-level2"', 'col-md-5');
            ?>
            <div class="col-md-4"><div class="form-group">
                <label class="control-label" for="<?=$type?>_country">Country&nbsp;*</label>
                <select class="form-control" name="<?=$type?>_country" id="<?=$type?>_country">
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
        <div class="col-lg-7">

            <?php if(!$logged){ ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h5 mt-0 mb-3">Already a customer? Sign in</h2>
                    <form method="POST" action="<?=BASE_URL?>/index.php?component=shop&task=login">
                        <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
                        <div class="row">
                            <div class="col-md-5"><div class="form-group">
                                <label class="control-label" for="login_email">Email</label>
                                <input required class="form-control" type="email" name="email" id="login_email" autocomplete="username"/>
                            </div></div>
                            <div class="col-md-4"><div class="form-group">
                                <label class="control-label" for="login_password">Password</label>
                                <input required class="form-control" type="password" name="password" id="login_password" autocomplete="current-password"/>
                            </div></div>
                            <div class="col-md-3 d-flex align-items-end"><div class="form-group w-100">
                                <button type="submit" class="btn btn-outline btn-outline-primary btn-block">Sign in</button>
                            </div></div>
                        </div>
                        <a href="<?=BASE_URL?>/index.php?component=user&task=forgot">Forgot password?</a>
                    </form>
                </div>
            </div>
            <?php } ?>

            <form method="POST" action="<?=BASE_URL?>/index.php?component=shop&task=checkout" novalidate>
                <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />

                <fieldset>
                    <legend><?=$logged ? 'Your personal data' : 'New customer: your personal data'?></legend>
                    <div class="row">
                        <?php
                        $this->field('first_name', 'First name&nbsp;*', $values, $errors, 'text', 'maxlength="100" autocomplete="given-name"');
                        $this->field('last_name', 'Last name&nbsp;*', $values, $errors, 'text', 'maxlength="100" autocomplete="family-name"');
                        $this->field('email', 'Email&nbsp;*', $values, $errors, 'email', $logged ? 'disabled' : 'maxlength="255" autocomplete="email"');
                        $this->field('phone', 'Phone', $values, $errors, 'tel', 'maxlength="30" autocomplete="tel"');
                        if(!$logged){
                            $this->field('password', 'Password&nbsp;* (to create your account)', $values, $errors, 'password', 'minlength="'.$passwordMinLength.'" maxlength="72" autocomplete="new-password"');
                            $this->field('password_confirmation', 'Password confirmation&nbsp;*', $values, $errors, 'password', 'maxlength="72" autocomplete="new-password"');
                        }
                        ?>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Delivery address</legend>
                    <?php $this->address('delivery', $values, $errors, $byType['delivery']); ?>
                </fieldset>

                <fieldset>
                    <legend>Postal address</legend>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="postal_same" id="postal_same" value="1" <?php if(!empty($values['postal_same'])) echo 'checked';?>>
                        <label class="form-check-label" for="postal_same">Same as the delivery address</label>
                    </div>
                    <div id="postal_address" <?php if(!empty($values['postal_same'])) echo 'style="display:none"';?>>
                        <?php $this->address('postal', $values, $errors, $byType['postal']); ?>
                    </div>
                </fieldset>

                <div class="form-group mt-3">
                    <a class="btn btn-outline btn-outline-secondary" href="<?=BASE_URL?>/index.php?component=shop&task=cart">Back to the cart</a>
                    <button type="submit" class="btn btn-success">Confirm the order</button>
                </div>
            </form>
        </div>

        <div class="col-lg-5">
            <h2 class="h5 mt-0 mb-3">Your order</h2>
            <?php $this->lines($cart->items, $cart->subtotal, $cart->tax, $cart->total); ?>
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
            $this->message(array('type' => 'warning', 'text' => 'Order not found'));
            return;
        }

        $subtotal = 0; $tax = 0;
        $items = array();
        foreach($order->items as $item){
            $subtotal += (int)$item['quantity'] * (int)$item['unit_price_amount'];
            $tax += (int)$item['tax_amount'];
            $items[] = array('name' => $item['product_name']) + $item;
        }

        $this->message(array('type' => 'success', 'text' => 'Thank you, your order n&deg; '.(int)$order->id_customer_order.' has been registered'));
        ?>
        <div class="row mb-3">
            <?php foreach(array('delivery' => 'Delivery address', 'postal' => 'Postal address') as $type => $label){
                if(!isset($addresses[$type])) continue; ?>
            <div class="col-md-6">
                <h2 class="h5"><?=$label?></h2>
                <p><?=ModelAddress::line($addresses[$type])?><?php if(($addresses[$type]['phone'] ?? '') != '') echo '<br>'.$addresses[$type]['phone'];?></p>
            </div>
            <?php } ?>
        </div>
        <?php
        $this->lines($items, $subtotal, $tax, $subtotal + $tax);
        ?>
        <a class="btn btn-outline btn-outline-primary" href="<?=BASE_URL?>/index.php?component=product&task=viewlist">Back to the products</a>
        <?php
    }
}
