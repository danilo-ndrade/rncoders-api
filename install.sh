#!/bin/bash
if [ ! -e .env ]; then
    cp .env.example .env
fi

echo Uploading Application containeres
docker-compose up -d --build

echo Installing dependencies
docker exec -it app-ibmc composer install

echo Generating key
docker exec -it app-ibmc php artisan key:generate

echo Generating JWT Secret
php artisan jwt:secret

echo Making migrations
docker exec -it app-ibmc php artisan migrate

echo Seeding database
docker exec -it app-ibmc php artisan db:seed

echo generating storage link primeira vez
docker-compose exec app-ibmc php artisan storage:link

echo Information of new containers
docker ps -a 