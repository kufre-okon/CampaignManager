## Campaign Manager App

A simple SPA laravel application for managing advertising campaigns. It enables creating new campaigns with creative banners (multiple file uploads), ability to edit already created campaigns as well as listing page for all created campaigns with caching system. Cache invalidation is handled by event, triggered when creating or editing campaign entry.

### Deployment
- Clone this repository.

#### Environment Setup (Docker)
- assuming docker is already installed on the host system and running
- cd into the cloned project directory and run the following commands
```bash
$ cp .env.example .env
$ composer install
$ php artisan key:generate
```
- cloned laradock repository into the main project
```bash
$ git clone https://github.com/laradock/laradock.git
```
- cd into the laradock directory and copy the .env.example to .env
```bash
$ cp .env.example .env
```
- open the .env and change `APACHE_DOCUMENT_ROOT=/var/www` to `APACHE_DOCUMENT_ROOT=/var/www/public`
- run up the containers and wait for it to finish(may take quite some time)
```bash
$ docker-compose up -d --build apache2 mysql
```
- create the database
```bash
$ docker-compose exec mysql bash
  mysql -uroot -proot
  create database campaign_db;
  exit
exit
```
- run migrations and other workspace tasks
```bash
$ docker-compose exec workspace bash
   php artisan migrate
   php artisan storage:link
exit
```
- run tests (uses model factories to seed database)
```bash
$ docker-compose exec workspace bash
   php artisan test
exit
```
- heads to your browser, open http://localhost:{port}/ or http://127.0.0.1:{port}/ 
_if you didn’t change APACHE_HOST_HTTP_PORT in the .env file in {your-project}/laradock, the port will remains 80._
