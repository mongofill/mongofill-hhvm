#!/bin/bash

set -ev

# install hhvm-dev tools
if [ ! -e /hhvm-extensions/mongo.so ]
then
	echo "no mongo.so found, so build it"

	sudo add-apt-repository -y ppa:ubuntu-toolchain-r/test
	sudo apt-get update -qq
	sudo mkdir /etc/hhvm -p
	sudo touch /etc/hhvm/php.ini
	sudo chmod ugo+rw /etc/hhvm/php.ini

	sudo apt-get install -qq hhvm-dev g++-4.8 libboost-dev
	sudo update-alternatives --install /usr/bin/g++ g++ /usr/bin/g++-4.8 90

	# install libgoogle.log-dev
	wget http://launchpadlibrarian.net/80433359/libgoogle-glog0_0.3.1-1ubuntu1_amd64.deb
	sudo dpkg -i libgoogle-glog0_0.3.1-1ubuntu1_amd64.deb
	rm libgoogle-glog0_0.3.1-1ubuntu1_amd64.deb
	wget http://launchpadlibrarian.net/80433361/libgoogle-glog-dev_0.3.1-1ubuntu1_amd64.deb
	sudo dpkg -i libgoogle-glog-dev_0.3.1-1ubuntu1_amd64.deb
	rm libgoogle-glog-dev_0.3.1-1ubuntu1_amd64.deb

	# install libjemalloc
	wget http://ubuntu.mirrors.tds.net/ubuntu/pool/universe/j/jemalloc/libjemalloc1_3.6.0-2_amd64.deb
	sudo dpkg -i libjemalloc1_3.6.0-2_amd64.deb
	rm libjemalloc1_3.6.0-2_amd64.deb
	wget http://ubuntu.mirrors.tds.net/ubuntu/pool/universe/j/jemalloc/libjemalloc-dev_3.6.0-2_amd64.deb
	sudo dpkg -i libjemalloc-dev_3.6.0-2_amd64.deb
	rm libjemalloc-dev_3.6.0-2_amd64.deb

	# compile libbson
	wget https://github.com/mongodb/libbson/archive/master.tar.gz
	tar xzf master.tar.gz
	rm master.tar.gz
	cd libbson-master
	./autogen.sh
	./configure
	make
	sudo make install
	cd ..
	rm libbson-master -r

	sudo wget -O /usr/include/hphp/runtime/version.h https://gist.githubusercontent.com/digitalkaoz/dee32e5e82fc776925cf/raw/1b432ba7d4c477e9cc3f88b5bf408713bae3b6e5/version.h

	# compile mongofill-hhvm
	./build.sh || exit 1

	sudo mkdir /hhvm-extensions && sudo mv mongo.so /hhvm-extensions
fi

sudo echo "hhvm.dynamic_extension_path=/hhvm-extensions" >> /etc/hhvm/php.ini
sudo echo "hhvm.dynamic_extensions[mongo]=mongo.so" >> /etc/hhvm/php.ini

# show mongo PHP extension version
echo "ext-mongo version: `php -r 'echo phpversion(\"mongo\");'`"
