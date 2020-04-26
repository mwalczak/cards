#!/bin/sh

docker-compose exec php php bin/console d:d:d --force -e dev
docker-compose exec php php bin/console d:d:c -e dev
docker-compose exec php php bin/console d:m:m -n -e dev
#php bin/console d:f:l -n -e dev
