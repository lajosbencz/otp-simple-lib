<?php


/** @var OtpSimple\Config $cfg */
$cfg = include __DIR__.'/boot.php';

//try {
    $lu = new OtpSimple\Transaction\LiveUpdate($cfg);
    $lu->automode = 1;
    $lu->method = OtpSimple\Enum\Method::AUTOMODE;
    $lu->order_id = md5(microtime(true).rand());
    $lu->order_date = date('Y-m-d H:i:s');
    $lu->language = OtpSimple\Enum\Language::HU;
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
    $lu->timeout_url.= '?order_ref='.$lu->order_id;
    $lu->redirect_url.= '?order_ref='.$lu->order_id;
    $lu->addProduct(new OtpSimple\Product([
        'name' => 'Lorem 1',
        'code' => 'sku0001',
        'info' => 'Lorem ipsum dolor sit amet',
        'price' => '30',
        'qty' => 2,
        'vat' => 0
    ]));
    $lu->addProduct(new OtpSimple\Product([
        'name' => 'Duis 2',
        'code' => 'sku0002',
        'info' => 'Duis aute (ÁRVÍZTŰRŐ TÜKÖRFÚRÓGÉP)',
        'price' => '51',
        'qty' => 3,
        'vat' => 0
    ]));
    $lu->addProduct(new OtpSimple\Product([
        'name' => 'Duis 2',
        'code' => 'sku0002',
        'info' => 'Duis aute (ÁRVÍZTŰRŐ TÜKÖRFÚRÓGÉP)',
        'price' => '51',
        'qty' => 1,
        'vat' => 0
    ]));
    if(!$lu->checkRequired()) {
        dump('missing:',$lu->getMissing());
        exit;
    }

    $form = $lu->createForm();
    $form->setId('otp_simple_'.md5(rand()));

    $title = 'OTP Simple Library for PHP';

    include 'header.php';

    echo $form->getHtml();
    echo $form->getButton('PAY', ['class' => 'btn btn-success btn-lg']);

    include 'footer.php';

    /*
} catch(Exception $e) {
    echo '<pre>';
    throw $e;
}*/