<?php

namespace OtpSimple\Request;


use OtpSimple\Request;
use OtpSimple\Response;

class QueryRequest extends Request
{
    protected $_orderRefs = [];
    protected $_transactionIds = [];

    public function getApiUrl(): string
    {
        return '/query';
    }

    /**
     * @return Response\QueryResponse
     */
    public function send(): Response
    {
        return parent::send();
    }

    public function addOrderRefs(string ...$refs): self
    {
        foreach ($refs as $i) {
            if (in_array($i, $this->_orderRefs)) {
                continue;
            }
            $this->_orderRefs[] = $i;
        }
        return $this;
    }

    public function addTransactionIds(string ...$ids): self
    {
        foreach ($ids as $i) {
            if (in_array($i, $this->_transactionIds)) {
                continue;
            }
            $this->_transactionIds[] = $i;
        }
        return $this;
    }

    public function getData(): array
    {
        $data = parent::getData();
        $data['transactionIds'] = $this->_transactionIds;
        $data['orderRefs'] = $this->_orderRefs;
        return $data;
    }
}
