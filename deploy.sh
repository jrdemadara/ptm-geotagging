#!/bin/bash
# cp -r /var/www/geoapp.ptmkapamilya.org /var/www/geoapp.ptmkapamilya.org.backup
git reset --hard
git checkout -- .
git clean -fd
git fetch origin
yarn install
composer install --no-dev --optimize-autoloader
sudo chown -R www-data:www-data /var/www/geoapp.ptmkapamilya.org
sudo chmod -R 775 /var/www/geoapp.ptmkapamilya.org/bootstrap/cache
sudo chmod -R 775 /var/www/geoapp.ptmkapamilya.org /var/www/geoapp.ptmkapamilya.org/storage
php artisan route:cache
php artisan config:cache
php artisan view:cache
php artisan key:generate