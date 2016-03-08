#
# Main tasks
#
install: install-infra install-vendors

update: update-composer

#
# Directories & files
#
bin:
	mkdir -p bin/ || /bin/true
bin/composer: bin
	curl -sS https://getcomposer.org/installer | php -- --install-dir=bin --filename=composer
	chmod +x bin/composer || /bin/true
bin/php-cs-fixer: bin
	wget http://get.sensiolabs.org/php-cs-fixer.phar -O bin/php-cs-fixer
	chmod +x bin/php-cs-fixer || /bin/true
update-bin: bin/composer bin/php-cs-fixer
	./bin/composer self-update
	php bin/php-cs-fixer self-update
.git/hooks/pre-commit:
	curl https://raw.githubusercontent.com/LinkValue/symfony-git-hooks/master/pre-commit -o .git/hooks/pre-commit
	chmod +x .git/hooks/pre-commit || /bin/true

install-infra: update-bin .git/hooks/pre-commit

#
# Librairies
#
install-composer: bin/composer
	./bin/composer install
update-composer: bin/composer
	./bin/composer update --no-scripts

install-vendors: install-composer

#
# Tests
#
tests: test-phpunit-coverage

test-phpunit:
	./vendor/phpunit/phpunit/phpunit src -c phpunit.xml.dist

test-phpunit-coverage:
	rm -rf tests-coverage/* || /bin/true
	./vendor/phpunit/phpunit/phpunit src -c phpunit.xml.dist --coverage-html tests-coverage

#
# CI
#
ci-install-composer: bin/composer
	./bin/composer install --prefer-dist
bin/ocular:
	wget https://scrutinizer-ci.com/ocular.phar -O bin/ocular
	chmod +x bin/ocular || /bin/true

travis: ci-install-composer bin/ocular
	./vendor/phpunit/phpunit/phpunit src -c phpunit.xml.dist --coverage-clover=coverage.clover
	php bin/ocular code-coverage:upload --format=php-clover coverage.clover
