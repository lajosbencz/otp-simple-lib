<?php

namespace OtpSimple\Transaction;

use OtpSimple\Config;
use OtpSimple\Exception\PaymentNotificationInvalidHashException;
use OtpSimple\Exception\PaymentNotificationUnsuccessfulException;
use OtpSimple\Transaction;

/**
 * Class PaymentNotification
 * @package OtpSimple\Transaction
 * @property string $saledate
 * @property string $refno
 * @property string $refnoext
 * @property string $orderno
 * @property string $orderstatus
 * @property string $paymethod
 * @property string $firstname
 * @property string $lastname
 * @property string $identity_no
 * @property string $identity_issuer
 * @property string $identity_cnp
 * @property string $company
 * @property string $registrationnumber
 * @property string $cbankname
 * @property string $cbankaccount
 * @property string $address1
 * @property string $address2
 * @property string $city
 * @property string $state
 * @property string $zipcode
 * @property string $country
 * @property string $phone
 * @property string $customeremail
 * @property string $firstname_d
 * @property string $lastname_d
 * @property string $company_d
 * @property string $address1_d
 * @property string $address2_d
 * @property string $city_d
 * @property string $zipcode_d
 * @property string $country_d
 * @property string $phone_d
 * @property string $ipaddress
 * @property string $currency
 * @property string[] $ipn_pid
 * @property string[] $ipn_pname
 * @property string[] $ipn_pcode
 * @property string[] $ipn_info
 * @property int[] $ipn_qty
 * @property float[] $ipn_price
 * @property float[] $ipn_vat
 * @property string[] $ipn_ver
 * @property float[] $ipn_discount
 * @property string[] $ipn_promoname
 * @property string[] $ipn_deliveredcodes
 * @property float[] $ipn_total
 * @property float $ipn_totalgeneral
 * @property float $ipn_shipping
 * @property float $ipn_commission
 * @property string $ipn_date
 * @property string $hash
 */
class PaymentNotification extends Transaction
{
    public static $successfulStatus = [
        "PAYMENT_AUTHORIZED",   //IPN
        "COMPLETE",             //IDN
        "REFUND",               //IRN
        "PAYMENT_RECEIVED",     //WIRE
        "CASH",                 //CASH
    ];

    protected function _getFields()
    {
        return [
            'saledate' => ['type'=>'simple'],
            'refno' => ['type'=>'simple'],
            'refnoext' => ['type'=>'simple'],
            'orderno' => ['type'=>'simple'],
            'orderstatus' => ['type'=>'simple'],
            'paymethod' => ['type'=>'simple'],
            'firstname' => ['type'=>'simple'],
            'lastname' => ['type'=>'simple'],
            'identity_no' => ['type'=>'simple'],
            'identity_issuer' => ['type'=>'simple'],
            'identity_cnp' => ['type'=>'simple'],
            'company' => ['type'=>'simple'],
            'registrationnumber' => ['type'=>'simple'],
            'cbankname' => ['type'=>'simple'],
            'cbankaccount' => ['type'=>'simple'],
            'address1' => ['type'=>'simple'],
            'address2' => ['type'=>'simple'],
            'city' => ['type'=>'simple'],
            'state' => ['type'=>'simple'],
            'zipcode' => ['type'=>'simple'],
            'country' => ['type'=>'simple'],
            'phone' => ['type'=>'simple'],
            'customeremail' => ['type'=>'simple'],
            'firstname_d' => ['type'=>'simple'],
            'lastname_d' => ['type'=>'simple'],
            'company_d' => ['type'=>'simple'],
            'address1_d' => ['type'=>'simple'],
            'address2_d' => ['type'=>'simple'],
            'city_d' => ['type'=>'simple'],
            'zipcode_d' => ['type'=>'simple'],
            'country_d' => ['type'=>'simple'],
            'phone_d' => ['type'=>'simple'],
            'ipaddress' => ['type'=>'simple'],
            'currency' => ['type'=>'simple'],
            'ipn_pid' => ['type'=>'array'],
            'ipn_pname' => ['type'=>'array'],
            'ipn_pcode' => ['type'=>'array'],
            'ipn_info' => ['type'=>'array'],
            'ipn_qty' => ['type'=>'array'],
            'ipn_price' => ['type'=>'array'],
            'ipn_vat' => ['type'=>'array'],
            'ipn_ver' => ['type'=>'array'],
            'ipn_discount' => ['type'=>'array'],
            'ipn_promoname' => ['type'=>'array'],
            'ipn_deliveredcodes' => ['type'=>'array'],
            'ipn_total' => ['type'=>'array'],
            'ipn_totalgeneral' => ['type'=>'simple'],
            'ipn_shipping' => ['type'=>'simple'],
            'ipn_commission' => ['type'=>'simple'],
            'ipn_date' => ['type'=>'simple'],
            'hash' => ['type'=>'simple'],
        ];
    }

    public function __construct(Config $config)
    {
        parent::__construct($config);
        $this->mergeFields($this->config->getPost());
    }

    public function checkResponse($throw=true) {
        if(!in_array(trim($this->orderstatus), self::$successfulStatus)) {
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