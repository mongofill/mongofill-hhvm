<?php

var_dump(md5(bson_encode(['foo', 'bar', 21])));

$array = [
    'string' => 'qux',
    'int' => 12,
    'bool' => true,
    'null' => null,
    'double' => 12.12,
    'array' => [
        'string' => 'qux',
        'int' => 12,
        'bool' => true,
        'null' => null,
        'double' => 12.12
    ]
];

var_dump(md5($data = bson_encode($array)));
var_dump($data);
var_dump(md5(bson_encode(['foo' => 'bar'])));
