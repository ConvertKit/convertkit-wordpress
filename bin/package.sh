#!/usr/bin/env bash

TMPDIR=${TMPDIR-/tmp}
TMPDIR=$(echo $TMPDIR | sed -e "s/\/$//")
RELEASE_VERSION=${1-master}
DOWNLOAD_LOCATION=${2-$HOME/tmp}

download() {
    curl -s "$1" > "$2";
}

download https://codeload.github.com/ConvertKit/convertkit-wordpress/zip/$RELEASE_VERSION $DOWNLOAD_LOCATION/convertkit.zip

cd $DOWNLOAD_LOCATION

unzip convertkit.zip

rm convertkit.zip

NEW_DIR=$(echo $RELEASE_VERSION | sed -e 's/\//-/g')
cd ConvertKit-WordPress-$NEW_DIR

if [ -e composer.json ]
then
    composer install --no-dev
fi

if [ -e .distignore ]
then
    wp dist-archive ./ $DOWNLOAD_LOCATION/convertkit-packaged.zip
fi

