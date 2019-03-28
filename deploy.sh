#!/bin/bash

update () {
    # Turn on maintenance mode
    # php artisan down

    /usr/local/bin/composer install --no-dev --optimize-autoloader

    # Cache boost configuration and routes
    php artisan cache:clear

    # Clear and cache config
    php artisan config:clear
    php artisan config:cache

    php artisan route:clear
    php artisan view:clear

    # Sync database changes
    php artisan migrate --force

    # Restart workers
    #php artisan queue:restart

    # Install npm packages & compile scss files
    #npm install
    #npm run production

    # Turn off maintenance mode
    # php artisan up
}

cd "$(dirname ${BASH_SOURCE[0]})"

git fetch --tags
git checkout $1

if ! [[ $1 =~ ^v?[0-9\.]+$ ]]
then
    if [[ $(git merge origin/$1) != 'Already up-to-date.' ]]
    then
        update
    fi
else
    update
fi

