APP_NAME=app
IMAGE_NAME=myapp-image
COMPOSE=docker compose

.PHONY: build up down db-setup queue-consumer clean logs default

default: build up install db-setup

prepare-env:
	@if [ ! -f .env ]; then cp .env.example .env; fi

build: prepare-env
	@echo "Building the Docker image..."
	@docker build -t $(IMAGE_NAME) .

up:
	@echo "Starting services with Docker Compose..."
	@$(COMPOSE) up -d $(APP_NAME) db mailhog rabbitmq

install:
	@echo "Installing dependencies..."
	@$(COMPOSE) exec $(APP_NAME) composer install

db-setup:
	@echo "Setting up the database..."
	@$(COMPOSE) exec $(APP_NAME) php bin/console orm:schema-tool:create

queue-consumer:
	@echo "Starting the queue consumer..."
	@$(COMPOSE) exec $(APP_NAME) php bin/console app:queue-consumer

down:
	@echo "Stopping services with Docker Compose..."
	@$(COMPOSE) down

clean:
	@echo "Cleaning up containers, volumes, and networks..."
	@$(COMPOSE) down -v --remove-orphans

logs:
	@echo "Viewing logs..."
	@$(COMPOSE) logs -f

test:
	@echo "Running unit tests..."
	@$(COMPOSE) exec $(APP_NAME) composer test
