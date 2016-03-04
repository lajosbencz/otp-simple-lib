<?php


namespace OtpSimpleTest;


use OtpSimple\Transaction;

class TransactionTest extends \PHPUnit_Framework_TestCase
{
    public function renameFieldsProvider() {
        return [
            [
                // $map
                [
                    'ORDER_REF' => 'order_id',
                    'ORDER_PCODE' => 'product_code',
                ],
                // $array
                [
                    'ORDER_REF' => '123',
                    'ORDER_PCODE' => [10,11,12],
                ],
                // $expected
                [
                    'order_id' => '123',
                    'product_code' => [10,11,12],
                ],
            ],

            [
                // $map
                [
                    ['x1'=>'y1','x2'=>['y2'=>['x3'=>'y3']]],
                ],
                // $array
                [
                    ['x1'=>1,'x2'=>['x3'=>2]],
                    ['x1'=>3,'x2'=>['x3'=>4]],
                ],
                // $expect
                [
                    ['y1'=>1,'y2'=>['y3'=>2]],
                    ['y1'=>3,'y2'=>['y3'=>4]],
                ],
            ],

            [
                // $map
                [
                    'f1'=>'g1',
                    'f2'=>[
                        'g2'=>[
                            's1'=>'t1',
                            's2'=>'t2',
                        ],
                    ],
                    'f3'=>[
                        'g3'=>[
                            [
                                'u1' => 'v1',
                                'u2' => 'v2',
                            ],
                        ],
                    ],
                ],
                // $array
                [
                    'f1' => 1,
                    'f2' => [
                        's1' => 2,
                        's2' => 3,
                    ],
                    'f3' => [
                        ['u1'=>4,'u2'=>5,'u3'=>6],
                        ['u1'=>7,'u2'=>8,'u3'=>9],
                    ],
                    'f4' => 10,
                ],
                // $expected
                [
                    'g1' => 1,
                    'g2' => [
                        't1' => 2,
                        't2' => 3,
                    ],
                    'g3' => [
                        ['v1'=>4,'v2'=>5,'u3'=>6],
                        ['v1'=>7,'v2'=>8,'u3'=>9],
                    ],
                    'f4' => 10,
                ],
            ],
        ];
    }

    /**
     * @dataProvider renameFieldsProvider
     * @param $map
     * @param $array
     * @param $expected
     * @outputBuffering disabled
     */
    public function testRenameFields($map, $array, $expected) {
        $result = Transaction::renameFields($map, $array);
        $this->assertEquals($expected, $result);
    }
}
