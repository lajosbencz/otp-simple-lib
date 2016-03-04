<?php


namespace OtpSimpleTest;

use OtpSimple\Object;

/**
 * Class ObjectTestMock
 * @package OtpSimpleTest
 * @property string $f1
 * @property array $f2
 * @property array $f3
 */
class ObjectTestMock extends Object {
    public function describeFields()
    {
        return [
            'f1' => ['type'=>'scalar','name'=>'field1','required'=>true],
            'f2' => ['type'=>'array','set'=>false],
            'f3' => ['type'=>'array','get'=>false],
        ];
    }

}

class ObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testObject() {

        $o = new ObjectTestMock;

    }
}
