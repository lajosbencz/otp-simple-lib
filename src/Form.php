<?php

namespace OtpSimple;

use OtpSimple\Transaction\LiveUpdate;

class Form implements FormInterface
{
    /** @var string */
    protected $_action;
    /** @var string */
    protected $_id = 'otp_simple_form';
    /** @var LiveUpdate */
    protected $_liveUpdate;

    protected function _createField($name, $value)
    {
        $config = &$this->_liveUpdate->config;
        if($config->isDebug()) {
            $this->_liveUpdate->log->debug($name.': '.$value);
        }
        return '<input type="hidden" name="'.$name.'" value="'.$value.'" />'."\r\n";
    }

    public function setId($id) {
        $this->_id = $id;
        return $this;
    }

    public function setAction($action) {
        $this->_action = $action;
        return $this;
    }

    public function setLiveUpdate(LiveUpdate $liveUpdate) {
        $this->_liveUpdate = $liveUpdate;
        return $this;
    }

    public function getHtml($button=false) {
        $html = ''."\r\n";
        $html.= '<form action="'.$this->_action.'" method="post" id="'.$this->_id.'" name="'.$this->_id.'">'."\r\n";
        foreach($this->_liveUpdate->getData() as $name=>$field) {
            if(is_array($field)) {
                foreach($field as $subField) {
                    $html.= $this->_createField($name."[]", $subField);
                }
            }
            elseif(!is_array($field)) {
                $html.= $this->_createField($name, $field);
            }
        }
        //$html.= $this->_createField('SDK_VERSION', $this->_liveUpdate->getVersion());
        if($button) {
            $html.= $this->getButton(is_string($button)?$button:'Submit');
        }
        $html.= '</form>'."\r\n";
        return $html;
    }

    public function getButton($text='Submit', $attributes=[]) {
        $attr = '';
        foreach($attributes as $k=>$v) {
            if($k=='form') {
                continue;
            }
            $v = str_replace('"','',$v);
            $attr.= ' '.$k.'="'.$v.'"';
        }
        return '<button type="submit" form="'.$this->_id.'"'.$attr.'>'.$text.'</button>'."\r\n";
    }
}
