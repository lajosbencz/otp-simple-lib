<?php

namespace OtpSimple\Transaction;

use OtpSimple\Config;
use OtpSimple\Transaction;

/**
 * Class RefundNotification
 * @package OtpSimple\Transaction
 * @property string $merchant_id
 * @property string $payment_id
 * @property float $total
 * @property float $amount
 * @property string $currency
 * @property string $date
 * @property string $url_redirect
 * @property string[] $product_code
 * @property int[] $product_qty
 */
class RefundNotification extends Transaction
{
    protected static $_reverseMap = true;

    protected function _getHashFields() {
        return [ 'merchant_id', 'payment_id', 'total', 'currency', 'date', 'product_code', 'product_qty', 'amount'];
    }

    protected function _describeFields()
    {
        return [
            'merchant_id' => ['name'=>'MERCHANT',"type" => "scalar", "required" => true],
            'payment_id' => ['name'=>'ORDER_REF',"type" => "scalar", "required" => true],
            'total' => ['name'=>'ORDER_AMOUNT',"type" => "scalar", "required" => true],
            'amount' => ['name'=>'AMOUNT',"type" => "scalar", "required" => true],
            'currency' => ['name'=>'ORDER_CURRENCY',"type" => "scalar", "required" => true],
            'date' => ['name'=>'IRN_DATE',"type" => "scalar", "required" => true],
            'url_redirect' => ['name'=>'REF_URL',"type" => "scalar"],
            'product_code' => ['name'=>'ORDER_PCODE',"type" => "array"],
            'product_qty' => ['name'=>'ORDER_QTY',"type" => "array"],
        ];
    }

    public function __construct(Config $config)
    {
        parent::__construct($config);
        $this->merchant_id = $config->getMerchant()->getId();
        $this->currency = $config->getMerchant()->getCurrency();
        $this->date = date('Y-m-d H:i:s');
        $this->_data = self::renameFields($this->getFieldsMap(), $this->config->getRequest(), false);
    }

    public function checkResponse($throw=true) {

    }
}