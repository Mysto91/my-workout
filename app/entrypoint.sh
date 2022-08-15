#!/bin/sh

composer install

php bin/console lexik:jwt:generate-keypair

php -S 0.0.0.0:8000 -t public/