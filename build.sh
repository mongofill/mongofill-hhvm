#! /bin/sh
: ${HPHP_HOME?"HPHP_HOME environment variable must be set!"}

git submodule init
$HPHP_HOME/hphp/hhvm/hhvm tools/merge.php
$HPHP_HOME/hphp/tools/hphpize/hphpize
cmake .
make
