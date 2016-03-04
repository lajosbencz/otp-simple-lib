<?php

namespace OtpSimple\Transaction;

use OtpSimple\Config;
use OtpSimple\Exception\PaymentNotificationInvalidHashException;
use OtpSimple\Exception\PaymentNotificationUnsuccessfulException;
use OtpSimple\Transaction;

/**
 * Class PaymentNotification
 * @package OtpSimple\Transaction
 * @property string $date
 * @property string $sales_id
 * @property string $order_id
 * @property string $order_no
 * @property string $status
 * @property string $method
 * @property string $bill_first_name
 * @property string $bill_last_name
 * @property string $identity_no
 * @property string $identity_issuer
 * @property string $identity_cnp
 * @property string $bill_company
 * @property string $bill_registration_number
 * @property string $bank_name
 * @property string $bank_account
 * @property string $address
 * @property string $address2
 * @property string $city
 * @property string $state
 * @property string $zip_code
 * @property string $country
 * @property string $phone
 * @property string $email
 * @property string $delivery_first_name
 * @property string $delivery_last_name
 * @property string $delivery_company
 * @property string $delivery_address
 * @property string $delivery_address2
 * @property string $delivery_city
 * @property string $delivery_zip_code
 * @property string $delivery_country
 * @property string $delivery_phone
 * @property string $ip_address
 * @property string $currency
 * @property string[] $product_id
 * @property string[] $product_name
 * @property string[] $product_code
 * @property string[] $product_info
 * @property int[] $product_qty
 * @property float[] $product_price
 * @property float[] $product_vat
 * @property string[] $product_ver
 * @property float[] $product_discount
 * @property string[] $product_promoname
 * @property string[] product_delivered_codes
 * @property float[] $product_total
 * @property float $total_general
 * @property float $shipping
 * @property float $commission
 * @property string $order_date
 * @property string $hash
 */
class PaymentNotification extends Transaction
{
    protected static $_reverseMap = true;

    public static $successfulStatus = [
        "PAYMENT_AUTHORIZED",   //IPN
        "COMPLETE",             //IDN
        "REFUND",               //IRN
        "PAYMENT_RECEIVED",     //WIRE
        "CASH",                 //CASH
    ];

    public function __construct(Config $config)
    {
        parent::__construct($config);
        $this->_data = self::renameFields($this->getFieldsMap(), $this->config->getRequest(), false);
    }

    protected function _describeFields()
    {
        return [
            'date' => ['name'=>'SALEDATE','type'=>'simple'],
            'sales_id' => ['name'=>'REFNO','type'=>'simple'],
            'order_id' => ['name'=>'REFNOEXT','type'=>'simple'],
            'order_no' => ['name'=>'ORDERNO','type'=>'simple'],
            'status' => ['name'=>'ORDERSTATUS','type'=>'simple'],
            'method' => ['name'=>'PAYMETHOD','type'=>'simple'],
            'bill_first_name' => ['name'=>'FIRSTNAME','type'=>'simple'],
            'bill_last_name' => ['name'=>'LASTNAME','type'=>'simple'],
            'identity_no' => ['name'=>'IDENTITY_NO','type'=>'simple'],
            'identity_issuer' => ['name'=>'IDENTITY_ISSUER','type'=>'simple'],
            'identity_cnp' => ['name'=>'IDENTITY_CNP','type'=>'simple'],
            'bill_company' => ['name'=>'COMPANY','type'=>'simple'],
            'bill_registration_number' => ['name'=>'REGISTRATIONNUMBER','type'=>'simple'],
            'bank_name' => ['name'=>'CBANKNAME','type'=>'simple'],
            'bank_account' => ['name'=>'CBANKACCOUNT','type'=>'simple'],
            'bill_address' => ['name'=>'ADDRESS1','type'=>'simple'],
            'bill_address2' => ['name'=>'ADDRESS2','type'=>'simple'],
            'bill_city' => ['name'=>'CITY','type'=>'simple'],
            'bill_state' => ['name'=>'STATE','type'=>'simple'],
            'bill_zip_code' => ['name'=>'ZIPCODE','type'=>'simple'],
            'bill_country' => ['name'=>'COUNTRY','type'=>'simple'],
            'bill_phone' => ['name'=>'PHONE','type'=>'simple'],
            'bill_email' => ['name'=>'CUSTOMEREMAIL','type'=>'simple'],
            'delivery_first_name' => ['name'=>'FIRSTNAME_D','type'=>'simple'],
            'delivery_last_name' => ['name'=>'LASTNAME_D','type'=>'simple'],
            'delivery_company' => ['name'=>'COMPANY_D','type'=>'simple'],
            'delivery_address' => ['name'=>'ADDRESS1_D','type'=>'simple'],
            'delivery_address2' => ['name'=>'ADDRESS2_D','type'=>'simple'],
            'delivery_city' => ['name'=>'CITY_D','type'=>'simple'],
            'delivery_zip_code' => ['name'=>'ZIPCODE_D','type'=>'simple'],
            'delivery_country' => ['name'=>'COUNTRY_D','type'=>'simple'],
            'delivery_phone' => ['name'=>'PHONE_D','type'=>'simple'],
            'ip_address' => ['name'=>'IPADDRESS','type'=>'simple'],
            'currency' => ['name'=>'CURRNCY','type'=>'simple'],
            'product_id' => ['name'=>'IPN_PID','type'=>'array'],
            'product_name' => ['name'=>'IPN_PNAME','type'=>'array'],
            'product_code' => ['name'=>'IPN_PCODE','type'=>'array'],
            'product_info' => ['name'=>'IPN_INFO','type'=>'array'],
            'product_qty' => ['name'=>'INP_QTY','type'=>'array'],
            'product_price' => ['name'=>'IPN_PRICE','type'=>'array'],
            'product_ver' => ['name'=>'IPN_VER','type'=>'array'],
            'product_vat' => ['name'=>'IPN_VAT','type'=>'array'],
            'discount' => ['name'=>'IPN_DISCOUNT','type'=>'array'],
            'promo_name' => ['name'=>'IPN_PROMONAME','type'=>'array'],
            'delivered_codes' => ['name'=>'IPN_DELIVEREDCODES','type'=>'array'],
            'total' => ['name'=>'IPN_TOTAL','type'=>'array'],
            'total_general' => ['name'=>'IPN_TOTALGENERAL','type'=>'simple'],
            'shipping' => ['name'=>'IPN_SHIPPING','type'=>'simple'],
            'commission' => ['name'=>'IPN_COMMISSION','type'=>'simple'],
            'order_date' => ['name'=>'IPN_DATE','type'=>'simple'],
            'hash' => ['name'=>'HASH','type'=>'simple'],
        ];
    }

    public function checkResponse($throw=true) {
        if(!in_array(trim($this->status), self::$successfulStatus)) {
            if(!$throw) {
                return false;
            }
            throw new PaymentNotificationUnsuccessfulException('Unknown error');
        }
        $data = $this->config->getPost();
        unset($data['HASH']);
        $hash = $this->config->getMerchant()->hash($data);
        if($hash == $this->hash) {
            return true;
        }
        if(!$throw) {
            return false;
        }
        throw new PaymentNotificationInvalidHashException('Unknown error');
    }

    public function confirm()
    {
        $date = date("YmdHis");
        $post = $this->config->getPost();
        $hash = $this->config->getMerchant()->hash([
            $post['IPN_PID'][0],
            $post['IPN_PNAME'][0],
            $post['IPN_DATE'],
            $date
        ]);
        echo '<EPAYMENT>'.$date.'|'.$hash.'</EPAYMENT>';
    }
}