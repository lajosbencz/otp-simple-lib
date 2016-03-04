<?php

namespace OtpSimple\Transaction;

use OtpSimple\Config;
use OtpSimple\Exception;
use OtpSimple\Transaction;

/**
 * Class PaymentBack
 * @package OtpSimple\Transaction
 * @property string $date
 * @property string $order_id
 * @property string $payment_id
 * @property string $text
 * @property string $code
 * @property string $secure
 */
class PaymentRedirect extends Transaction
{
    protected static $_reverseMap = true;

    protected function _describeFields()
    {
        // TODO: Implement _describeFields() method.
    }


    public function __construct(Config $config)
    {
        parent::__construct($config);
        $this->setFields(self::renameFields($this->getFieldsMap(true), $this->config->getQuery()));
    }

    public function describeFields()
    {
        return [
            'date' => ['name'=>'date', 'type' => 'scalar'],
            'order_id' => ['name'=>'order_ref', 'type' => 'scalar'],
            'payment_id' => ['name'=>'payrefno', 'type' => 'scalar'],
            'text' => ['name'=>'RT', 'type' => 'scalar'],
            'code' => ['name'=>'RC', 'type' => 'scalar'],
            'secure' => ['name'=>'3dsecure', 'type' => 'scalar'],
        ];
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

    public function getText() {
        $s = explode('|', $this->text);
        return trim($s[1]);
    }

    protected function checkRtVariable()
    {
        if(!$this->isFieldSet('text')) {
            return false;
        }
        if($this->text == "No Response" || $this->text == "NR" || $this->code == '') {
            return false;
        }
        //000 and 001, or App are successful
        if(in_array(substr($this->code, 0, 3), ["000", "001", "App"])) {
            return true;
        }
        return true;
    }

    public function checkResponse($throw=true) {
        if(!$this->order_id) {
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
            throw new Exception\LiveUpdateUnsuccessfulException("Unsuccessful response code: ".$this->getText());
        }
        return true;
    }

}