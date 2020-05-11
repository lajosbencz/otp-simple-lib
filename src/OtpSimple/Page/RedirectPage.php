<?php

namespace OtpSimple\Page;


use OtpSimple\Exception;
use OtpSimple\Page;

class RedirectPage extends Page
{
    public $responseCode = 0;
    public $transactionId = 0;
    public $event = '';
    public $merchant = '';
    public $orderRef = '';

    public function __construct(?array $dataSource = null)
    {
        if (!$dataSource) {
            $dataSource = $_GET;
        }
        $json = base64_decode($dataSource['r']);
        $sig = $this->security->sign($json);
        if ($sig !== $dataSource['s']) {
            throw new Exception\VerifySignatureException;
        }
        $data = $this->security->deserialize($json);
        $this->responseCode = $data['r'];
        $this->transactionId = $data['t'];
        $this->event = $data['e'];
        $this->merchant = $data['m'];
        $this->orderRef = $data['o'];

        $this->log->debug('returned from bank page', $this->toArray());
    }
}
