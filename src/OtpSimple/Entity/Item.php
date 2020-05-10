<?php

namespace OtpSimple\Entity;

use OtpSimple\Util;

class Item
{
    public $ref;
    public $title;
    public $description = '';
    public $amount = 1;
    public $price = 0;
    public $tax = 0;

    public function __construct(string $ref, string $title, float $price, int $amount = 1, string $description = '', float $tax = 0.)
    {
        $this->ref = $ref;
        $this->title = $title;
        $this->price = $price;
        $this->amount = $amount;
        $this->description = $description;
        $this->tax = $tax;
    }

    public function addAmount(int $amount): void
    {
        $this->amount += $amount;
    }

    public function toArray(): array
    {
        return Util::objectToArray($this);
    }

}
