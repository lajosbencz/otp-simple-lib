<?php

namespace OtpSimple;


use ReflectionClass;

abstract class Request extends Component
{
    public function __construct(?Container $container = null)
    {
        if ($container) {
            $this->setContainer($container);
        }
    }

    abstract public function getApiUrl(): string;

    final public function getResponseClass(): string
    {
        static $class;
        if (!isset($class)) {
            $ref = new ReflectionClass($this);
            $class = Response::class . '\\' . substr($ref->getShortName(), 0, -7) . 'Response';
        }
        return $class;
    }

    public function getData(): array
    {
        return [
            'sdkVersion' => Config::SDK_VERSION,
            'salt' => $this->security->salt(),
            'merchant' => $this->config->getMerchant()->id,
        ];
    }

    public function send(): Response
    {
        $requestData = $this->getData();
        $this->log->debug('sending request', [
            'url' => $this->getApiUrl(),
            'data' => $requestData,
        ]);
        $responseClass = $this->getResponseClass();
        $responseData = $this->broker->send($this->getApiUrl(), $requestData);
        return new $responseClass($this, $responseData);
    }
}
