# Makefile for Laravel Docker setup

.PHONY: build up down restart install composer-install artisan

# Build the Docker images and start containers
build:
	docker-compose build

# Start the Docker containers
up:
	docker-compose up -d

# Stop and remove the containers
down:
	docker-compose down

# Restart the containers
restart:
	docker-compose restart

# Run Composer Install (uses composer container)
composer-install:
	docker-compose run --rm composer install

# Run Artisan commands (useful for Laravel migrations, etc.)
artisan:
	docker-compose exec app php artisan $(cmd)
