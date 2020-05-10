<?php

require_once __DIR__ . '/bootstrap.php';

$backPage = new OtpSimple\Component\Page\RedirectPage($_GET);

include 'header.php';

echo '<pre>';
var_dump($backPage->toArray());
echo '</pre>';

include 'footer.php';
