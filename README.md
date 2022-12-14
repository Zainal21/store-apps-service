# Store Apps Service API (Gabut Apps)

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

## Features / API Resources

- [x]  Authentication with JWT (JSON Web Token)
- [x]  CRUD Product
- [x]  CRUD Product Category
- [x]  CRUD Product Gallery
- [x]  Manage Cart Item
- [x]  Create Order (Midtrans Payment Gateway)
- [x]  Callback Notification When Transaction Approve/settlement (Midtrans Payment Gateway)

- [ ]   Push Notification With Email & Whatsapp (Up Coming)
- [ ]   Another Payment gateway integrated (Up Coming)
- [ ]   Excel Export and Import Transaction (Up Coming)


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

5. change directory to root folder then run create container and run your container using docker compose

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

8. Open in your browser in port 8080 (because nginx expose port in 8080)



## Insomnia Collection (Example)

You can access in this link https://store-apps-documentation.netlify.app/

---------------------------------------------------------------------------

Copyright © 2022 by Muhamad Zainal Arifin
