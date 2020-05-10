<?php

class ContainerTest extends PHPUnit\Framework\TestCase
{
    public function testContainer()
    {
        $c = new OtpSimple\Container;
        $c->set('foo', function () {
            return 'bar';
        });
        $this->assertTrue($c->has('foo'));
        $this->assertEquals('bar', $c->get('foo'));
    }

    public function testContainerErrors()
    {
        $n = 'n/a';
        $c = new OtpSimple\Container;
        $this->expectExceptionObject(new OtpSimple\Exception\ContainerException('unknown service: ' . $n));
        $c->get($n);
    }

    public function testContainerShared()
    {
        $c = new OtpSimple\Container;
        $s1 = 0;
        $c->set('s1', function () use (&$s1) {
            $s1++;
            return 's1=' . $s1;
        }, true);
        $this->assertEquals('s1=1', $c->get('s1'));
        $this->assertEquals('s1=1', $c->get('s1'));
        $this->assertEquals('s1=1', $c->get('s1'));

        $s2 = 0;
        $c->set('s2', function () use (&$s2) {
            $s2++;
            return 's2=' . $s2;
        }, false);
        $this->assertEquals('s2=1', $c->get('s2'));
        $this->assertEquals('s2=2', $c->get('s2'));
        $this->assertEquals('s2=3', $c->get('s2'));
    }
}
