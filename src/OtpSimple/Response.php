<?php

namespace OtpSimple;


abstract class Response extends Component
{
    protected $_request;
    protected $_responseData = [];

    public function __construct(Request $request, array $data)
    {
        $this->_request = $request;
        $this->_responseData = $data;
        $this->process($data);
        $this->log->debug('received response', [
            'url' => $this->getRequest()->getApiUrl(),
            'data' => $this->getResponseData(),
        ]);
    }

    public function getRequest(): Request
    {
        return $this->_request;
    }

    public function getResponseData(): array
    {
        return $this->_responseData;
    }

    public function process(array $data): void
    {
        Util::copyFromArray($this, $data);
    }

    public function toArray(): array
    {
        return Util::objectToArray($this);
    }
}
