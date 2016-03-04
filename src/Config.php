<?php

namespace OtpSimple;

use OtpSimple\Request\Curl;
use OtpSimple\Enum\Currency;
use OtpSimple\Enum\Language;
use OtpSimple\Enum\Method;

/**
 * Class Config
 * @package OtpSimple
 * @property bool $sandbox
 * @property bool $debug
 * @property string $server
 * @property string $query
 * @property string $post
 * @property string $request
 * @property string $timeout
 * @property string $currency
 * @property string $language
 * @property string $method
 * @property string $url_live
 * @property string $url_sandbox
 * @property string $url_timeout
 * @property string $url_redirect
 * @property string $uri_lu
 * @property string $uri_alu
 * @property string $uri_idn
 * @property string $uri_irn
 * @property string $uri_ios
 * @property string $uri_oc
 * @property Merchant[] $merchants
 * @property string $request_class
 * @property string $form_class
 */
class Config extends Object
{
    #region Constants
    const URL_LIVE = 'https://secure.simplepay.hu/payment/order/';
    const URL_SANDBOX = 'https://sandbox.simplepay.hu/payment/order/';
    const URI_LU = 'lu.php';
    const URI_ALU = 'alu.php';
    const URI_IDN = 'idn.php';
    const URI_IRN = 'irn.php';
    const URI_IOS = 'ios.php';
    const URI_OC = 'tokens/';
    #endregion


