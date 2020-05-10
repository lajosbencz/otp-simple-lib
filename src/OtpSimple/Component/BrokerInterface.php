<?php

namespace OtpSimple\Component;


interface BrokerInterface
{
    function setBaseUrl(string $baseUrl): void;

    function send(string $url, array $data): array;

    public function getTransferInfo(): array;
}
