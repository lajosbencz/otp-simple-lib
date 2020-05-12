<?php

namespace OtpSimple\Component;


use OtpSimple\Component;
use OtpSimple\Exception;
use Phalcon\Http\Request;
use RuntimeException;

class Broker extends Component implements BrokerInterface
{
    protected $_curl;
    protected $_baseUrl = '';

    public function __construct(string $baseUrl = '', int $timeout = 60, bool $verify = true)
    {
        $this->_curl = curl_init();
        curl_setopt_array($this->_curl, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => 'curl',
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER => true,
            CURLOPT_SSL_VERIFYPEER => $verify,
            CURLOPT_SSL_VERIFYHOST => $verify ? 2 : 0,
        ]);
        $this->setBaseUrl($baseUrl);
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
        curl_setopt_array($this->_curl, [
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => [
                'Accept-Language: en',
                'Content-Type: application/json',
                'Signature: ' . $hash,
            ],
        ]);
        $raw = curl_exec($this->_curl);
        $this->log->debug('request sent', [
            'url' => $url,
            'raw' => $raw,
        ]);
        if (!$raw) {
            throw new RuntimeException(curl_error($this->_curl), curl_errno($this->_curl));
        }

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
        }

        if (array_key_exists('errorCodes', $result)) {
            throw new Exception\ApiException($result['errorCodes']);
        }
        return $result;
    }

    public function getTransferInfo(): array
    {
        return curl_getinfo($this->_curl) ?: [];
    }

    public function __destruct()
    {
        curl_close($this->_curl);
    }
}
