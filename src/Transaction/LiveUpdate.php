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
 * @property string $merchant_id
 * @property string $order_id
 * @property string $order_date
 * @property float $discount
 * @property float $shipping
 * @property string $currency
 * @property string $method
 * @property string $language
 * @property string $automode
 * @property string $redirect_url
 * @property int $timeout
 * @property string $timeout_url
 * @property string $version
 * @property string $hash
 * @property string[] $product_name
 * @property string[] $product_code
 * @property string[] $product_info
 * @property int[] $product_qty
 * @property float[] $product_price
 * @property float[] $product_vat
 * @property string $bill_first_name
 * @property string $bill_last_name
 * @property string $bill_email
 * @property string $bill_phone
 * @property string $bill_company
 * @property string $bill_fiscal_code
 * @property string $bill_country_code
 * @property string $bill_state
 * @property string $bill_city
 * @property string $bill_address
 * @property string $bill_address2
 * @property string $bill_zip_code
 * @property string $delivery_first_name
 * @property string $delivery_last_name
 * @property string $delivery_phone
 * @property string $delivery_company
 * @property string $delivery_fiscal_code
 * @property string $delivery_country_code
 * @property string $delivery_state
 * @property string $delivery_city
 * @property string $delivery_address
 * @property string $delivery_address2
 * @property string $delivery_zip_code
 */
class LiveUpdate extends Transaction
{
    protected static $_reverseMap = false;

    protected function _getHashFields()
    {
        return ['merchant_id','order_id','order_date','product_name','product_code','product_info','product_price','product_qty','product_vat','shipping','currency','discount','method'];
    }

    protected function _describeFields()
    {
        return [
            'merchant_id' => ['name'=>'MERCHANT', 'length' => 7, 'type' => 'scalar', 'required' => true],
            'order_id' => ['name'=>'ORDER_REF', 'length' => 155, 'type' => 'scalar', 'required' => true],
            'order_date' => ['name'=>'ORDER_DATE', 'length' => 19, 'type' => 'scalar', 'required' => true],
            'discount' => ['name'=>'DISCOUNT', 'length' => 20, 'type' => 'scalar', 'required' => true, 'default' => 0],
            'shipping' => ['name'=>'ORDER_SHIPPING', 'length' => 20, 'type' => 'scalar', 'required' => true, 'default' => 0],
            'currency' => ['name'=>'PRICES_CURRENCY', 'length' => 3, 'type' => 'scalar', 'required' => true],
            'method' => ['name'=>'PAY_METHOD', 'length' => 32, 'type' => 'scalar', 'required' => true],
            'language' => ['name'=>'LANGUAGE', 'length' => 2, 'type' => 'scalar', 'required' => true],
            'automode' => ['name'=>'AUTOMODE', 'length' => 1, 'type' => 'scalar', 'required' => true],
            'timeout' => ['name'=>'ORDER_TIMEOUT', 'length' => 4, 'type' => 'scalar', 'required' => true],
            'timeout_url' => ['name'=>'TIMEOUT_URL', 'length' => 155, 'type' => 'scalar', 'required' => true],
            'redirect_url' => ['name'=>'BACK_REF', 'length' => 155, 'type' => 'scalar', 'required' => true],
            'version' => ['name'=>'SDK_VERSION', 'length' => 155, 'type' => 'scalar'],
            'hash' => ['name'=>'ORDER_HASH', 'length' => 32, 'type' => 'scalar', 'required'=>true],

            //product
            'product_name' => ['name'=>'ORDER_PNAME', 'length' => 155, 'type' => 'array', 'required' => true],
            'product_code' => ['name'=>'ORDER_PCODE', 'length' => 20, 'type' => 'array', 'required' => true],
            'product_info' => ['name'=>'ORDER_PINFO', 'length' => 155, 'type' => 'array', 'required' => true, 'default' => ''],
            'product_qty' => ['name'=>'ORDER_QTY', 'length' => 155, 'type' => 'array', 'required' => true, 'default' => 1],
            'product_price' => ['name'=>'ORDER_PRICE', 'length' => 20, 'type' => 'array', 'required' => true],
            'product_vat' => ['name'=>'ORDER_VAT', 'length' => 2, 'type' => 'array', 'required' => true, 'default' => 0],

            //billing
            'bill_first_name' => ['name'=>'BILL_FNAME', 'length' => 155, 'type' => 'scalar', 'required' => true],
            'bill_last_name' => ['name'=>'BILL_LNAME', 'length' => 155, 'type' => 'scalar', 'required' => true],
            'bill_email' => ['name'=>'BILL_EMAIL', 'length' => 155, 'type' => 'scalar', 'required' => true],
            'bill_phone' => ['name'=>'BILL_PHONE', 'length' => 155, 'type' => 'scalar', 'required' => true],
            'bill_company' => ['name'=>'BILL_COMPANY', 'length' => 155, 'type' => 'scalar'],
            'bill_fiscal_code' => ['name'=>'BILL_FISCALCODE', 'length' => 155, 'type' => 'scalar'],
            'bill_country_code' => ['name'=>'BILL_COUNTRYCODE', 'length' => 2, 'type' => 'scalar', 'required' => true],
            'bill_state' => ['name'=>'BILL_STATE', 'length' => 155, 'type' => 'scalar', 'required' => true],
            'bill_city' => ['name'=>'BILL_CITY', 'length' => 155, 'type' => 'scalar', 'required' => true],
            'bill_address' => ['name'=>'BILL_ADDRESS', 'length' => 155, 'type' => 'scalar', 'required' => true],
            'bill_address2' => ['name'=>'BILL_ADDRESS2', 'length' => 155, 'type' => 'scalar'],
            'bill_zip_code' => ['name'=>'BILL_ZIPCODE', 'length' => 20, 'type' => 'scalar', 'required' => true],

            //delivery
            'delivery_first_name' => ['name'=>'DELIVERY_FNAME', 'length' => 155, 'type' => 'scalar', 'required' => true],
            'delivery_last_name' => ['name'=>'DELIVERY_LNAME', 'length' => 155, 'type' => 'scalar', 'required' => true],
            'delivery_phone' => ['name'=>'DELIVERY_PHONE', 'length' => 155, 'type' => 'scalar', 'required' => true],
            'delivery_company' => ['name'=>'DELIVERY_COMPANY', 'length' => 155, 'type' => 'scalar'],
            'delivery_country_code' => ['name'=>'DELIVERY_COUNTRYCODE', 'length' => 2, 'type' => 'scalar', 'required' => true],
            'delivery_state' => ['name'=>'DELIVERY_STATE', 'length' => 155, 'type' => 'scalar', 'required' => true],
            'delivery_city' => ['name'=>'DELIVERY_CITY', 'length' => 155, 'type' => 'scalar', 'required' => true],
            'delivery_address' => ['name'=>'DELIVERY_ADDRESS', 'length' => 155, 'type' => 'scalar', 'required' => true],
            'delivery_address2' => ['name'=>'DELIVERY_ADDRESS2', 'length' => 155, 'type' => 'scalar'],
            'delivery_zip_code' => ['name'=>'DELIVERY_ZIPCODE', 'length' => 20, 'type' => 'scalar', 'required' => true],
        ];
    }

