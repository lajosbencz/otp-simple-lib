<?php

namespace OtpSimple\Entity;


class ItemCollection
{
    /** @var Item[] */
    protected $_items = [];

    public function add(Item $item): self
    {
        if (isset($this->_items[$item->ref])) {
            $this->_items[$item->ref]->addAmount($item->amount);
        } else {
            $this->_items[$item->ref] = $item;
        }
        return $this;
    }

    public function remove(Item $item): self
    {
        unset($this->_items[$item->ref]);
        return $this;
    }

    public function clear(): self
    {
        $this->_items = [];
        return $this;
    }

    public function count(): int
    {
        return count($this->_items);
    }

    public function sum(): float
    {
        $sum = 0;
        foreach ($this->_items as $item) {
            $sum += $item->amount * $item->price;
        }
        return $sum;
    }

    public function toArray(): array
    {
        $r = [];
        foreach ($this->_items as $i) {
            $r[] = $i->toArray();
        }
        return $r;
    }
}
