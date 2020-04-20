# Cards backend using SF5 and API platform

## After git pull run:
```
composer install
bash reset.dev.db.sh
php bin/console app:cards:prepare black 53
php bin/console app:cards:prepare white 155 
```

## Run tests with:
```
./bin/phpunit
```

## Production deploy script:
```
bash deploy_prod.sh
```