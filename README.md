# Cards backend using SF5 and API platform

## Start docker with:
```
cp docker-compose-dist.yml docker-compose.yml
cp docker/nginx/default-dev.conf docker/nginx/default.conf
docker-compose up -d
docker-compose exec php composer install
docker-compose exec php bash reset_dev_db.sh
docker-compose exec php php bin/console app:cards:prepare black 53
docker-compose exec php php bin/console app:cards:prepare white 155 
```

### Setup permissions:
```
docker-compose exec php chmod u+x bin/console bin/phpunit
```

## Run tests with:
```
cp phpunit.xml.dist phpunit.xml
#import cards using above command afeter changing dev db dsn to test
docker-compose exec php bash reset_test_db.sh
docker-compose exec php php bin/phpunit
```

## Production deploy script:
```
bash deploy_prod.sh
```