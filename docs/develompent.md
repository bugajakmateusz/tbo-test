# Development Documentation

## Project Structure

- **src/** – Main web application directory. Contains business logic, controllers, models, and services.
- **tests/** – Contains unit and integration tests for the application. Tests are executed using PHPUnit and PHPStan.
- **landings/frontend/** – Directory containing the application's frontend code. This includes JavaScript/TypeScript files, CSS styles, and user interface components.
- **python/** – Additional script sending daily report to headquarters.

## Prerequisites
- Docker (required for running the application)

## Available Services and Ports

- **haproxy**: Available on `localhost:3100`
- **nginx**: Internal service, not directly exposed
- **fpm (PHP)**: Internal service
- **postgres (Database)**: Available on `localhost:3011` (development) and `localhost:3012` (testing)
- **mailhog (SMTP Testing)**: Available on `localhost:3013`

## Running the Application

To start the application, run the following command:

```bash
docker compose up
```

This will build and start all services.

## Accessing the Application

The application is accessible at:

```
http://localhost:3100
```

## Database Information

The database is initially empty. Default login credentials are:

- **Username**: root
- **Password**: polsl-admin
- **Host**: localhost
- **Port**: 3011

## Seeding the Database

To seed the database with initial data, run:

```bash
make seed-db
```

This will populate the database with initial records and create users with random emails but the same password:

```
polsl-admin
```

## Running Tests

To run unit tests and static analysis, execute:

```bash
make test
```

This will run PHPUnit and PHPStan to ensure code quality.


## Python script
Daily reports are send to provided email everyday at 9 AM. Email and cron details may be customised in ``python/main.py``

## Emulating snack sell
Selling snack can be emulated by command ``app:snacks:sell`` with ``snack_id``, ``machine_id`` and ``position`` parameters.

## Setting envs
Remember to set password's salt, database connection details and SMTP server when using production environment.
Those parameters are preset for development environment.