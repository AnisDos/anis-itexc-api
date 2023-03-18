# Anis-Itexc-Api

> Simple Laravel Doctor's Office API [live preview](https://anis-itexc-api.hawesli-com.mon.world/)

This project runs with Laravel version 10.4.

## Getting started

Assuming you've already installed on your machine: PHP (>= 8.1.0), [Laravel](https://laravel.com), [Composer](https://getcomposer.org) and [Node.js](https://nodejs.org).

``` bash
# install dependencies
composer update
composer install
npm install

# create .env file and generate the application key
cp .env.example .env
php artisan key:generate

# create database
php artisan migrate
php artisan db:seed


```

Then launch the server:

``` bash
php artisan serve
```

The Laravel sample project is now up and running! Access it at http://localhost:8000.

use postman to test.

