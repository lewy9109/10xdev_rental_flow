.DEFAULT_GOAL := help2

CONTAINER_NAME = rental_flow_php
DB_CONTAINER_NAME = rental_flow_db
ENV_FILE_PATH = .env

ifneq ("$(wildcard $(ENV_FILE_PATH))","")
    ENV_FILE = .env
else
    ENV_FILE = .env.dist
endif

include $(ENV_FILE)

.PHONY: start
start:  ## Docker compose up
	docker-compose --env-file $(ENV_FILE) up -d

.PHONY: stop
stop: ## Docker compose stop
	docker-compose --env-file $(ENV_FILE) stop

.PHONY: restart
restart: stop start ## Restart docker compose

.PHONY: down
down: ## Docker compose down
	docker-compose --env-file $(ENV_FILE) down -v

.PHONY: run-dev
run-dev: ## Run dev script
	bash docker/run-dev.sh

.PHONY: backend-rebuild
backend-rebuild: ## Run dev script
	docker-compose --env-file $(ENV_FILE) build --no-cache

.PHONY: exec
exec: ## Shell into container
	docker exec -it -u www-data $(CONTAINER_NAME) /bin/bash

.PHONY: coverage
coverage: ## Create tests coverage
	docker-compose --env-file $(ENV_FILE) exec -u www-data $(CONTAINER_NAME) /var/www/html/bin/phpunit tests --configuration /var/www/html/phpunit.xml.dist -d memory_limit=2048M --coverage-html /var/www/html/var/coverage

.PHONY: tests
tests: ## Run tests
	docker-compose --env-file $(ENV_FILE) exec -u www-data $(CONTAINER_NAME) /var/www/html/bin/phpunit

.PHONY: tests-unit
tests-unit: ## Run tests
	docker-compose --env-file $(ENV_FILE) exec -u www-data $(CONTAINER_NAME) /var/www/html/bin/phpunit --testsuite unit

.PHONY: tests-e2e
tests-e2e: ## Run tests
	docker-compose --env-file $(ENV_FILE) exec -u www-data $(CONTAINER_NAME) /var/www/html/bin/phpunit -c /var/www/html/phpunit.xml.dist --testsuite e2e

.PHONY: test
test: ## Run tests with filter ex. make test filter='App\\Tests\\Unit\\ExampleTest::testExample'
	docker-compose --env-file $(ENV_FILE) exec -u www-data $(CONTAINER_NAME) /var/www/html/bin/phpunit -c /var/www/html/phpunit.xml.dist --filter '$(filter)'

.PHONY: grumphp
grumphp: ## Run GrumPHP tasks
	docker-compose --env-file $(ENV_FILE) exec -u www-data $(CONTAINER_NAME) /var/www/html/vendor/bin/grumphp run

.PHONY: behat
behat: ## Run behat
	docker-compose --env-file $(ENV_FILE) exec -u www-data $(CONTAINER_NAME) sh /var/www/html/bin/behat -f progress

.PHONY: infection
infection: ## Run infection
	docker-compose --env-file $(ENV_FILE) exec -u www-data $(CONTAINER_NAME) sh /var/www/html/bin/infection

.PHONY: remove-logs
remove-logs: ## Remove logs
	docker-compose --env-file $(ENV_FILE) exec $(CONTAINER_NAME) rm -rf /var/www/html/var/log/*

.PHONY: composer-install
composer-install: ## Composer install
	docker-compose --env-file $(ENV_FILE) run -u www-data $(CONTAINER_NAME) composer install

.PHONY: composer-update
composer-update: ## Composer update
	docker-compose --env-file $(ENV_FILE) run -u www-data $(CONTAINER_NAME) composer update

.PHONY: composer-recipes
composer-recipes: ## Composer recipes
	docker-compose --env-file $(ENV_FILE) run -u www-data $(CONTAINER_NAME) composer recipes

.PHONY: cache
cache: ## Clears cache
	docker-compose --env-file $(ENV_FILE) exec -u www-data $(CONTAINER_NAME) /var/www/html/bin/console cache:clear

.PHONY: db-query
db-query: ## Run query on database. Example: make db-query query="select NOW()"
	docker-compose --env-file $(ENV_FILE) exec -u www-data $(CONTAINER_NAME) /var/www/html/bin/console doctrine:query:sql '$(query)'

.PHONY: db-create-dump
db-create-dump: ## Dump MariaDB database to docker/etc/db/vobis-db.dump file
	docker-compose --env-file $(ENV_FILE) exec $(DB_CONTAINER_NAME) bash -c "/usr/bin/mysqldump -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE}" > "docker/etc/db/${DB_DATABASE}-db.dump"

.PHONY: db-restore-dump
db-restore-dump: ## Restore MariaDB database dump from to docker/etc/db/vobis-db.dump file
	docker-compose --env-file $(ENV_FILE) exec -T $(DB_CONTAINER_NAME) bash -c "/usr/bin/mariadb -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE}" < "docker/etc/db/${DB_DATABASE}-db.dump"

.PHONY: fixtures
fixtures: ## Restore MariaDB database dump from to docker/etc/db/vobis-db.dump file
		docker-compose --env-file $(ENV_FILE) exec -u www-data $(CONTAINER_NAME) bin/console doctrine:fixtures:load --no-interaction

.PHONY: help
help: ## Alphabetically ordered help
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[32m%-20s\033[0m %s\n", $$1, $$2}'

.PHONY: help2
help2: ## Makefile ordered help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z0-9_-]+:.*?## / {gsub("\\\\n",sprintf("\n%22c",""), $$2);printf "\033[32m%-20s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

.PHONY: analyse
analyse:  ## Static analyse code by GrumPHP
		docker-compose --env-file $(ENV_FILE) exec $(CONTAINER_NAME) /var/www/html/vendor/bin/grumphp run

# Host-only helpers (no Docker). Run from repo root with local PHP/composer env.
.PHONY: analyse-host
analyse-host: ## Run GrumPHP locally (no Docker)
	cd app && ./vendor/bin/grumphp run || (echo "Hint: run 'cd app && composer install' first" && false)

.PHONY: phpstan-host
phpstan-host: ## Run PHPStan locally (no Docker)
	cd app && ./vendor/bin/phpstan analyse --configuration=phpstan.dist.neon || cd app && ./vendor/bin/phpstan analyse --configuration=phpstan.neon

.PHONY: tests-host
tests-host: ## Run PHPUnit locally (no Docker)
	cd app && ./bin/phpunit -c phpunit.xml.dist || cd app && ./bin/phpunit
.PHONY: tests-unit-1
tests-unit-1: ## Run tests
	docker-compose --env-file $(ENV_FILE) exec -u www-data $(CONTAINER_NAME) /var/www/html/bin/phpunit --testsuite Unit_1

.PHONY: tests-unit-2
tests-unit-2: ## Run tests
	docker-compose --env-file $(ENV_FILE) exec -u www-data $(CONTAINER_NAME) /var/www/html/bin/phpunit --testsuite Unit_2

.PHONY: run-rabbitmq
run-rabbitmq: # Run RabbitMQ
	docker-compose --file docker-compose.rabbitmq.yaml up -d
