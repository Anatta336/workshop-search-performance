# Search Performance
This project has a customer search function that works correctly but was running very slow.

Your predecessor fixed the performance by adding a checkbox, but after careful examination the checkbox also makes it ignore most the customers. Please do a better job.

## Project Details
This is a project for learning. It's set up as a very minimal Laravel CRM with customers and orders. There's no support for editing any of the data, just viewing it and running a search on the customers.

Parts of how it is set up - especially the database - are intentionally done poorly so you can improve them.

The project is built on the Laravel 12 Vue starter kit and adapted to run in Docker locally. All of its requirements (including database) are handled within its set of containers.

The `.env` file is in version control for this example to make setup as fast as possible. If you put any real credentials in the `.env`, please first add it to `.gitignore`.

## Goal
The customer search on the dashboard is far slower than it should be. Your goal is to improve performance as much as possible.

You are free to change as much or as little of the project as you wish. If possible try to keep it all running within its own Docker containers. You are free to add/replace/remove containers and dependencies as you wish. You can rewrite it in COBOL if you really want to.

You are also free to update the automated tests. The existing tests do not need to keep passing.

The only limits are you can't just speed it up by deleting/ignoring data and the search functionality should remain as:
- Find customers who have a first or last name *starting* with the entered string.
    - Searching "hol" should find a customer named "Sherlock Holmes" but searching "lock" does not.
- If the search term has a space in it, the text before the space must match the start of the customer's first name and the text after must match the start of the customer's last name.
    - Searching "sher hol" should match a customer named "Sherlock Holmes".
- Show the full name and the total spend of customers who match.
- If there's more than 10 matching customers, only show 10 of them. Which 10 you show doesn't matter.
- What order you show the customers in doesn't matter.

Getting the customer search to run as fast as possible will earn the eternal respect of your peers.

## Local Setup

### First time
Assuming you already have a reasonably up to date install of Docker and git.

```
git clone https://github.com/Anatta336/workshop-search-performance.git
cd workshop-search-performance
docker compose up -d
docker compose run --rm composer install
docker compose run --rm app php artisan migrate:fresh --seed
```
The seeders create a very large number of records and may take a couple of minutes to complete.

Access the site at http://localhost:8089/dashboard

If you need to change the ports used, edit the `.env` file.

You should be able to register a new user and then log in.

When the project is no longer needed:
```
docker compose stop
```

### Normal use
```
docker compose up -d
```

The frontend assets are automatically rebuilt and hot loaded via the `node` service defined in `docker-compose.yml`, which creates a Docker container named `sp_node`.

To run artisan commands using the `app` service:
```
docker compose run --rm app php artisan inspire
```

To access composer using the `composer` service:
```
docker compose run --rm composer
```

To run tests using the `app` service:
```
docker compose run --rm app php artisan test
```

When the project is no longer needed:
```
docker compose stop
```