    #region Static Methods
    /**
     * @param $url
     * @param array $query (optional)
     * @return string
     */
    protected static function _urlQuery($url, array $query=[]) {
        parse_str(parse_url($url, PHP_URL_QUERY),$q);
        if(is_array($q) && count($q)>0) {
            $url = substr($url,0,strpos($url,'?'));
        }
        $query = array_merge($q, $query);
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
    #endregion

    protected function _describeFields()
    {
        return [
            'sandbox' => ['type'=>'scalar'],
            'debug' => ['type'=>'scalar'],
            'server' => ['type'=>'scalar'],
            'query' => ['type'=>'scalar'],
            'post' => ['type'=>'scalar'],
            'request' => ['type'=>'scalar'],
            'timeout' => ['type'=>'scalar'],
            'currency' => ['type'=>'scalar'],
            'language' => ['type'=>'scalar'],
            'method' => ['type'=>'scalar'],
            'url_live' => ['type'=>'scalar'],
            'url_sandbox' => ['type'=>'scalar'],
            'url_timeout' => ['type'=>'scalar'],
            'url_redirect' => ['type'=>'scalar'],
            'uri_lu' => ['type'=>'scalar'],
            'uri_alu' => ['type'=>'scalar'],
            'uri_idn' => ['type'=>'scalar'],
            'uri_irn' => ['type'=>'scalar'],
            'uri_ios' => ['type'=>'scalar'],
            'uri_oc' => ['type'=>'scalar'],
            'merchants' => ['array'],
            'request_class' => ['type'=>'scalar'],
            'form_class' => ['type'=>'scalar'],
        ];
    }

    /**
     * @param self|array|string $config (optional)
     */
    public function __construct($config=[])
    {
        if(is_a($config, self::class)) {
            $this->fromObject($config);
        } elseif(is_array($config)) {
            $this->fromArray($config);
        } elseif(is_string($config)) {
            $this->fromJson($config);
        }
        if(!empty($_SERVER)) {
            if($this->server === null) {
                $this->server = $_SERVER;
            }
            if($this->query === null) {
                $this->query = $_GET;
            }
            if($this->post === null) {
                $this->post = $_POST;
            }
            if($this->request === null) {
                $this->request = $_REQUEST;
            }
        }
    }

    public function describeFields()
    {
        return [
        ];
    }

    #region Setters
    /**
     * @param array $data
     * @return $this
     */
    public function setServer(array $data) {
        $this->server = $data;
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setQuery(array $data) {
        $this->query = $data;
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setPost(array $data) {
        $this->post = $data;
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setRequest(array $data) {
        $this->request = $data;
        return $this;
    }

    /**
     * @param boolean $sandbox
     * @return $this
     */
    public function setSandbox($sandbox)
    {
        $this->sandbox = $sandbox;
        return $this;
    }

    /**
     * @param boolean $debug
     * @return $this
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * @param int $timeout
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @param Currency|string $code
     * @return $this
     */
    public function setCurrency($code) {
        $this->currency = (string)$code;
        return $this;
    }

    /**
     * @param Language|string $code
     * @return $this
     */
    public function setLanguage($code) {
        $this->language = (string)$code;
        return $this;
    }

    /**
     * @param Method|string $method
     * @return $this
     */
    public function setMethod($method) {
        $this->method = (string)$method;
        return $this;
    }

    /**
     * @param string $urlLive
     * @return $this
     */
    public function setUrlLive($urlLive)
    {
        $this->url_live = $urlLive;
        return $this;
    }

    /**
     * @param string $urlSandbox
     * @return $this
     */
    public function setUrlSandbox($urlSandbox)
    {
        $this->url_sandbox = $urlSandbox;
        return $this;
    }

    /**
     * @param string $urlTimeout
     * @param array $query (optional)
     * @return $this
     */
    public function setUrlTimeout($urlTimeout, $query=[])
    {
        $this->url_timeout = self::_urlQuery($urlTimeout, $query);
        if(!preg_match('/^https?\:\/\//',$this->url_timeout)) {
            $this->url_timeout = $this->getHostName().$this->url_timeout;
        }
        return $this;
    }

    /**
     * @param string $urlRedirect
     * @param array $query (optional)
     * @return $this
     */
    public function setUrlRedirect($urlRedirect, $query=[])
    {
        $this->url_redirect = self::_urlQuery($urlRedirect, $query);
        if(!preg_match('/^https?\:\/\//',$this->url_redirect)) {
            $this->url_redirect = $this->getHostName().$this->url_redirect;
        }
        return $this;
    }

    /**
     * @param string $uriLu
     * @param array $query (optional)
     * @return $this
     */
    public function setUriLu($uriLu, $query=[])
    {
        $this->uri_lu = self::_urlQuery($uriLu, $query);
        return $this;
    }

    /**
     * @param string $uriAlu
     * @param array $query (optional)
     * @return $this
     */
    public function setUriAlu($uriAlu, $query=[])
    {
        $this->uri_alu = self::_urlQuery($uriAlu,$query);
        return $this;
    }

    /**
     * @param string $uriIdn
     * @param array $query (optional)
     * @return $this
     */
    public function setUriIdn($uriIdn, $query=[])
    {
        $this->uri_idn = self::_urlQuery($uriIdn,$query);
        return $this;
    }

    /**
     * @param string $uriIrn
     * @param array $query (optional)
     * @return $this
     */
    public function setUriIrn($uriIrn, $query=[])
    {
        $this->uri_irn = self::_urlQuery($uriIrn,$query);
        return $this;
    }

    /**
     * @param string $uriIos
     * @param array $query (optional)
     * @return $this
     */
    public function setUriIos($uriIos, $query=[])
    {
        $this->uri_ios = self::_urlQuery($uriIos,$query);
        return $this;
    }

    /**
     * @param string $uriOc
     * @param array $query (optional)
     * @return $this
     */
    public function setUriOc($uriOc, $query=[])
    {
        $this->uri_oc = self::_urlQuery($uriOc,$query);
        return $this;
    }

    /**
     * @param array $merchants
     * @return $this
     */
    public function setMerchants($merchants)
    {
        $this->merchants = [];
        foreach($merchants as $currency=>$merchant) {
            $currency = strtoupper($currency);
            if(is_array($merchant)) {
                if(is_numeric($currency)) {
                    $currency = $merchant['currency'];
                }
                $merchant = new Merchant($currency,$merchant['id'],$merchant['key']);
            }
            if(!is_a($merchant, Merchant::class)) {
                continue;
            }
            $this->addMerchant($merchant->getCurrency(), $merchant->getId(), $merchant->getKey());
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
        $this->_data['merchants'][$currency] = new Merchant($currency,$id,$key);
        return $this;
    }

    /**
     * @param string $className
     * @return $this
     */
    public function setRequestClass($className)
    {
        $this->request_class = $className;
        return $this;
    }

    /**
     * @param string $className
     * @return $this
     */
    public function setFormClass($className) {
        $this->form_class = $className;
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
            return $this->server;
        }
        if(!array_key_exists($offset, $this->server)) {
            return null;
        }
        return $this->server[$offset];
    }

    /**
     * @param mixed $offset (optional)
     * @return array
     */
    public function getQuery($offset=null) {
        if($offset===null) {
            return $this->query;
        }
        if(!array_key_exists($offset, $this->query)) {
            return null;
        }
        return $this->query[$offset];
    }

    /**
     * @param mixed $offset (optional)
     * @return array
     */
    public function getPost($offset=null) {
        if($offset===null) {
            return $this->post;
        }
        if(!array_key_exists($offset, $this->post)) {
            return null;
        }
        return $this->post[$offset];
    }

    /**
     * @param mixed $offset (optional)
     * @return array
     */
    public function getRequest($offset=null) {
        if($offset===null) {
            return $this->request;
        }
        if(!array_key_exists($offset, $this->request)) {
            return null;
        }
        return $this->request[$offset];
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
        return $this->sandbox ? true : false;
    }

    /**
     * @return boolean
     */
    public function isDebug()
    {
        return $this->debug ? true : false;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return intval($this->timeout);
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getUrlLive()
    {
        return $this->url_live;
    }

    /**
     * @return string
     */
    public function getUrlSandbox()
    {
        return $this->url_sandbox;
    }

    /**
     * @return string
     */
    public function getUrlTimeout()
    {
        return $this->url_timeout;
    }

    /**
     * @return string
     */
    public function getUrlRedirect()
    {
        return $this->url_redirect;
    }

    public function getUrlBase() {
        if($this->isSandbox()) {
            return $this->getUrlSandbox();
        }
        return $this->getUrlLive();
    }

    /**
     * @return string
     */
    public function getUriLu()
    {
        return $this->getUrlBase().$this->uri_lu;
    }

    /**
     * @return string
     */
    public function getUriAlu()
    {
        return $this->getUrlBase().$this->uri_alu;
    }

    /**
     * @return string
     */
    public function getUriIdn()
    {
        return $this->getUrlBase().$this->uri_idn;
    }

    /**
     * @return string
     */
    public function getUriIrn()
    {
        return $this->getUrlBase().$this->uri_irn;
    }

    /**
     * @return string
     */
    public function getUriIos()
    {
        return $this->getUrlBase().$this->uri_ios;
    }

    /**
     * @return string
     */
    public function getUriOc()
    {
        return $this->getUrlBase().$this->uri_oc;
    }

    /**
     * @return Merchant[]
     */
    public function getMerchants()
    {
        return $this->merchants;
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
        $code = strtoupper((string)$code);
        if(array_key_exists($code, $this->_data['merchants'])) {
            return $this->merchants[$code];
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
     * @param string $urlTimeout
     * @param array $query (optional)
     * @return self
     */
    public function withUrlTimeout($urlTimeout, $query=[]) {
        return (new self($this))->setUrlTimeout($urlTimeout, $query);
    }

    /**
     * @param string $urlRedirect
     * @param array $query (optional)
     * @return self
     */
    public function withUrlBack($urlRedirect, $query=[]) {
        return (new self($this))->setUrlRedirect($urlRedirect, $query);
    }

    /**
     * @param string $uriLu
     * @param array $query (optional)
     * @return self
     */
    public function withUriLu($uriLu, $query=[]) {
        return (new self($this))->setUriLu($uriLu, $query);
    }

    /**
     * @param string $uriAlu
     * @param array $query (optional)
     * @return self
     */
    public function withUriAlu($uriAlu, $query=[]) {
        return (new self($this))->setUriAlu($uriAlu, $query);
    }

    /**
     * @param string $uriIdn
     * @param array $query (optional)
     * @return self
     */
    public function withUriIdn($uriIdn, $query=[]) {
        return (new self($this))->setUriIdn($uriIdn, $query);
    }

    /**
     * @param string $uriIrn
     * @param array $query (optional)
     * @return self
     */
    public function withUriIrn($uriIrn, $query=[]) {
        return (new self($this))->setUriIrn($uriIrn, $query);
    }

    /**
     * @param string $uriIos
     * @param array $query (optional)
     * @return self
     */
    public function withUriIos($uriIos, $query=[]) {
        return (new self($this))->setUriIos($uriIos, $query);
    }

    /**
     * @param string $uriOc
     * @param array $query (optional)
     * @return self
     */
    public function withUriOc($uriOc, $query=[]) {
        return (new self($this))->setUriOc($uriOc, $query);
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
            $k = self::underscore($k);
            if($k=='merchants') {
                foreach($v as $currency=>$merchant) {
                    $this->addMerchant($currency, $merchant['id'], $merchant['key']);
                }
                continue;
            }
            if($this->_isField($k)) {
                $m = 'set'.ucfirst(self::camelcase($k));
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
