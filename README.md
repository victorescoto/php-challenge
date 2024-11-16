
# Slim Challenge

This project is a PHP application built with the Slim framework and designed for a challenge. It includes various dependencies and utilizes Docker for containerization.

## Requirements

- Docker - [Install Docker](https://docs.docker.com/get-docker/)
  - Version: Docker version 27.2.0, build 3ab4256
- Docker Compose - [Install Docker Compose](https://docs.docker.com/compose/install/)
  - Version: docker-compose version 1.29.2, build 5becea4c

## Technologies

- PHP 8.3
- Slim 4
- Doctrine ORM
- MySQL
- RabbitMQ
- Mailhog
- PHPUnit
- Guzzle
- JWT
- PHPMailer

## Features

- User registration and authentication
- Stock data retrieval
- Stock query history
- Queue consumer for sending emails

## Architecture

The application is built with a layered architecture, separating the concerns of the application into different layers:

- **app**: Contains the application code.
  - **Commands**: Contains the command classes that handle the business logic of the application.
  - **Controllers**: Contains the controller classes that handle the HTTP requests and responses.
  - **Entities**: Contains the entity classes that represent the database tables.
  - **Exceptions**: Contains the exception classes that handle the application errors.
  - **Middlewares**: Contains the middleware classes that handle the HTTP requests and responses.
  - **Services**: Contains the service classes that handle the business logic of the application.
  - **Validators**: Contains the validator classes that handle the validation of the request data.
- **bin**: Contains the executable scripts for the application.
- **config**: Contains the configuration files for the application.
- **public**: Contains the public files for the application.
- **templates**: Contains the template files for the application.
- **tests**: Contains the test files for the application.

The application also includes a queue consumer that listens for messages on the `emails` queue and sends emails to the users.

## Database Schema

The application uses a MySQL database with the following schema:

- **users**: Contains the user information.
- **stock_queries**: Contains the stock query history.

## Setup

### 1. Clone the Repository

```bash
git clone <repository_url>
cd <repository_name>
```

### 2. Initial Setup

```bash
make
```

This will copy `.env.example` to `.env` if `.env` does not already exist, build the Docker image, start the necessary services (app, db, mailhog, rabbitmq), and set up the database schema.

### 3. Running Unit Tests

To run the unit tests:

```bash
make test
```

### 4. Running the Queue Consumer

To run the queue consumer:

```bash
make queue-consumer
```

This will start the queue consumer and listen for messages on the `emails` queue.

## Additional Commands

- **Stop Services:** `make down`
- **Clean Up:** `make clean`
- **View Logs:** `make logs`

## Links

- **App**: [http://localhost:8000](http://localhost:8000)
- **Mailhog**: [http://localhost:8025](http://localhost:8025)
- **RabbitMQ**: [http://localhost:15672](http://localhost:15672)

## Routes

- **User Register**: Send a POST request to `/users` with `email` and `password` to register the user.
- **User Authentication**: Send a POST request to `/auth/login` to authenticate a user and receive a JWT token.
- **Fetch Stock Data**: Send a GET request to `/stock/?q={symbol}` with a valid JWT token to retrieve stock information.
- **Fetch Stock Query History**: Send a GET request to `/history` with a valid JWT token to retrieve the history of stock queries made by the user.
