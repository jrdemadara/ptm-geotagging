#!/bin/bash

#cd /var/www/geoapp.ptmkapamilya.org
git pull origin main
yarn install
composer install --no-dev --optimize-autoloader
sudo chown -R www-data:www-data /var/www/geoapp.ptmkapamilya.org
sudo chmod -R 775 /var/www/geoapp.ptmkapamilya.org/bootstrap/cache
sudo chmod -R 775 /var/www/geoapp.ptmkapamilya.org /var/www/geoapp.ptmkapamilya.org/storage
php artisan route:cache
php artisan config:cache
php artisan view:cache
cp -r /var/www/ptm-geotagging/storage/app/profile /var/www/geoapp.ptmkapamilya.org/storage/app/
#php artisan storage:link