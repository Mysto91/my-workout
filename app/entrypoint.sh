#!/bin/sh

composer install

php -S 0.0.0.0:8000 -t public/