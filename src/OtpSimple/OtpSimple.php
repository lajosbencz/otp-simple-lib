<?php

namespace OtpSimple;


use OtpSimple\Page\IpnPage;
use OtpSimple\Page\RedirectPage;
use OtpSimple\Request\FinishRequest;
use OtpSimple\Request\QueryRequest;
use OtpSimple\Request\RefundRequest;
use OtpSimple\Request\StartRequest;
use Psr\Log\NullLogger;

class OtpSimple implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function __construct(Config $config)
    {
        $container = new Container;
        $container->set('config', function () use ($config) {
            return $config;
        });
        $container->set('log', function () {
            return new NullLogger;
        });
        $container->set('broker', function () use ($config) {
            return new Component\Broker($config->getBaseUrl(), $config->request_timeout, !$config->isSandbox());
        });
        $container->set('security', function () {
            return new Component\Security;
        });
        $this->setContainer($container);
    }

    public function start(string $orderRef, string $email, float $total): StartRequest
    {
        $req = new StartRequest($this->getContainer());
        $req->orderRef = $orderRef;
        $req->email = $email;
        $req->total = $total;
        return $req;
    }

    public function finish(string $orderRef, float $originalTotal, ?float $approvedTotal = null)
    {
        $req = new FinishRequest($this->getContainer());
        $req->orderRef = $orderRef;
        $req->originalTotal = $originalTotal;
        $req->approvedTotal = $approvedTotal ?? $originalTotal;
        return $req;
    }

    public function refund(string $orderRef, float $refundTotal)
    {
        $req = new RefundRequest($this->getContainer());
        $req->orderRef = $orderRef;
        $req->refundTotal = $refundTotal;
        return $req;
    }

    public function query(array $orderRefs)
    {
        $req = new QueryRequest($this->getContainer());
        $req->addOrderRefs(...$orderRefs);
        return $req;
    }

    public function pageRedirect(?array $dataSource = null): RedirectPage
    {
        $page = new RedirectPage($this->getContainer());
        $page->process($dataSource);
        return $page;
    }

    public function pageIpn(?string $jsonText = null, ?string $signature = null): IpnPage
    {
        $page = new IpnPage($this->getContainer());
        $page->process($jsonText, $signature);
        return $page;
    }
}
