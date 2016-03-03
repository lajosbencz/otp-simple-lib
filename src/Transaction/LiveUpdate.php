<?php

namespace OtpSimple\Transaction;

use OtpSimple\Config;
use OtpSimple\Enum\Method;
use OtpSimple\Exception;
use OtpSimple\FormInterface;
use OtpSimple\Product;
use OtpSimple\Transaction;

/**
 * Class LiveUpdate
 * @package OtpSimple\Transaction
 * @property string $merchant
 * @property string $order_ref
 * @property string $order_date
 * @property float $discount
 * @property float $order_shipping
 * @property string $prices_currency
 * @property string $pay_method
 * @property string $language
 * @property string $automode
 * @property string $back_ref
 * @property int $order_timeout
 * @property string $timeout_url
 * @property string $sdk_version
 * @property string $order_hash
 * @property string[] $order_pname
 * @property string[] $order_pcode
 * @property string[] $order_pinfo
 * @property int[] $order_qty
 * @property float[] $order_price
 * @property float[] $order_vat
 * @property string $bill_fname
 * @property string $bill_lname
 * @property string $bill_email
 * @property string $bill_phone
 * @property string $bill_company
 * @property string $bill_fiscalcode
 * @property string $bill_countrycode
 * @property string $bill_state
 * @property string $bill_city
 * @property string $bill_address
 * @property string $bill_address2
 * @property string $bill_zipcode
 * @property string $delivery_fname
 * @property string $delivery_lname
 * @property string $delivery_phone
 * @property string $delivery_company
 * @property string $delivery_fiscalcode
 * @property string $delivery_countrycode
 * @property string $delivery_state
 * @property string $delivery_city
 * @property string $delivery_address
 * @property string $delivery_address2
 * @property string $delivery_zipcode
 */
class LiveUpdate extends Transaction
{

    protected function _getFields()
    {
        return [
            'merchant' => ['length'=>7, 'type'=>'simple', 'required'=>true],
            'order_ref' => ['length'=>155, 'type' => 'simple', 'required' => true],
            'order_date' => ['length'=>19, 'type' => 'simple', 'required' => true],
            'discount' => ['length'=>20, 'type' => 'simple', 'default' => 0, 'required' => true],
            'order_shipping' => ['length'=>20, 'type' => 'simple', 'default' => 0],
            'prices_currency' => ['length'=>3, 'type' => 'simple', 'required' => true],
            'pay_method' => ['length'=>32, 'type' => 'simple', 'required'=>true],
            'language' => ['length'=>2, 'type' => 'simple', 'required'=>true],
            'automode' => ['length'=>1, 'type' => 'simple', 'required'=>true],
            'back_ref' => ['length'=>155, 'type' => 'simple', 'required'=>true],
            'order_timeout' => ['length'=>4, 'type' => 'simple', 'required'=>true],
            'timeout_url' => ['length'=>155, 'type' => 'simple', 'required'=>true],
            'sdk_version' => ['length'=>155, 'type' => 'simple'],
            'order_hash' => ['length'=>32, 'type' => 'simple'],

            //product
            'order_pname' => ['length'=>155, 'type' => 'array', 'required' => true],
//            'order_pgroup' => ['length'=>155, 'type' => 'array'],
            'order_pcode' => ['length'=>20, 'type' => 'array', 'required' => true],
            'order_pinfo' => ['length'=>155, 'type' => 'array', 'default' => '', 'required'=>true],
            'order_qty' => ['length'=>155, 'type' => 'array', 'default' => 1, 'required' => true],
            'order_price' => ['length'=>20, 'type' => 'array', 'required' => true],
            'order_vat' => ['length'=>2, 'type' => 'array', 'default' => '0', 'required' => true],
//            'order_ver' => ['length'=>155, 'type' => 'array'],

            //billing
            'bill_fname' => ['length'=>155, 'type' => 'simple', 'required' => true],
            'bill_lname' => ['length'=>155, 'type' => 'simple', 'required' => true],
            'bill_email' => ['length'=>155, 'type' => 'simple', 'required' => true],
            'bill_phone' => ['length'=>155, 'type' => 'simple', 'required' => true],
            'bill_company' => ['length'=>155, 'type' => 'simple'],
            'bill_fiscalcode' => ['length'=>155, 'type' => 'simple'],
            'bill_countrycode' => ['length'=>2, 'type' => 'simple', 'required' => true],
            'bill_state' => ['length'=>155, 'type' => 'simple', 'required' => true],
            'bill_city' => ['length'=>155, 'type' => 'simple', 'required' => true],
            'bill_address' => ['length'=>155, 'type' => 'simple', 'required' => true],
            'bill_address2' => ['length'=>155, 'type' => 'simple'],
            'bill_zipcode' => ['length'=>20, 'type' => 'simple', 'required' => true],

            //delivery
            'delivery_fname' => ['length'=>155, 'type' => 'simple', 'required' => true],
            'delivery_lname' => ['length'=>155, 'type' => 'simple', 'required' => true],
            'delivery_phone' => ['length'=>155, 'type' => 'simple', 'required' => true],
            'delivery_company' => ['length'=>155, 'type' => 'simple'],
            'delivery_fiscalcode' => ['length'=>155, 'type'=>'simple'],
            'delivery_countrycode' => ['length'=>2, 'type' => 'simple', 'required' => true],
            'delivery_state' => ['length'=>155, 'type' => 'simple', 'required' => true],
            'delivery_city' => ['length'=>155, 'type' => 'simple', 'required' => true],
            'delivery_address' => ['length'=>155, 'type' => 'simple', 'required' => true],
            'delivery_address2' => ['length'=>155, 'type' => 'simple'],
            'delivery_zipcode' => ['length'=>20, 'type' => 'simple', 'required' => true],
        ];
    }

