#!/bin/bash

#cd /var/www/transpo.ptmkapamilya.org
#git pull origin main
yarn install
composer install --no-dev --optimize-autoloader
sudo chown -R www-data:www-data /var/www/geoapp.ptmkapamilya.org
sudo chmod -R 775 /var/www/transpo.ptmkapamilya.org/bootstrap/cache
sudo chmod -R 775 /var/www/transpo.ptmkapamilya.org /var/www/transpo.ptmkapamilya.org/storage
php artisan route:cache
php artisan config:cache
php artisan view:cache
#cp -r /var/www/transpo.ptmkapamilya.org.old/storage/app/profile /var/www/transpo.ptmkapamilya.org/storage/app/
php artisan storage:link