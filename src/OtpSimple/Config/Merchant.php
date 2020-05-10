<?php

namespace OtpSimple\Config;


class Merchant
{
    /** @var string */
    public $id;

    /** @var string */
    public $key;

    /** @var string */
    public $currency;

    public function __construct(string $id, string $key, string $currency)
    {
        $this->id = $id;
        $this->key = $key;
        $this->currency = $currency;
    }
}
