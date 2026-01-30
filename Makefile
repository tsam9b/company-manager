.PHONY: up down build restart logs ps shell bash migrate seed test test-mail config-clear cache-clear assets install

COMPOSE ?= docker compose
APP_SERVICE ?= app

up:
	$(COMPOSE) up --build -d

down:
	$(COMPOSE) down

build:
	$(COMPOSE) build

restart:
	$(COMPOSE) down
	$(COMPOSE) up --build -d

logs:
	$(COMPOSE) logs -f

ps:
	$(COMPOSE) ps

shell:
	$(COMPOSE) exec $(APP_SERVICE) sh

bash:
	$(COMPOSE) exec $(APP_SERVICE) bash

migrate:
	$(COMPOSE) exec $(APP_SERVICE) php artisan migrate

seed:
	$(COMPOSE) exec $(APP_SERVICE) php artisan db:seed

test:
	$(COMPOSE) exec $(APP_SERVICE) php artisan test

test-mail:
	$(COMPOSE) exec $(APP_SERVICE) php artisan test --filter CompanyMailhogIntegrationTest

config-clear:
	$(COMPOSE) exec $(APP_SERVICE) php artisan config:clear

cache-clear:
	$(COMPOSE) exec $(APP_SERVICE) php artisan cache:clear

assets:
	$(COMPOSE) exec node sh -c "npm install && npm run build"

install:
	$(COMPOSE) exec $(APP_SERVICE) composer install --no-interaction --prefer-dist --optimize-autoloader

