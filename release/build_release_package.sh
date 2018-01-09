#!/bin/sh

ZIP_FOLDER="shopgate_base-${TRAVIS_TAG}"
ZIP_FILE="${ZIP_FOLDER}.zip"

# Remove mentioning the src/ structure when packaging
sed -i 's/src\///g' composer.json

mkdir release/${ZIP_FOLDER}
rsync -a ./src/ release/${ZIP_FOLDER}
rsync -av --exclude-from './release/exclude-list.txt' ./ release/${ZIP_FOLDER}
cd ./release
zip -r ${ZIP_FILE} ${ZIP_FOLDER}
wget https://raw.githubusercontent.com/magento/marketplace-tools/master/validate_m2_package.php
php validate_m2_package.php ${ZIP_FILE}
mv ${ZIP_FILE} ${TRAVIS_BUILD_DIR}/${ZIP_FILE}
