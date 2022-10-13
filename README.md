# Store Apps Service API (Project Gabut)

<p align="center">
  <a href="https://laravel.com/">
    <img title="Laravel" src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400">
  </a>
</p>

---

## Prerequiste

-   [Composer](https://getcomposer.org/) - Download and Install Composer
-   Docker
-   Docker Compose Plugin

## Tools

-   Terminal (OhMyZSH)
-   Code Editor : Visual Studio Code
-   Web Server : Apache
-   Database Server : MySQL
-   GUI Database Management : DBeaver

## Stacks

-   PHP 7.4.3 
-   Laravel 8
-   Docker
-   Nginx
-   MySQL 

(Running inside Container)

## Installation

1. Clone repository

```bash
$ git clone https://github.com/Zainal21/store-apps-service.git
```

2. move to directory project and Install depedencies

```bash

$ cd store-apps-service/src

$ Composer install
```

4. Setup your environment variabl in `.env` files or rename `.env.example to .env` based on your configuration in your _docker-compose.yml_ file.

5. run create container and run your container use  docker compose

```bash

$ docker compose up -d

```

6. Generate your application key

```bash
$ docker compose exec php php /var/www/html/artisan key:generate
```

7. Run Migration and Seeder (if you not import .sql file manually)

```bash
$ docker compose exec php php /var/www/html/artisan migrate --seed
```

8. Open Your Browser in port 8080 (nginx expose port in 8080)

Copyright © 2022 by Muhamad Zainal Arifin
