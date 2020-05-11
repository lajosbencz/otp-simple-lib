<?php

namespace OtpSimple\Response;


use Generator;
use OtpSimple\Entity\Transaction;
use OtpSimple\Request;
use OtpSimple\Response;

class QueryResponse extends Response
{
    /**
     * @var Transaction[]
     */
    protected $_transactions = [];

    /**
     * @return Request\QueryRequest
     */
    public function getRequest(): Request
    {
        return parent::getRequest();
    }

    public function process(array $data): void
    {
        parent::process($data);
        $this->_transactions = [];
        foreach ($data['transactions'] as $tx) {
            $this->_transactions[$tx['orderRef']] = new Transaction($tx);
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

    public function getTransaction(string $orderRef): Transaction
    {
        if (!array_key_exists($orderRef, $this->_transactions)) {
            throw new \InvalidArgumentException('invalid orderRef');
        }
        return $this->_transactions[$orderRef];
    }

}
