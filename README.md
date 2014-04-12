Mongofill for HHVM
==============================

This package provides a drop-in replacement of the [official mongodb extension](https://github.com/mongodb/mongo-php-driver), as a HNI extension to be executed under HHVM runtime
 
The BSON encode and decode functions are implemented in C++ and the rest of the interface is in pure PHP thanks to    [Mongofill](https://github.com/mongofill/mongofill).

This HNI implementation is 3-5 times faster than the original mongofill extension in pure PHP, too provide a working phpversion("mongo") under HHVM, returning `1.4.5`, missed in the original mongofill.


Building and Installation
-------------------------
Installation requires a copy of HHVM to be built from source on the local machine, instructions on how to do this are available on the [HHVM Wiki](https://github.com/facebook/hhvm/wiki ). 

The library [libbson](https://github.com/mongodb/libbson) is required to be installed in the system, you can follow the instructions in the libbson repository.

Once done, the following commands will build the extension, assuming you've also installed HHVM.

```sh
git clone https://github.com/mongofill/mongofill-hhvm
cd mongofill-hhvm
./build.sh
```

This will produce a `mongo.so` file, the dynamically-loadable extension.

To enable the extension, you need to have the following section in your hhvm config file

```
DynamicExtensionPath = /path/to/hhvm/extensions
DynamicExtensions {
	* = mongo.so
}
```

Supported libraries
-------------------

You can check the current supported libraries at wiki page [Supported-Libraries](https://github.com/koubas/mongofill/wiki/Supported-Libraries)


Community
---------

You can catch us on IRC on Freenode channel #mongofill


Benchmarking
---------

A small suite of benchmarking is included with the [mongofill](https://github.com/mongofill/mongofill) package, you can run the suite with this command:

``` bash
php ./vendor/bin/athletic -b tests/bootstrap.php  -p tests/Mongofill/Benchmarks/
```

Some results can be find at: https://gist.github.com/mcuadros/9551290


License
-------

MIT, see [LICENSE](LICENSE)
