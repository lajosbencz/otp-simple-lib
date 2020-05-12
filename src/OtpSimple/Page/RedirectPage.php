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

    public function process(?array $dataSource = null): self
    {
        if (!$dataSource) {
            $dataSource = $_GET;
        }
        $json = base64_decode($dataSource['r']);
        $data = $this->security->deserialize($json);
        $sig = $this->security->sign($json);
        $this->log->debug('returned from bank page', ['data' => $data, 'signature' => $sig]);
        if ($sig !== $dataSource['s']) {
            throw new Exception\VerifySignatureException;
        }
        $this->responseCode = $data['r'];
        $this->transactionId = $data['t'];
        $this->event = $data['e'];
        $this->merchant = $data['m'];
        $this->orderRef = $data['o'];

        return $this;
    }
}
