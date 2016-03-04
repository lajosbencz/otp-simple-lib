<?php

namespace OtpSimple\Transaction;

use DOMDocument;
use OtpSimple\Config;
use OtpSimple\Enum\Status;
use OtpSimple\RequestInterface;
use OtpSimple\Transaction;

/**
 * Class OrderStatus
 * @package OtpSimple\Transaction
 * @property string $date
 * @property string $order_id
 * @property string $payment_id
 * @property string $status
 * @property string $method
 */
class OrderStatus extends Transaction
{
    protected static $_reverseMap = true;

    protected $_max_run = 1;

    protected function _describeFields() {
        return [
            'date' => ['name'=>'ORDER_DATE'],
            'order_id' => ['name'=>'REFNOEXT'],
            'payment_id' => ['name'=>'REFNO'],
            'status' => ['name'=>'ORDER_STATUS'],
            'method' => ['name'=>'PAYMETHOD'],
        ];
    }

    public function __construct(Config $config)
    {
        parent::__construct($config);

    }

    public function createHash() {
        return $this->config->getMerchant()->hash([$this->config->getMerchant()->getId(),$this->order_id]);
    }

    public function send() {

        $iosCounter = 0;
        $result = null;
        while ($iosCounter < $this->_max_run) {
            $c = $this->config->getRequestClass();
            /** @var RequestInterface $request */
            $request = new $c;
            $request->setUrl($this->config->getUriIos(), [
                'MERCHANT' => $this->config->getMerchant()->getId(),
                'REFNOEXT' => $this->order_id,
                'HASH' => $this->createHash(),
            ]);
            $result = $request->send();
            $this->status = Status::INVALID;
            if (!empty($result)) {
                $dom = new DOMDocument;
                $dom->loadXML($result);
                $order = $dom->getElementsByTagName("Order");
                $data = [];
                foreach($order->item(0)->childNodes as $item) {
                    if($item->nodeType == 1) {
                        $data[$item->tagName] = $item->nodeValue;
                    }
                }
                $this->mergeFields(self::renameFields($this->getFieldsMap(),$data, false));
            }
            switch ($this->status) {
                case 'NOT_FOUND':
                    $iosCounter++;
                    sleep(1);
                    break;
                case 'CARD_NOTAUTHORIZED':
                    $iosCounter += 5;
                    sleep(1);
                    break;
                default:
                    $iosCounter += $this->_max_run;
            }
        }
        return $result;
    }

    public function isSuccess() {
        return Status::isSuccess($this->status);
    }

}