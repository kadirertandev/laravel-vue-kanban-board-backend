# laravel-vue-kanban-board-backend

[Frontend Repository](https://github.com/kadirertandev/laravel-vue-kanban-board-frontend)

### Install dependencies

```sh
composer install
```

### Generate application key

```sh
php artisan key:generate
```

### Copy environment file

```sh
cp .env.example .env
```

**Change the database name using .env file**

### Seed the database

```sh
php artisan migrate:fresh --seed
```

### Serve the application

```sh
php artisan serve
```
