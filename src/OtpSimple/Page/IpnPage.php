<?php

namespace OtpSimple\Page;


use OtpSimple\Exception;
use OtpSimple\Page;
use OtpSimple\Util;

class IpnPage extends Page
{
    public $salt = '';
    public $orderRef = '';
    public $method = '';
    public $merchant = '';
    public $finishDate = '';
    public $paymentDate = '';
    public $transactionId = 0;
    public $status = '';
    protected $_receiveDate;

    public function process(?string $jsonText = null, ?string $signature = null): self
    {
        if (!$jsonText) {
            $jsonText = file_get_contents("php://input");
        }
        if (!$signature) {
            $headers = Util::getServerRequestHeaders();
            $this->log->debug('IPN headers', $headers);
            $signature = $headers['Signature'];
        }
        $this->log->debug('processing IPN', ['raw' => $jsonText, 'signature' => $signature]);
        if ($signature !== $this->security->sign($jsonText)) {
            throw new Exception\VerifySignatureException;
        }
        $this->_receiveDate = date('c');
        Util::copyFromArray($this, $this->security->deserialize($jsonText));
        $this->log->debug('IPN received', $this->toArray());
        return $this;
    }

    public function getResponseData(): array
    {
        $r = $this->toArray();
        $r['receiveDate'] = $this->_receiveDate;
        return $r;
    }

    public function getResponseBody(): string
    {
        return $this->security->serialize($this->getResponseData());
    }

    public function confirm(): void
    {
        $body = $this->getResponseBody();
        header('Content-Type: application/json; charset=utf-8');
        header('Signature: ' . $this->security->sign($body));
        echo $body;
        $this->log->debug('confirmed IPN', $this->getResponseData());
        exit;
    }
}
