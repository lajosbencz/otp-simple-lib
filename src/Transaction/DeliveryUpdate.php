<?php

namespace OtpSimple\Transaction;

use OtpSimple\Exception;
use OtpSimple\RequestInterface;
use OtpSimple\Transaction;

/**
 * Class DeliveryUpdate
 * @package OtpSimple\Transaction
 */
class DeliveryUpdate extends Transaction
{
    protected static $_reverseMap = false;

    protected function _getHashFields() {
        return ['merchant_id','payment_id','amount','currency','date'];
    }

    protected function _describeFields()
    {
        return [
            'merchant_id' => ['name'=>'MERCHANT','type'=>'scalar','required'=>true],
            'payment_id' => ['name'=>'ORDER_REF','type'=>'scalar','required'=>true],
            'amount' => ['name'=>'ORDER_AMOUNT','type'=>'scalar','required'=>true],
            'currency' => ['name'=>'ORDER_CURRENCY','type'=>'scalar','required'=>true],
            'date' => ['name'=>'IDN_DATE','type'=>'scalar','required'=>true,'default'=>date('Y-m-d H:i:s')],
            'hash' => ['name'=>'ORDER_HASH','type'=>'scalar','required'=>true],
            'redirect_url' => ['name'=>'REF_URL','type'=>'scalar'],
        ];
    }

    public function createHash() {
        return $this->config->getMerchant()->hash($this->_data, $this->_getHashFields());
    }

    public function send() {
        $c = $this->config->getRequestClass();
        /** @var RequestInterface $request */
        $request = new $c;
        $request->setUrl($this->config->getUriIdn(), $this->getData());
        $result = $request->send();
        if(!$this->__isset('redirect_url')) {
            if(preg_match('/\<EPAYMENT\>(.*?)\<\/EPAYMENT\>/', $result, $matches)) {
                $matches = explode('|',$matches[1]);
                $hash = $this->config->getMerchant()->hash([$matches[0],$matches[1],$matches[2],$matches[3]]);
                if($hash!=$matches[4]) {
                    throw new Exception('Invalid control hash');
                }
                return [
                    'payment_id' => $matches[0],
                    'code' => $matches[1],
                    'text' => $matches[2],
                    'date' => $matches[3],
                    'hash' => $matches[4],
                ];
            }
        }
        return null;
    }

}