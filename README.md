# laravel-vue-kanban-board-backend

[Frontend Repository](https://github.com/kadirertandev/laravel-vue-kanban-board-frontend)

### Install dependencies

```sh
composer install
```

### Copy environment file

```sh
cp .env.example .env
```

**Change the database name using .env file**

### Generate application key

```sh
php artisan key:generate
```

### Seed the database

```sh
php artisan migrate:fresh --seed
```

### Serve the application

```sh
php artisan serve
```