    protected function _getHashFields()
    {
        return ['merchant','order_ref','order_date','order_pname','order_pcode','order_pinfo','order_price','order_qty','order_vat','order_shipping','prices_currency','discount','pay_method'];
    }

    public function __construct(Config $config)
    {
        parent::__construct($config);
        $this['merchant'] = $config->getMerchant()->getId();
        $this['prices_currency'] = $config->getMerchant()->getCurrency();
        $this['order_date'] = date('Y-m-d H:i:s');
        $this['order_timeout'] = $config->getTimeout();
        $this['pay_method'] = $config->getMethod();
        $this['automode'] = $config->getMethod() == Method::AUTOMODE ? 1 : 0;
        $this['language'] = $config->getLanguage();
    }

    public function offsetSet($offset, $value)
    {
        parent::offsetSet($offset, $value);
        if(substr($offset,0,5)=='bill_') {
            $name = 'delivery_'.substr($offset, 5);
            if($this->isFieldValid($name) && !$this->isFieldSet($name)) {
                $this->offsetSet($name, $value);
            }
        }
    }

    public function clearProducts() {
        $this->order_pname = [];
        $this->order_pcode = [];
        $this->order_pinfo = [];
        $this->order_qty = [];
        $this->order_price = [];
        $this->order_vat = [];
        return $this;
    }

    /**
     * @param string|array|Product $productOrCode
     * @return bool
     */
    public function hasProduct($productOrCode) {
        if(is_array($productOrCode)) {
            $productOrCode = $productOrCode['code'];
        }
        elseif(is_a($productOrCode, Product::class)) {
            $productOrCode = $productOrCode->code;
        }
        return array_key_exists('order_pcode', $this->_data) && is_array($this->_data['order_pcode']) && in_array($productOrCode, $this->_data['order_pcode']);
    }

    /**
     * @param array|Product $product
     * @return $this
     * @throws Exception\InvalidProductException
     */
    public function addProduct($product) {
        if(is_array($product)) {
            $product = new Product($product);
        }
        if(!is_a($product, Product::class)) {
            throw new Exception\InvalidProductException('Invalid product, must be an array or instance of '.Product::class);
        }
        if($this->hasProduct($product->code)) {
            $index = array_search($product->code, $this->_data['order_pcode']);
            $this->_data['order_qty'][$index]+= $product->qty;
        } else {
            $this->_data['order_pname'][] = $product->name;
            $this->_data['order_pcode'][] = $product->code;
            $this->_data['order_pinfo'][] = $product->info;
            $this->_data['order_qty'][] = $product->qty;
            $this->_data['order_price'][] = $product->price;
            $this->_data['order_vat'][] = $product->vat;
        }
        return $this;
    }
    public function checkRequired()
    {
        $this->createHash();
        return parent::checkRequired();
    }

    public function createHash() {
        $hash = [];
        foreach($this->_getHashFields() as $name) {
            $hash[] = $this->_data[$name];
        }
        $this['order_hash'] = $this->config->getMerchant()->hash($hash);
        return $this;
    }


    public function createForm($id=null, $formClass=null) {
        if(!$this->checkRequired()) {
            throw new Exception\LiveUpdateMissingFieldsException('Missing fields: '.join(', ',$this->getMissing()));
        }
        if(!$formClass) {
            $formClass = $this->config->getFormClass();
        }
        if(!in_array(FormInterface::class, class_implements($formClass))) {
            throw new Exception\InvalidFormClassException('Form class must implement '.FormInterface::class);
        }
        /** @var FormInterface $form */
        $form = new $formClass;
        $form->setLiveUpdate($this);
        $form->setAction($this->getConfig()->getUrlLu());
        if($id) {
            $form->setId($id);
        }
        return $form;
    }

}