    public function __construct(Config $config)
    {
        parent::__construct($config);
        $this->merchant_id = $config->getMerchant()->getId();
        $this->currency = $config->getMerchant()->getCurrency();
        $this->order_date = date('Y-m-d H:i:s');
        $this->timeout = $config->getTimeout();
        $this->method = $config->getMethod();
        $this->automode = $config->getMethod() == Method::AUTOMODE ? 1 : 0;
        $this->language = $config->getLanguage();
        $this->timeout_url = $config->getUrlTimeout();
        $this->redirect_url = $config->getUrlRedirect();
    }

    public function __set($name, $value) {
        parent::__set($name, $value);
        if(substr($name,0,5)=='bill_') {
            $name = 'delivery_'.substr($name, 5);
            if($this->_isField($name) && !isset($this->$name)) {
                $this->$name = $value;
            }
        }
    }

    public function clearProducts() {
        $this->product_name = [];
        $this->product_code = [];
        $this->product_info = [];
        $this->product_qty = [];
        $this->product_price = [];
        $this->product_vat = [];
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
        return array_key_exists('product_code', $this->_data) && is_array($this->_data['product_code']) && in_array($productOrCode, $this->_data['product_code']);
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
        if(!is_array($this->product_code)) {
            $this->clearProducts();
        }
        if($this->hasProduct($product->code)) {
            $index = array_search($product->code, $this->product_code);
            $this->_data['product_qty'][$index]+= $product->qty;
        } else {
            $this->_data['product_name'][] = $product->name;
            $this->_data['product_code'][] = $product->code;
            $this->_data['product_info'][] = $product->info;
            $this->_data['product_qty'][] = $product->qty;
            $this->_data['product_price'][] = $product->price;
            $this->_data['product_vat'][] = $product->vat;
        }
        return $this;
    }
    public function checkRequired()
    {
        $this->version = $this->getVersion();
        $this->hash = $this->createHash();
        return parent::checkRequired();
    }

    public function createHash() {
        return $this->config->getMerchant()->hash($this->toArray(), $this->_getHashFields());
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
        $form->setAction($this->getConfig()->getUriLu());
        if($id) {
            $form->setId($id);
        }
        return $form;
    }

}