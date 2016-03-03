<?php

namespace OtpSimple\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Html extends StreamHandler
{
    public function __construct($level = Logger::DEBUG, $bubble = true, $filePermission = null, $useLocking = false)
    {
        parent::__construct("php://output", $level, $bubble, $filePermission, $useLocking);
    }

    public function write(array $record)
    {
        $record['formatted'].= '<br/>';
        parent::write($record);
    }
}