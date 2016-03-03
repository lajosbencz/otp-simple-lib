<?php

namespace OtpSimple;

use InvalidArgumentException;
use ArrayAccess;
use OtpSimple\Request\Curl;
use ReflectionClass;
use ReflectionProperty;
use OtpSimple\Enum\Currency;
use OtpSimple\Enum\Language;
use OtpSimple\Enum\Method;

class Config implements ArrayAccess
{
    #region Constants
    const URL_LIVE = 'https://secure.simplepay.hu/payment/';
    const URL_SANDBOX = 'https://sandbox.simplepay.hu/payment/';
    const URI_LU = 'order/lu.php';
    const URI_ALU = 'order/alu.php';
    const URI_IDN = 'order/idn.php';
    const URI_IRN = 'order/irn.php';
    const URI_IOS = 'order/ios.php';
    const URI_OC = 'order/tokens/';
    #endregion


    #region Static Methods
    /**
     * @param $url
     * @param array $query (optional)
     * @return string
     */
    protected static function _urlQuery($url, array $query=[]) {
        if(count($query)>0) {
            $query = '?'.http_build_query($query);
        } else {
            $query = '';
        }
        return $url.$query;
    }

    /**
     * @param string $underscore
     * @return string
     */
    public static function camelcase($underscore) {
        return lcfirst(str_replace(' ','',ucwords(str_replace('_',' ',$underscore))));
    }

    /**
     * @param string $camelcase
     * @return string
     */
    public static function underscore($camelcase) {
        $underscore = '';
        $n = strlen($camelcase);
        for($i=0; $i<$n; $i++) {
            $c = $camelcase[$i];
            $l = strtolower($c);
            if($c != $l) {
                $c = '_'.$l;
            }
            $underscore.= $c;
        }
        return $underscore;
    }

    /**
     * @return string[]
     */
    public static function listNames() {
        static $names;
        if(!$names) {
            $ref = new ReflectionClass(self::class);
            $names = array_map(function($item){
                /** @var ReflectionProperty $item */
                return substr($item->getName(),1);
            },$ref->getProperties(ReflectionProperty::IS_PROTECTED));
        }
        return $names;
    }

    /**
     * @param string $offset
     * @return bool
     */
    public static function hasName($offset) {
        $offset = self::camelcase($offset);
        return in_array($offset, self::listNames());
    }
    #endregion

    #region Private Properties
    /** @var array */
    private $_queryBase = [];
    #endregion

    #region Protected Properties
    /** @var array */
    protected $_server = [];
    /** @var array */
    protected $_query = [];
    /** @var array */
    protected $_post = [];
    /** @var array */
    protected $_request = [];
    /** @var int */
    protected $_timeout = 60;
    /** @var bool */
    protected $_sandbox = true;
    /** @var bool */
    protected $_debug = false;
    /** @var string */
    protected $_urlLive = self::URL_LIVE;
    /** @var string */
    protected $_urlSandbox = self::URL_SANDBOX;
    /** @var string */
    protected $_urlBase = 'order/';
    /** @var string */
    protected $_urlTimeout = 'timeout.php';
    /** @var string */
    protected $_urlBack = 'back.php';
    /** @var string */
    protected $_urlLu = 'lu.php';
    /** @var string */
    protected $_urlAlu = 'alu.php';
    /** @var string */
    protected $_urlIdn = 'idn.php';
    /** @var string */
    protected $_urlIrn = 'irn.php';
    /** @var string */
    protected $_urlIos = 'ios.php';
    /** @var string */
    protected $_urlOc = 'tokens/';
    /** @var Merchant[] */
    protected $_merchants = [];
    /** @var string */
    protected $_currency = Currency::__default;
    /** @var string */
    protected $_language = Language::__default;
    /** @var string */
    protected $_method = Method::__default;
    /** @var string */
    protected $_requestClass = Curl::class;
    /** @var string */
    protected $_formClass = Form::class;
    #endregion

    /**
     * @param self|array|string $config (optional)
     */
    public function __construct($config=[])
    {
        $config['server'] = $_SERVER;
        $config['query'] = $_GET;
        $config['post'] = $_POST;
        $config['request'] = $_REQUEST;
        if(is_a($config, self::class)) {
            $this->fromObject($config);
        } elseif(is_array($config)) {
            $this->fromArray($config);
        } elseif(is_string($config)) {
            $this->fromJson($config);
        }
    }

