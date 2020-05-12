<?php

namespace OtpSimple\Component;


use OtpSimple\Component;
use OtpSimple\Exception;
use Phalcon\Http\Request;
use RuntimeException;

class Broker extends Component implements BrokerInterface
{
    protected $_baseUrl = '';
    protected $_timeout = 60;
    protected $_verify = true;
    protected $_lastInfo = [];

    public function __construct(string $baseUrl = '', int $timeout = 60, bool $verify = true)
    {
        $this->setBaseUrl($baseUrl);
        $this->_timeout = $timeout;
        $this->_verify = $verify;
    }

    public function setBaseUrl(string $baseUrl): void
    {
        $this->_baseUrl = trim($baseUrl);
    }

    /**
     * @param string $url
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function send(string $url, array $data): array
    {
        $url = $this->_baseUrl . $url;
        $json = $this->security->serialize($data);
        $hash = $this->security->sign($json);
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => 'curl',
            CURLOPT_TIMEOUT => $this->_timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER => true,
            CURLOPT_SSL_VERIFYPEER => $this->_verify,
            CURLOPT_SSL_VERIFYHOST => $this->_verify ? 2 : 0,
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => [
                'Accept-Language: en',
                'Content-Type: application/json',
                'Signature: ' . $hash,
            ],
        ]);
        $raw = curl_exec($curl);
        $this->log->debug('request sent', [
            'url' => $url,
            'raw' => $raw,
        ]);
        $this->_lastInfo = curl_getinfo($curl);
        if (!$raw) {
            $err = curl_error($curl);
            $code = curl_errno($curl);
            curl_close($curl);
            throw new RuntimeException($err, $code);
        }
        curl_close($curl);

        $raw = trim(preg_replace("/^HTTP\/[\d\.]+\s+[\d]+\s+Continue(\r\n)?/i", '', $raw));
        list($headers, $body) = explode("\r\n\r\n", $raw, 2);
        $headers = explode("\r\n", $headers);
        $body = trim($body);

        if (!$this->config->isSandbox()) {
            array_map('trim', $headers);
            $sig = null;
            foreach ($headers as $header) {
                if (strpos(strtolower($header), 'signature: ') === 0) {
                    $sig = trim(substr($header, strlen('signature: ')));
                }
            }
            if (!$sig) {
                throw new Exception\InvalidSignatureException;
            }
            $sigCheck = $this->security->sign($body);
            if ($sig !== $sigCheck) {
                throw new Exception\VerifySignatureException;
            }
        }

        try {
            $result = $this->security->deserialize($body);
        } catch (\Throwable $e) {
            $this->log->error($e->getMessage(), ['headers' => $headers, 'body' => $body]);
            throw $e;
        }

        if (array_key_exists('errorCodes', $result)) {
            throw new Exception\ApiException($result['errorCodes']);
        }
        return $result;
    }

    public function getTransferInfo(): array
    {
        return $this->_lastInfo;
    }
}
