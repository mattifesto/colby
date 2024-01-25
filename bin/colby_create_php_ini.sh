#!/usr/bin/env sh

sed \
    -E \
    -e 's/^(;?upload_max_filesize *=).*$/\1 64M/g' \
    -e 's/^(;?post_max_size *=).*$/\1 65M/g' \
    -e 's/^;?(date.timezone *=).*$/\1 "UTC"/g' \
    "$PHP_INI_DIR/php.ini-production" > "$PHP_INI_DIR/php.ini"
