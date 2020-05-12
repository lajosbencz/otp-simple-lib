<?php

namespace OtpSimple\Request;


use OtpSimple\Config\LanguageTrait;
use OtpSimple\Config\RedirectTimeoutTrait;
use OtpSimple\Container;
use OtpSimple\Entity\Address;
use OtpSimple\Entity\ItemCollection;
use OtpSimple\Enum\Method;
use OtpSimple\Config\RedirectUrlTrait;
use OtpSimple\Request;
use OtpSimple\Response;
use OtpSimple\Util;

/**
 * @property-read ItemCollection $items
 * @property-read Address $invoice
 * @property-read Address $delivery
 */
class StartRequest extends Request
{
    use RedirectUrlTrait, LanguageTrait, RedirectTimeoutTrait;

    public $total = 0;

    public $orderRef = '';
    public $customer = '';
    public $email = '';
    public $shippingCost = 0.;
    public $discount = 0.;

    /**
     * If it has false as value, the transaction will not wait for a “finish” request, but will trigger an immediate
     * full charge. This can be used to initiate an immediate charge even on two-step accounts.
     * @var bool
     */
    public $twoStep = false;

    /**
     * If the billing information of the buyer is not known in the merchant system, then the variable
     * “maySelectInvoice” will allow the customer to provide this information on the payment page.
     * @var bool
     */
    public $maySelectInvoice = true;

    /**
     * If the customer’s delivery information is not available for the merchant, the SimplePay payment page can
     * also request it. To do this, all countries must be listed where the merchant ships.
     * <example>
     * "maySelectDelivery":[
     *   "HU",
     *   "AT",
     *   "DE"
     * ],
     * </example>
     * @var array
     */
    public $maySelectDelivery = [];

    /** @var Address */
    protected $_invoice;

    /** @var Address */
    protected $_delivery;

    /** @var ItemCollection */
    protected $_items;

    /** @var string[] */
    protected $_methods = [
        Method::__default,
    ];

    public function __construct(?Container $container = null)
    {
        parent::__construct($container);
        $this->_items = new ItemCollection;
        $this->_invoice = new Address;
        $this->_delivery = new Address;
        $this->setRedirectTimeout($this->config->getRedirectTimeout());
        $this->setRedirectUrls($this->config->getRedirectUrls());
        $this->setLanguage($this->config->getLanguage());
    }

    /**
     * @return Response\StartResponse
     */
    public function send(): Response
    {
        return parent::send();
    }

    public function getApiUrl(): string
    {
        return '/start';
    }

    public function __get(string $name)
    {
        switch ($name) {
            case 'items':
                return $this->_items;
            case 'invoice':
                return $this->_invoice;
            case 'delivery':
                return $this->_delivery;
        }
        return parent::__get($name);
    }

    public function setPaymentMethods(array $methods): self
    {
        $this->_methods = $methods;
        return $this;
    }

    public function getData(): array
    {
        $data = array_merge(parent::getData(), [
            'currency' => $this->config->getMerchant()->currency,
            'timeout' => date("c", time() + $this->config->getRedirectTimeout()),
            'language' => $this->getLanguage(),
            'methods' => $this->_methods,
            'orderRef' => $this->orderRef,
            'customerEmail' => $this->email,
            'total' => $this->total,
            'urls' => $this->getRedirectUrls(),
            'twoStep' => $this->twoStep,
            'shippingCost' => $this->shippingCost,
            'discount' => $this->discount,
            'maySelectInvoice' => $this->maySelectInvoice,
            'maySelectDelivery' => $this->maySelectDelivery,
        ]);
        if ($this->items->count() > 0) {
            $data['items'] = $this->items->toArray();
        }
        if ($this->invoice->name) {
            $data['invoice'] = Util::objectToArray($this->invoice);
        } else {
            $data['customer'] = $this->customer;
        }
        if ($this->delivery->name) {
            $data['delivery'] = Util::objectToArray($this->delivery);
        }
        return $data;
    }

}
