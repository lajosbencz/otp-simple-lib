<?php

use PHPUnit\Framework\TestCase;

class OtpSimpleTest extends TestCase
{
    protected $otp;
    protected $proc;

    public function setUp(): void
    {
        $config = new OtpSimple\Config([
            'sandbox' => true,
            'merchants' => [
                'HUF' => [
                    'id' => getenv('MERCHANT_ID'),
                    'key' => getenv('MERCHANT_KEY'),
                ],
            ],
        ]);
        $this->otp = new OtpSimple\OtpSimple($config);
    }

    public function tearDown(): void
    {
    }

    public function testStart()
    {
//        $currency = $this->otp->config->getDefaultCurrency();
//        $this->assertIsString($currency);
        $start = $this->otp->start();
        $start->twoStep = false;
        $start->orderRef = 'test-' . uniqid(time() . '-');
        $start->total = 123;
        $start->customer = 'foo bar';
        $start->email = 'foo@bar.co';
        $start->setRedirectTimeout(10);
        $start->setRedirectUrl('http://localhost:8080/back.php');
        $res = $start->send();
        $this->assertIsString($res->paymentUrl);
        $this->assertGreaterThan(0, strlen($res->paymentUrl));
        $this->assertArrayHasKey('paymentUrl', $res->getResponseData());
        echo $res->paymentUrl, PHP_EOL;

        $query = $this->otp->query()->addTransactionIds($res->transactionId);
        $response = $query->send();
        $this->assertEquals(1, $response->countResults());
        foreach ($response->getTransactions() as $tx) {
            $this->assertInstanceOf(OtpSimple\Entity\Transaction::class, $tx);
            $this->assertEquals($res->transactionId, $tx->transactionId);
            $this->assertEquals(OtpSimple\Enum\TransactionStatus::INIT, $tx->status);
            $this->assertEquals(0, $tx->resultCode);
        }
    }

    public function testQuery()
    {
        $txId = '10287769';
        $query = $this->otp->query()->addTransactionIds($txId);
        $response = $query->send();
        $this->assertEquals(1, $response->countResults());
        foreach ($response->getTransactions() as $tx) {
            $this->assertInstanceOf(OtpSimple\Entity\Transaction::class, $tx);
            $this->assertEquals($txId, $tx->transactionId);
            $this->assertEquals(OtpSimple\Enum\TransactionStatus::FINISHED, $tx->status);
            $this->assertEquals('OK', $tx->resultCode);
        }
    }
}
