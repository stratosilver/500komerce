<?php
/**
 * API of the component customerorder: same operations as ControllerCustomerOrder, same model, JSON instead of views.
 * See api.php for the way to call it.
 */

declare(strict_types=1);
namespace Apgenic\CustomerOrder;

class ControllerApiCustomerOrder extends \Apgenic\Classes\ControllerApi {

    const MODEL = ModelCustomerOrder::class;
    const PK = array('id_customer_order');
    const PK_AUTO = true;
    const SOFT_DELETE = false;
    const AUTO_DATES = array();

    // Fields saved by ModelCustomerOrder
    const FIELDS = array(
        'id_user'              => array('type' => 'string', 'null' => false, 'required' => true, 'max' => 45),
    );

    // Fields of the table `customer_order` in the answers. The status is read only here: it is changed in the
    // back office, by \Apgenic\Shop\ModelOrder::setStatus() that puts the stock back when an order is cancelled
    public static array $fieldsNames = array('id_customer_order', 'id_user', 'total_amount', 'status', 'created_at');

}
