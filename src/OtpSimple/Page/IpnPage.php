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

    public function __construct(?string $jsonText = null, ?string $signature = null)
    {
        if (!$jsonText) {
            $jsonText = file_get_contents("php://input");
        }
        if (!$signature) {
            $headers = Util::getServerRequestHeaders();
            $signature = $headers['signature'];
        }
        if ($signature !== $this->security->sign($jsonText)) {
            throw new Exception\VerifySignatureException;
        }
        $this->log->debug('IPN received from bank', $this->toArray());
    }

    public function confirm(): void
    {
        $r = $this->toArray();
        $r['receiveDate'] = date('c');
        $this->log->debug('IPN confirmed', $r);
        header('Content-Type: application/json; charset=utf-8');
        echo $this->security->serialize($r);
    }
}
