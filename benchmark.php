<?php

function test($iterations) {
    $doc = [
        'string' => 'foo',
        'int' => 32,
        'float' => 32.12,
        'field4' => 'value',
        'field5' => 'value',
        'field6' => 'value',
        'field7' => 'value',
        'field8' => 'value',
        'field9' => 'value',
    ];
    for ($i = 0; $i < $iterations; ++$i) {
        $bson = bson_encode($doc);
    }
}

$iterations = 2000000;
$s = microtime(true);
test($iterations);
$elapsed = microtime(true) - $s;
echo number_format($iterations / $elapsed) . " ops/s\n";