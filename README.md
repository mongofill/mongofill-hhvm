BSON for the HipHop PHP Virtual Machine
==============================

bson_encode and bson_decode functions for HHVM, a wrapper of libbson library

Building and Installation
-------------------------
Installation requires a copy of HHVM to be built from source on the local machine, instructions on how to do this are available on the [HHVM Wiki](https://github.com/facebook/hhvm/wiki ). Once done, the following commands will build the extension, assuming you've also installed HHVM.

```sh
git clone https://github.com/mcuadros/bson-hni
cd bson-hni
./build.sh
```

This will produce a `bson.so` file, the dynamically-loadable extension.

To enable the extension, you need to have the following section in your hhvm config file

```
DynamicExtensionPath = /path/to/hhvm/extensions
DynamicExtensions {
	* = bson.so
}
```

Functions
--------

- ```bson_encode``` — Serializes a PHP variable into a BSON string
- ```bson_decode``` - Deserializes a BSON object into a PHP array
- ```bson_encode_multiple``` (internal) — Serializes an PHP array of variable into BSON documents
- ```bson_decode_multiple``` (internal) — Deserializes multiple BSON objects into a nested PHP array

License
-------

MIT, see [LICENSE](LICENSE)
