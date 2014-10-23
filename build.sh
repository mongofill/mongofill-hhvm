#! /bin/sh
git submodule update --init
hhvm tools/merge.php
hphpize
cmake .
make
