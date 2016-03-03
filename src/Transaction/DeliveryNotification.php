<?php

namespace OtpSimple\Transaction;

use OtpSimple\Transaction;

class DeliveryNotification extends Transaction
{
    protected function _getFields()
    {
        return [];
    }

    protected function _nameData($data = [])
    {
        return $data;
    }

}