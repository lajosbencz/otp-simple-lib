<?php

namespace OtpSimple;

use OtpSimple\Transaction\LiveUpdate;

interface FormInterface
{
    /**
     * @param LiveUpdate $liveUpdate
     * @return $this
     */
    function setLiveUpdate(LiveUpdate $liveUpdate);

    /**
     * @param string $action
     * @return $this
     */
    function setAction($action);

    /**
     * @param string $id
     * @return $this
     */
    function setId($id);

    /**
     * @param bool|string $button
     * @return string
     */
    function getHtml($button=false);

    /**
     * @param string $text
     * @param array $attributes
     * @return mixed
     */
    function getButton($text='Submit', $attributes=[]);

}
