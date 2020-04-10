#!/usr/bin/env bash
git pull
composer install
composer dump-env prod
composer dump-autoload --no-dev --classmap-authoritative
php bin/console doctrine:migration:migrate -n
php bin/console cache:warmup