    #region ArrayAccess
    public function offsetExists($offset)
    {
        return self::hasName($offset);
    }

    public function offsetGet($offset)
    {
        if(!$this->offsetExists($offset)) {
            return null;
        }
        $offset = self::camelcase($offset);
        return $this->{'get'.ucfirst($offset)}();
    }

    public function offsetSet($offset, $value)
    {
        $offset = self::camelcase($offset);
        $this->{'set'.ucfirst($offset)}($value);
    }

    public function offsetUnset($offset)
    {
        $this->offsetSet($offset, null);
    }
    #endregion

    #region Setters
    /**
     * @param array $data
     * @return $this
     */
    public function setServer(array $data) {
        $this->_server = $data;
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setQuery(array $data) {
        $this->_query = $data;
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setPost(array $data) {
        $this->_post = $data;
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setRequest(array $data) {
        $this->_request = $data;
        return $this;
    }

    /**
     * @param boolean $sandbox
     * @return $this
     */
    public function setSandbox($sandbox)
    {
        $this->_sandbox = $sandbox;
        return $this;
    }

    /**
     * @param int $timeout
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->_timeout = $timeout;
        return $this;
    }

    /**
     * @param boolean $debug
     * @return $this
     */
    public function setDebug($debug)
    {
        $this->_debug = $debug;
        return $this;
    }

    /**
     * @param Currency|string $code
     * @return $this
     */
    public function setCurrency($code) {
        if(is_a($code, Currency::class)) {
            $code = $code->__toString();
        }
        $this->_currency = $code;
        return $this;
    }

    /**
     * @param Language|string $code
     * @return $this
     */
    public function setLanguage($code) {
        if(is_a($code, Language::class)) {
            $code = $code->__toString();
        }
        $this->_language = $code;
        return $this;
    }

    /**
     * @param Method|string $method
     * @return $this
     */
    public function setMethod($method) {
        if(is_a($method, Method::class)) {
            $method = $method->__toString();
        }
        $this->_method = $method;
        return $this;
    }

    /**
     * @param string $urlLive
     * @return $this
     */
    public function setUrlLive($urlLive)
    {
        $this->_urlLive = $urlLive;
        return $this;
    }

    /**
     * @param string $urlSandbox
     * @return $this
     */
    public function setUrlSandbox($urlSandbox)
    {
        $this->_urlSandbox = $urlSandbox;
        return $this;
    }

    /**
     * @param string $urlBase
     * @param array $query (optional)
     * @return $this
     */
    public function setUrlBase($urlBase, $query=[])
    {
        $this->_urlBase = $urlBase;
        $this->_queryBase = $query;
        return $this;
    }

    /**
     * @param string $urlTimeout
     * @param array $query (optional)
     * @return $this
     */
    public function setUrlTimeout($urlTimeout, $query=[])
    {
        $this->_urlTimeout = self::_urlQuery($urlTimeout,array_merge($this->_queryBase,$query));
        return $this;
    }

    /**
     * @param string $urlBack
     * @param array $query (optional)
     * @return $this
     */
    public function setUrlBack($urlBack, $query=[])
    {
        $this->_urlBack = self::_urlQuery($urlBack,array_merge($this->_queryBase,$query));
        return $this;}

    /**
     * @param string $urlLu
     * @param array $query (optional)
     * @return $this
     */
    public function setUrlLu($urlLu, $query=[])
    {
        $this->_urlLu = self::_urlQuery($urlLu,array_merge($this->_queryBase,$query));
        return $this;
    }

    /**
     * @param string $urlAlu
     * @param array $query (optional)
     * @return $this
     */
    public function setUrlAlu($urlAlu, $query=[])
    {
        $this->_urlAlu = self::_urlQuery($urlAlu,array_merge($this->_queryBase,$query));
        return $this;
    }

    /**
     * @param string $urlIdn
     * @param array $query (optional)
     * @return $this
     */
    public function setUrlIdn($urlIdn, $query=[])
    {
        $this->_urlIdn = self::_urlQuery($urlIdn,array_merge($this->_queryBase,$query));
        return $this;
    }

    /**
     * @param string $urlIrn
     * @param array $query (optional)
     * @return $this
     */
    public function setUrlIrn($urlIrn, $query=[])
    {
        $this->_urlIrn = self::_urlQuery($urlIrn,array_merge($this->_queryBase,$query));
        return $this;
    }

    /**
     * @param string $urlIos
     * @param array $query (optional)
     * @return $this
     */
    public function setUrlIos($urlIos, $query=[])
    {
        $this->_urlIos = self::_urlQuery($urlIos,array_merge($this->_queryBase,$query));
        return $this;
    }

    /**
     * @param string $urlOc
     * @param array $query (optional)
     * @return $this
     */
    public function setUrlOc($urlOc, $query=[])
    {
        $this->_urlOc = self::_urlQuery($urlOc,array_merge($this->_queryBase,$query));
        return $this;
    }

    /**
     * @param array $merchants
     * @return $this
     */
    public function setMerchants($merchants)
    {
        $this->_merchants = [];
        foreach($merchants as $currency=>$merchant) {
            $currency = strtoupper($currency);
            if(is_array($merchant)) {
                if(is_numeric($currency)) {
                    $currency = $merchant['currency'];
                }
                $merchant = new Merchant($merchant['id'],$merchant['key'],$currency);
            }
            if(!is_a($merchant, Merchant::class)) {
                continue;
            }
            $this->_merchants[$currency] = $merchant;
        }
        return $this;
    }

    /**
     * @param Currency|string $currency
     * @param string $id
     * @param string $key
     * @return $this
     */
    public function addMerchant($currency, $id, $key)
    {
        $currency = strtoupper($currency);
        $this->_merchants[$currency] = new Merchant($id,$key,$currency);
        return $this;
    }

    /**
     * @param string $className
     * @return $this
     */
    public function setRequestClass($className)
    {
        $this->_requestClass = $className;
        return $this;
    }

    /**
     * @param string $className
     * @return $this
     */
    public function setFormClass($className) {
        $this->_formClass = $className;
        return $this;
    }

    #endregion

    #region Getters
    /**
     * @param mixed $offset (optional)
     * @return array
     */
    public function getServer($offset=null) {
        if($offset===null) {
            return $this->_server;
        }
        if(!array_key_exists($offset, $this->_server)) {
            return null;
        }
        return $this->_server[$offset];
    }

    /**
     * @param mixed $offset (optional)
     * @return array
     */
    public function getQuery($offset=null) {
        if($offset===null) {
            return $this->_query;
        }
        if(!array_key_exists($offset, $this->_query)) {
            return null;
        }
        return $this->_query[$offset];
    }

    /**
     * @param mixed $offset (optional)
     * @return array
     */
    public function getPost($offset=null) {
        if($offset===null) {
            return $this->_post;
        }
        if(!array_key_exists($offset, $this->_post)) {
            return null;
        }
        return $this->_post[$offset];
    }

    /**
     * @param mixed $offset (optional)
     * @return array
     */
    public function getRequest($offset=null) {
        if($offset===null) {
            return $this->_request;
        }
        if(!array_key_exists($offset, $this->_request)) {
            return null;
        }
        return $this->_request[$offset];
    }

    /**
     * @return string
     */
    public function getHostName() {
        static $h;
        if(!$h) {
            $protocol = "http://";
            if(
                ($this->getServer('HTTPS')!==null && $this->getServer('HTTPS') !== 'off') ||
                strtolower($this->getServer('HTTP_FRONT_END_HTTPS')) == "on" ||
                strtolower($this->getServer('HTTP_X_FORWARDED_PROTO')) == 'https' ||
                strtolower($this->getServer('HTTP_X_FORWARDED_SSL')) == 'on'
            ) {
                $protocol = "https://";
            }
            $h = $protocol . $this->getServer('HTTP_HOST') . '/';
        }
        return $h;
    }

    /**
     * @return boolean
     */
    public function isSandbox()
    {
        return $this->_sandbox;
    }

    /**
     * @return boolean
     */
    public function isDebug()
    {
        return $this->_debug;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->_timeout;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->_currency;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * @return string
     */
    public function getUrlLive()
    {
        return $this->_urlLive;
    }

    /**
     * @return string
     */
    public function getUrlSandbox()
    {
        return $this->_urlSandbox;
    }

    /**
     * @return string
     */
    public function getUrlBase()
    {
        return ($this->isSandbox()?$this->_urlSandbox:$this->_urlLive).$this->_urlBase;
    }

    /**
     * @return string
     */
    public function getUrlTimeout()
    {
        return $this->getHostName().$this->_urlTimeout;
    }

    /**
     * @return string
     */
    public function getUrlBack()
    {
        return $this->getHostName().$this->_urlBack;
    }

    /**
     * @return string
     */
    public function getUrlLu()
    {
        return $this->getUrlBase().$this->_urlLu;
    }

    /**
     * @return string
     */
    public function getUrlAlu()
    {
        return $this->getUrlBase().$this->_urlAlu;
    }

    /**
     * @return string
     */
    public function getUrlIdn()
    {
        return $this->getUrlBase().$this->_urlIdn;
    }

    /**
     * @return string
     */
    public function getUrlIrn()
    {
        return $this->getUrlBase().$this->_urlIrn;
    }

    /**
     * @return string
     */
    public function getUrlIos()
    {
        return $this->getUrlBase().$this->_urlIos;
    }

    /**
     * @return string
     */
    public function getUrlOc()
    {
        return $this->getUrlBase().$this->_urlOc;
    }

    /**
     * @return Merchant[]
     */
    public function getMerchants()
    {
        return $this->_merchants;
    }

    /**
     * @return Merchant
     */
    public function getMerchant() {
        return $this->getMerchantByCurrency($this->getCurrency());
    }

    /**
     * @param string $id
     * @return Merchant
     */
    public function getMerchantById($id)
    {
        foreach($this->getMerchants() as $merchant) {
            if($merchant->getId() === $id) {
                return $merchant;
            }
        }
        return null;
    }

    /**
     * @param Currency|string $code
     * @return Merchant
     */
    public function getMerchantByCurrency($code) {
        $code = (string)$code;
        if(array_key_exists($code, $this->_merchants)) {
            return $this->_merchants[$code];
        }
        return null;
    }

    /**
     * @return string
     */
    public function getRequestClass() {
        return $this->_requestClass;
    }

    /**
     * @return string
     */
    public function getFormClass() {
        return $this->_formClass;
    }
    #endregion

    #region Withers
    /**
     * @param array $data
     * @return self
     */
    public function withServer(array $data) {
        return (new self($this))->setServer($data);
    }

    /**
     * @param array $data
     * @return self
     */
    public function withQuery(array $data) {
        return (new self($this))->setQuery($data);
    }

    /**
     * @param array $data
     * @return self
     */
    public function withPost(array $data) {
        return (new self($this))->setPost($data);
    }

    /**
     * @param array $data
     * @return self
     */
    public function withRequest(array $data) {
        return (new self($this))->setRequest($data);
    }

    /**
     * @param bool $sandbox
     * @return self
     */
    public function withSandbox($sandbox) {
        return (new self($this))->setSandbox($sandbox);
    }

    /**
     * @param bool $debug
     * @return self
     */
    public function withDebug($debug) {
        return (new self($this))->setDebug($debug);
    }

    /**
     * @param int $timeout
     * @return self
     */
    public function withTimeout($timeout) {
        return (new self($this))->setTimeout($timeout);
    }

    /**
     * @param Currency|string $code
     * @return self
     */
    public function withCurrency($code) {
        return (new self($this))->setCurrency($code);
    }

    /**
     * @param Language|string $code
     * @return self
     */
    public function withLanguage($code) {
        return (new self($this))->setLanguage($code);
    }

    /**
     * @param string $urlLive
     * @return self
     */
    public function withUrlLive($urlLive) {
        return (new self($this))->setUrlLive($urlLive);
    }

    /**
     * @param string $urlSandbox
     * @return self
     */
    public function withUrlSandbox($urlSandbox) {
        return (new self($this))->setUrlSandbox($urlSandbox);
    }

    /**
     * @param string $urlBase
     * @param array $query (optional)
     * @return self
     */
    public function withUrlBase($urlBase, $query=[]) {
        return (new self($this))->setUrlBase($urlBase, $query);
    }

    /**
     * @param string $urlTimeout
     * @param array $query (optional)
     * @return self
     */
    public function withUrlTimeout($urlTimeout, $query=[]) {
        return (new self($this))->setUrlTimeout($urlTimeout, $query);
    }

    /**
     * @param string $urlBack
     * @param array $query (optional)
     * @return self
     */
    public function withUrlBack($urlBack, $query=[]) {
        return (new self($this))->setUrlBack($urlBack, $query);
    }

    /**
     * @param string $urlLu
     * @param array $query (optional)
     * @return self
     */
    public function withUrlLu($urlLu, $query=[]) {
        return (new self($this))->setUrlLu($urlLu, $query);
    }

    /**
     * @param string $urlAlu
     * @param array $query (optional)
     * @return self
     */
    public function withUrlAlu($urlAlu, $query=[]) {
        return (new self($this))->setUrlAlu($urlAlu, $query);
    }

    /**
     * @param string $urlIdn
     * @param array $query (optional)
     * @return self
     */
    public function withUrlIdn($urlIdn, $query=[]) {
        return (new self($this))->setUrlIdn($urlIdn, $query);
    }

    /**
     * @param string $urlIrn
     * @param array $query (optional)
     * @return self
     */
    public function withUrlIrn($urlIrn, $query=[]) {
        return (new self($this))->setUrlIrn($urlIrn, $query);
    }

    /**
     * @param string $urlIos
     * @param array $query (optional)
     * @return self
     */
    public function withUrlIos($urlIos, $query=[]) {
        return (new self($this))->setUrlIos($urlIos, $query);
    }

    /**
     * @param string $urlOc
     * @param array $query (optional)
     * @return self
     */
    public function withUrlOc($urlOc, $query=[]) {
        return (new self($this))->setUrlOc($urlOc, $query);
    }

    /**
     * @param Merchant[] $merchants
     * @return self
     */
    public function withMerchants(array $merchants) {
        return (new self($this))->setMerchants($merchants);
    }

    /**
     * @param string $id
     * @param string $key
     * @param Currency|string $currency (optional)
     * @return self
     */
    public function withMerchant($id, $key, $currency=Currency::__default) {
        return (new self($this))->addMerchant($id,$key,$currency);
    }

    /**
     * @param Method|string $method
     * @return self
     */
    public function withMethod($method) {
        return (new self($this))->setMethod($method);
    }

    /**
     * @param string $className
     * @return mixed
     */
    public function withRequestClass($className) {
        return (new self($this))->setRequestClass($className);
    }

    /**
     * @param string $className
     * @return mixed
     */
    public function withFormClass($className) {
        return (new self($this))->setFormClass($className);
    }
    #endregion

    #region Miscellaneous
    /**
     * @param array $config
     * @return $this
     * @throws Exception
     */
    public function fromArray(array $config) {
        foreach($config as $k=>$v) {
            $k = self::camelcase($k);
            if($k=='merchants') {
                foreach($v as $currency=>$merchant) {
                    $this->addMerchant($currency,$merchant['id'], $merchant['key']);
                }
                continue;
            }
            if(self::hasName($k)) {
                $m = 'set'.ucfirst($k);
                $this->{$m}($v);
            } else {
                throw new Exception('Invalid config name: '.$k);
            }
        }
        return $this;
    }

    /**
     * @param string $config
     * @param int $depth (optional)
     * @param int $options (optional)
     * @return $this
     */
    public function fromJson($config, $depth=null, $options=null) {
        $this->fromArray(json_decode($config, true, $depth, $options));
        return $this;
    }

    /**
     * @param self $config
     * @return $this
     */
    public function fromObject(self $config) {
        $this->fromArray($config->toArray());
        return $this;
    }

    /**
     * @return array
     */
    public function toArray() {
        $array = [];
        foreach(self::listNames() as $name) {
            $v = $this->{'_'.$name};
            if($name == 'merchants') {
                foreach($v as &$m) {
                    /** @var Merchant $m */
                    $m = $m->toArray();
                }
            }
            $array[$name] = $v;
        }
        return $array;
    }

    /**
     * @param int $options (optional)
     * @return string
     */
    public function toJson($options=null) {
        return json_encode($this->toArray(), $options);
    }

    public function __toString()
    {
        return $this->toJson(JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    }
    #endregion

}
