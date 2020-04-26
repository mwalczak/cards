#!/bin/sh

docker-compose exec php php bin/console d:d:d --force -e test
docker-compose exec php php bin/console d:d:c -e test
docker-compose exec php php bin/console d:m:m -n -e test
#php bin/console d:f:l -n -e test --group=AppFixtures
