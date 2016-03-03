<?php

namespace OtpSimple\Transaction;

use OtpSimple\Config;
use OtpSimple\Exception;
use OtpSimple\Transaction;

/**
 * Class PaymentBack
 * @package OtpSimple\Transaction
 * @property string $date
 * @property string $order_ref
 * @property string $payrefno
 * @property string $rt
 * @property string $rc
 */
class PaymentBack extends Transaction
{
    protected function _getFields()
    {
        return [
            'date' => ['type' => 'simple'],
            'order_ref' => ['type' => 'simple'],
            'payrefno' => ['type' => 'simple'],
            'rt' => ['type' => 'simple'],
            'rc' => ['type' => 'simple'],
            '3dsecure' => ['type' => 'simple'],
        ];
    }

    public function __construct(Config $config)
    {
        parent::__construct($config);
        $this->setFields($_GET);
    }

    public function checkCtrl()
    {
        $url = $this->_config->getHostName().substr($this->config->getServer('REQUEST_URI'),1);
        $url = substr($url, 0, -38); //the last 38 characters are the CTRL param
        $hash = $this->config->getMerchant()->hash([$url]);
        if($this->config->getQuery('ctrl') == $hash) {
            return true;
        }
        return false;
    }

    public function getRtString() {
        $s = explode('|', $this->rt);
        return trim($s[1]);
    }

    protected function checkRtVariable()
    {
        if(!$this->isFieldSet('rt')) {
            return false;
        }
        if($this['rt'] == "No Response" || $this['rt'] == "NR" || $this['rc'] == '') {
            return false;
        }
        //000 and 001, or App are successful
        if(in_array(substr($this['rt'], 0, 3), ["000", "001", "App"])) {
            return true;
        }
        return true;
    }

    public function checkResponse($throw=true) {
        if(!$this['order_ref']) {
            if(!$throw) {
                return false;
            }
            throw new Exception\LiveUpdateOrderRefMissingException("Missing order_ref");
        }
        if(!$this->checkCtrl()) {
            if(!$throw) {
                return false;
            }
            throw new Exception\LiveUpdateInvalidCtrlException("Invalid ctrl");
        }
        if(!$this->checkRtVariable()) {
            if(!$throw) {
                return false;
            }
            throw new Exception\LiveUpdateUnsuccessfulException("Unsuccessful response code: ".$this->getRtString());
        }
        return true;
    }

}