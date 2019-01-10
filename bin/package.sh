#!/usr/bin/env bash

TMPDIR=${TMPDIR-/tmp}
TMPDIR=$(echo $TMPDIR | sed -e "s/\/$//")
RELEASE_VERSION=${1-master}
DOWNLOAD_LOCATION=${2-$HOME/tmp}

download() {
    curl -s "$1" > "$2";
}

download https://github.com/ConvertKit/ConvertKit-WordPress/archive/$RELEASE_VERSION.zip $DOWNLOAD_LOCATION/convertkit.zip

cd $DOWNLOAD_LOCATION
unzip convertkit.zip
NEW_DIR=$(echo $RELEASE_VERSION | sed -e 's/\//-/g')
cd ConvertKit-WordPress-$NEW_DIR

composer install --no-dev