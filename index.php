<?php

use Keylink\Finder\Keyword;
use Keylink\Extra\Utils;
// 暂时用include  后面封装成composer包
include 'Finder/Keyword.php';
include 'Extra/Utils.php';

$keywords = new Keyword(['中华', 'he', 'she', 'his', 'hers']);

$result = $keywords->searchIn( 'She 在苏打水打算的爱=箭hishers迪生中she华的中撒所大撒的' );

print_r($result);

$result = Utils::mergeUniqueResult($result);

print_r($result);