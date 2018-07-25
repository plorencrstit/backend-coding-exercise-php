spawn:
	docker-compose up

test:
	vendor/bin/phpunit tests

test-cc:
	vendor/bin/phpunit --coverage-html coverage tests

example:
	bin/console app:exercise my-example 30/07/18 11:00 NW43QB 5

fixer:
	vendor/bin/php-cs-fixer fix --verbose --allow-risky yes --show-progress=estimating --config=.php_cs
