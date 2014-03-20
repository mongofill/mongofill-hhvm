<?php
require_once('../mongofill/vendor/autoload.php');

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
    ],
    'MongoDate' => new MongoDate(strtotime('2012-12-11 00:00:00')),
    'MongoId' => new MongoId('4af9f23d8ead0e1d32000000'),
    'MongoRegexp' => new MongoRegex('/^Nicolas/i'),
    'MongoTimestamp' => new MongoTimestamp(strtotime('2012-12-11 00:00:00'))
];

var_dump(md5($data = bson_encode($array)));
var_dump($data);
var_dump(md5(bson_encode(['foo' => 'bar'])));