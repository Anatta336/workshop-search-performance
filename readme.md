# Search Performance

This is a small Laravel project, built on the Laravel 12 Vue starter kit. Adapted to run in Docker locally, with all its requirements (including database) handled within its set of containers.

## Local Setup

### First time
```
docker compose up -d
docker compose run --rm composer install
docker compose run --rm app php artisan migrate:fresh --seed
```
The seeders create a very large number of order records, and may take a couple of minutes to complete.

Access the site at http://localhost:8080

If you need to change the ports used, edit the `.env` file.

You should be able to register a new user and then log in.

You'll notice some of the pages are quite slow to load.

When the project is no longer needed:
```
docker compose down
```

### Normal use
```
docker compose up -d
```

The frontend assets are automatically rebuilt and hot loaded.

To run artisan commands:
```
docker compose run --rm app php artisan inspire
```

To access composer:
```
docker compose run --rm composer
```

When the project is no longer needed:
```
docker compose down
```
