.PHONY: list
list:
	@$(MAKE) -pRrq -f $(lastword $(MAKEFILE_LIST)) : 2>/dev/null | awk -v RS= -F: '/^# File/,/^# Finished Make data base/ {if ($$1 !~ "^[#.]") {print $$1}}' | sort | egrep -v -e '^[^[:alnum:]]' -e '^$@$$'

.PHONY: remigrate-db
remigrate-db:
	docker compose exec --user=www-data fpm vendor/bin/phinx rollback -t 0 --force
	docker compose exec --user=www-data fpm vendor/bin/phinx migrate

.PHONY: seed-db
seed-db:
	docker compose exec -T --user=www-data fpm composer db:seed

.PHONY: clear-seed-db
clear-seed-db: remigrate-db seed-db

.PHONY: test
test:
	docker compose exec --user=www-data fpm composer app:checks
	docker compose exec --user=www-data fpm php tests/bad-bots-test.php
	docker compose exec --user=www-data fpm vendor/bin/phpunit

.PHONY: check-phpstan-without-cache
check-phpstan-without-cache:
	docker compose exec --user=www-data fpm bin/console c:c -e test
	docker compose exec --user=www-data fpm bin/console c:w -e test
	docker compose exec --user=www-data fpm sh -c "rm -rf /tmp/phpstan"
	docker compose exec --user=www-data fpm composer phpstan:check

.PHONY: cs-fix
cs-fix:
	docker compose exec --user=www-data fpm composer cs:fix

.PHONY: restart-proxies
restart-proxies:
	docker compose exec haproxy haproxy -c -V -f /usr/local/etc/haproxy/haproxy.cfg
	docker compose restart haproxy

.PHONY: regenerate-baseline
regenerate-baseline:
	docker compose exec --user=www-data fpm composer phpstan:check -- --generate-baseline

test-unit-integration:
	docker compose exec --user=www-data fpm composer cs:check
	docker compose exec --user=www-data fpm composer phpstan:check
	docker compose exec --user=www-data fpm vendor/bin/phpunit --group 'default'