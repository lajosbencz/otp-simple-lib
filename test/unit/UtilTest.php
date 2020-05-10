<?php

use OtpSimple\Util;
use PHPUnit\Framework\TestCase;

class _pubProt
{
    public $pub = 'pub';
    protected $prot = 'prot';
}

class UtilTest extends TestCase
{
    public function testObjectToArray()
    {
        $o = new _pubProt;
        $a = Util::objectToArray($o);
        $this->assertIsArray($a);
        $this->assertArrayNotHasKey('prot', $a);
        $this->assertArrayHasKey('pub', $a);

        $o = new OtpSimple\Config\Merchant('1', '123', 'HUF');
        $o->foobar = [];
        $o->foobar[] = new OtpSimple\Config\Merchant('1', '123', 'HUF');
        $a = Util::objectToArray($o);
        $this->assertIsArray($a);
        $this->assertEquals([
            'id' => $o->id,
            'key' => $o->key,
            'currency' => $o->currency,
            'foobar' => [
                [
                    'id' => $o->id,
                    'key' => $o->key,
                    'currency' => $o->currency,
                ],
            ],
        ], $a);
    }

    public function testCopyFromArray()
    {
        $o = new OtpSimple\Entity\Item('123', 'foo bar', 123);
        Util::copyFromArray($o, ['ref' => '456']);
        $this->assertEquals('456', $o->ref);
    }
}
