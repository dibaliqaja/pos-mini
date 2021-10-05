<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a><h1 align="center">POS Mini</h1></p>

<p align="center">POS Mini is a simple project for an POS that is created to fulfill the initial coding test with a given requirement.</p>
<p align="center"><a href="https://github.com/dibaliqaja/pos-mini/releases" target="_blank"><img src="https://img.shields.io/badge/version-v0.0.1-red?style=for-the-badge&logo=none" alt="system version" /></a>&nbsp;<a href="https://github.com/dibaliqaja/pos-mini" target="_blank"><img src="https://img.shields.io/badge/Laravel-%5E8.54-fb503b?style=for-the-badge&logo=laravel" alt="laravel version" /></a>&nbsp;<img src="https://img.shields.io/badge/license-MIT-red?style=for-the-badge&logo=none" alt="license" /></p>

## Features
* [x] Master Data User
* [x] Master Data Product
* [x] Autentikasi

## Installation
1. Clone GitHub repo for this project locally
```bash
$ git clone https://github.com/dibaliqaja/pos-mini.git
```
2. Change directory in project which already clone
```bash
$ cd pos-mini
```
3. Install Composer dependencies
```bash
$ composer install
```
4. Create a copy of your .env file
```bash
$ cp .env.example .env
```
5. Generate an app encryption key
```bash
$ php artisan key:generate
```
6. Generate an app jwt key
```bash
$ php artisan jwt:secret
```
7. Create an empty database for our application

8. In the .env file, add database information to allow Laravel to connect to the database
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE={database-name}
DB_USERNAME={username-database}
DB_PASSWORD={password-database}
```
9. Migrate the database
```bash
$ php artisan migrate
```
10. Create a symbolic link from public/storage to storage/app/public 
```bash
$ php artisan storage:link
```
11. Make a folder for images
```bash
$ mkdir ./public/storage/images
```
12. Seed the database
```bash
$ php artisan db:seed
```
13. Running project
```bash
$ php artisan serve
```

## Admin Credentials in Seeder

**Admin:** budi@pos.com  
**Password:** password

## API Access
```
# Authentication
POST    {hostname}/api/v1/login         // Login User
POST    {hostname}/api/v1/refresh       // Refresh Token User
POST    {hostname}/api/v1/logout        // Logout User

# Master User
GET     {hostname}/api/v1/users         // List Users
GET     {hostname}/api/v1/user/{id}     // Show User by Id
POST    {hostname}/api/v1/user          // Create User
PUT     {hostname}/api/v1/user/{id}     // Update User
DELETE  {hostname}/api/v1/user/{id}     // Delete User

# Master Product
GET     {hostname}/api/v1/products      // List Products
GET     {hostname}/api/v1/product/{id}  // Show User by Id
POST    {hostname}/api/v1/product       // Create User
POST    {hostname}/api/v1/product/{id}  // Update User
DELETE  {hostname}/api/v1/product/{id}  // Delete User
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
