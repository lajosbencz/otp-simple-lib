<?php

namespace OtpSimpleTest;

class LiveUpdateTest extends \PHPUnit_Framework_TestCase
{
    public function testOrderHash() {
        
        $config = require dirname(__DIR__) . '/payments/config.php';

        $lu = new \OtpSimple\Transaction\LiveUpdate($config);
        $lu['automode'] = '1';
        $lu['order_ref'] = '18822613371456920420';
        $lu['order_date'] = '2016-03-02 13:07:00';
        $lu['language'] = \OtpSimple\Enum\Language::HU;
        $lu['order_shipping'] = 20;
        $lu['discount'] = 30;
        $lu['bill_fname'] = 'Foo';
        $lu['bill_lname'] = 'Bar';
        $lu['bill_email'] = 'foo@bar.em';
        $lu['bill_phone'] = '00/0000000';
        $lu['bill_countrycode'] = 'HU';
        $lu['bill_state'] = 'N/A';
        $lu['bill_city'] = 'N/A';
        $lu['bill_address'] = 'N/A';
        $lu['bill_zipcode'] = '0000';
        $lu['timeout_url'] = $config->getUrlTimeout().'?order_ref='.$lu['order_ref'];
        $lu['back_ref'] = $config->getUrlBack().'?order_ref='.$lu['order_ref'];
        $lu->addProduct(new \OtpSimple\Product([
            'name' => 'Lorem 1',
            'code' => 'sku0001',
            'info' => 'Lorem ipsum dolor sit amet',
            'price' => '30',
            'qty' => 2,
            'vat' => 0
        ]));
        $lu->addProduct(new \OtpSimple\Product([
            'name' => 'Duis 2',
            'code' => 'sku0002',
            'info' => 'Duis aute (ÁRVÍZTŰRŐ TÜKÖRFÚRÓGÉP)',
            'price' => '51',
            'qty' => 3,
            'vat' => 0
        ]));

        $this->assertEquals(true, $lu->checkRequired(), 'Missing fields: '.join(', ',$lu->getMissing()));
        $this->assertEquals('26574e57a72d7199824333d792005fbd', $lu['order_hash']);
    }
}
