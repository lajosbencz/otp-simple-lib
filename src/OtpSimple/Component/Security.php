<?php

namespace OtpSimple\Component;


use Exception;
use OtpSimple\Component;
use OtpSimple\Config;

class Security extends Component
{
    public function serialize(array $data): string
    {
        return json_encode($data);
    }

    public function deserialize(string $data): array
    {
        return json_decode($data, true);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function salt()
    {
        return bin2hex(random_bytes(Config::SALT_LENGTH));
    }

    /**
     * @param string $data
     * @return string
     */
    public function sign(string $data): string
    {
        return base64_encode(hash_hmac(Config::HASH_ALGORITHM, $data, $this->config->getMerchant()->key, true));
    }
}
