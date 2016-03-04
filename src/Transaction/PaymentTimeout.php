<?php

namespace OtpSimple\Transaction;

use OtpSimple\Config;
use OtpSimple\Transaction;

/**
 * Class PaymentTimeout
 * @package OtpSimple\Transaction
 * @property string $order_id
 * @property bool $user_cancel
 */
class PaymentTimeout extends Transaction
{
    protected static $_reverseMap = true;

    protected function _describeFields()
    {
        return [
            'order_id' => ['name'=>'order_ref', 'type'=>'simple','required'=>true],
            'user_cancel' => ['name'=>'redirect', 'type'=>'simple','required'=>true,'default'=>0],
        ];
    }

    public function __construct(Config $config)
    {
        parent::__construct($config);
        $this->setFields(self::renameFields($this->getFieldsMap(true), $this->config->getQuery()));
    }

}