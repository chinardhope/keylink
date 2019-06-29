<?php

use Keylink\Finder\Keyword;
use Keylink\Extra\Utils;
// 暂时用include  后面封装成composer包
include 'Finder/Keyword.php';
include 'Extra/Utils.php';

$keywords = new Keyword(['中华', 'he', 'she', 'his', 'hers']);

$result = $keywords->searchIn( 'She 在苏打水打算的爱=箭hishers迪生中she华的中撒he所大撒的' );

print_r($result);

$result = Utils::mergeUniqueResult($result);

print_r($result);


/**

[0] => Array
        (
            [0] => 1
            [1] => he
        )

    [1] => Array
        (
            [0] => 14
            [1] => his
        )

    [2] => Array
        (
            [0] => 16
            [1] => she
        )

    [4] => Array
        (
            [0] => 17
            [1] => hers
        )

    [5] => Array
        (
            [0] => 24
            [1] => she
        )


    [7] => Array
        (
            [0] => 31
            [1] => he
        )
1 14 16 17 24 31
*/