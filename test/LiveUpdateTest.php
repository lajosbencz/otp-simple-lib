<?php

namespace OtpSimpleTest;

use OtpSimple\Config;

class LiveUpdateTest extends \PHPUnit_Framework_TestCase
{
    public function testOrderHash() {
        /** @var Config $config */
        $config = require dirname(__DIR__) . '/demo/config.php';

        $lu = new \OtpSimple\Transaction\LiveUpdate($config);
        $lu->automode = '1';
        $lu->order_id = '18822613371456920420';
        $lu->order_date = '2016-03-02 13:07:00';
        $lu->language = \OtpSimple\Enum\Language::HU;
        $lu->shipping = 20;
        $lu->discount = 30;
        $lu->bill_first_name = 'Payment';
        $lu->bill_last_name = 'Tester';
        $lu->bill_email = 'payment@tester.hu';
        $lu->bill_phone = '00/0000000';
        $lu->bill_country_code = 'HU';
        $lu->bill_state = 'State';
        $lu->bill_city = 'City';
        $lu->bill_address = 'First line address';
        $lu->bill_zip_code = '1234';
        $lu->timeout = 300;
        $lu->timeout_url = $config->getUrlTimeout().'?order_ref='.$lu->order_id;
        $lu->redirect_url = $config->getUrlRedirect().'?order_ref='.$lu->order_id;
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
        $this->assertEquals('26574e57a72d7199824333d792005fbd', $lu->hash);
    }
}
