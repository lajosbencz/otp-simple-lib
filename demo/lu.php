<?php

require_once dirname(__DIR__).'/vendor/autoload.php';


include 'header.php';

if(count($_POST)>0) {
    echo '<pre>';
    var_dump($_POST);
    echo '</pre>';
}

include 'footer.php';
