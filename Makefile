default: run-unit-tests

.DELETE_ON_ERROR:

.PHONY: \
	run-unit-tests

vendor: composer.lock
	composer install
	touch "$@"

# If composer.lock doesn't exist at all,
# this will 'composer install' for the first time.
# After that, it's up to you to 'composer update' to get any
# package updates or apply changes to composer.json.
composer.lock: | composer.json
	composer install

run-unit-tests: vendor
	vendor/bin/phpsimplertest --bootstrap vendor/autoload.php test
