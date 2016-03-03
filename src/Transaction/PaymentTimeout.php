<?php

namespace OtpSimple\Transaction;

use OtpSimple\Config;
use OtpSimple\Transaction;

/**
 * Class PaymentTimeout
 * @package OtpSimple\Transaction
 * @property string $order_ref
 * @property bool $redirect
 */
class PaymentTimeout extends Transaction
{
    protected function _getFields()
    {
        return [
            'order_ref' => ['type'=>'simple','required'=>true],
            'redirect' => ['type'=>'simple','default'=>0],
        ];
    }

    public function __construct(Config $config)
    {
        parent::__construct($config);
        $this->order_ref = $this->config->getQuery('order_ref');
        $this->redirect = intval($this->config->getQuery('redirect')) > 0 ? true : false;
    }

}