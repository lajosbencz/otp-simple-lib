<?php


/** @var \OtpSimple\Config $cfg */
$cfg = include __DIR__.'/boot.php';

try {
    $tx = new \OtpSimple\Transaction\LiveUpdate($cfg);
    $tx['automode'] = '1';
    $tx['pay_method'] = \OtpSimple\Enum\Method::AUTOMODE;
    $tx['order_ref'] = md5(microtime(true).rand());
    $tx['order_date'] = date('Y-m-d H:i:s');
    $tx['language'] = \OtpSimple\Enum\Language::HU;
    $tx['order_shipping'] = 20;
    $tx['discount'] = 30;
    $tx['bill_fname'] = 'Payment';
    $tx['bill_lname'] = 'Tester';
    $tx['bill_email'] = 'payment@tester.hu';
    $tx['bill_phone'] = '00/0000000';
    $tx['bill_countrycode'] = 'HU';
    $tx['bill_state'] = 'State';
    $tx['bill_city'] = 'City';
    $tx['bill_address'] = 'First line address';
    $tx['bill_zipcode'] = '1234';
    $tx['order_timeout'] = 300;
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
    echo $e->getMessage();
}