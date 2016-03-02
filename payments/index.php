<?php


$cfg = include __DIR__.'/boot.php';

try {

    $product = new \OtpSimple\Product;
    $product->name = 'Test #1';
    $product->code = 'sku_000000';
    $product->price = 24000;
    //$product->qty = 2;

    $tx = new \OtpSimple\Transaction\LiveUpdate($cfg);
    $tx['automode'] = '1';
    $tx['order_ref'] = '18822613371456920420';
    $tx['order_date'] = '2016-03-02 13:07:00';
    $tx['language'] = \OtpSimple\Enum\Language::HU;
    $tx['order_shipping'] = 20;
    $tx['discount'] = 30;
    $tx['bill_fname'] = 'Foo';
    $tx['bill_lname'] = 'Bar';
    $tx['bill_email'] = 'foo@bar.em';
    $tx['bill_phone'] = '00/0000000';
    $tx['bill_countrycode'] = 'HU';
    $tx['bill_state'] = 'N/A';
    $tx['bill_city'] = 'N/A';
    $tx['bill_address'] = 'N/A';
    $tx['bill_zipcode'] = '0000';
    $tx['timeout_url'] = $cfg->getUrlTimeout().'?order_ref='.$tx['order_ref'];
    $tx['back_ref'] = $cfg->getUrlBack().'?order_ref='.$tx['order_ref'];
    $tx->addProduct(new \OtpSimple\Product([
        'name' => 'Lorem 1',
        'code' => 'sku0001',
        'info' => 'Lorem ipsum dolor sit amet',
        'price' => '30',
        'qty' => 2,
        'vat' => 0
    ]));
    $tx->addProduct(new \OtpSimple\Product([
        'name' => 'Duis 2',
        'code' => 'sku0002',
        'info' => 'Duis aute (ÁRVÍZTŰRŐ TÜKÖRFÚRÓGÉP)',
        'price' => '51',
        'qty' => 3,
        'vat' => 0
    ]));
    if(!$tx->checkRequired()) {
        dump($tx->getMissing());
        exit;
    }

    $form = $tx->createForm();
    $form->setId('otp_simple_'.md5(rand()));

    $title = 'OTP Simple Library for PHP';

    include 'header.php';

    echo $form->getHtml();
    echo $form->getButton('PAY', ['class' => 'btn btn-success btn-lg']);

    include 'footer.php';
} catch(Exception $e) {
    echo '<pre>';
    throw $e;
}