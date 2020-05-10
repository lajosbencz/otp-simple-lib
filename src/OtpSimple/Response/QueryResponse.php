<?php

namespace OtpSimple\Response;


use Generator;
use OtpSimple\Entity\Transaction;
use OtpSimple\Response;

class QueryResponse extends Response
{
    protected $_transactions = [];

    public function process(array $data): void
    {
        parent::process($data);
        $this->_transactions = [];
        foreach ($data['transactions'] as $tx) {
            $this->_transactions[] = new Transaction($tx);
        }
    }

    public function countResults(): int
    {
        return count($this->_transactions);
    }

    /**
     * @return Generator|Transaction[]
     */
    public function getTransactions(): Generator
    {
        foreach ($this->_transactions as $tx) {
            yield $tx;
        }
    }

}
