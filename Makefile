.PHONY: dist-gen dist-clean dist clean test

composer:
	rm -r vendor  2> /dev/null || true
	composer install --prefer-dist --no-dev

npm-build:
	rm -r public/build 2> /dev/null || true
	rm -r node_modules 2> /dev/null || true
	npm install
	npm run build

dist-gen: clean composer npm-build
	@echo "packaging..."
	@mkdir InvoiceShelf
	@mkdir InvoiceShelf/public
	@cp -r app                              InvoiceShelf
	@cp -r bootstrap                        InvoiceShelf
	@cp -r config                           InvoiceShelf
	@cp -r database                         InvoiceShelf
	@cp -r public/build                     InvoiceShelf/public
	@cp -r public/favicons                  InvoiceShelf/public
	@cp -r public/.htaccess                 InvoiceShelf/public
	@cp -r public/index.php                 InvoiceShelf/public
	@cp -r public/robots.txt                InvoiceShelf/public
	@cp -r public/web.config                InvoiceShelf/public
	@cp -r resources                        InvoiceShelf
	@cp -r routes                           InvoiceShelf
	@cp -r storage                          InvoiceShelf
	@cp -r vendor                           InvoiceShelf 2> /dev/null || true
	@cp -r .env.example                     InvoiceShelf
	@cp -r artisan                          InvoiceShelf
	@cp -r composer.json                    InvoiceShelf
	@cp -r composer.lock                    InvoiceShelf
	@cp -r server.php                       InvoiceShelf
	@cp -r LICENSE                          InvoiceShelf
	@cp -r readme.md                        InvoiceShelf
	@cp -r SECURITY.md                      InvoiceShelf
	@touch InvoiceShelf/storage/logs/laravel.log

dist-clean: dist-gen
	find InvoiceShelf -wholename '*/[Tt]ests/*' -delete
	find InvoiceShelf -wholename '*/[Tt]est/*' -delete
	@rm -r InvoiceShelf/storage/framework/cache/data/* 2> /dev/null || true
	@rm    InvoiceShelf/storage/framework/sessions/* 2> /dev/null || true
	@rm    InvoiceShelf/storage/framework/views/* 2> /dev/null || true
	@rm    InvoiceShelf/storage/logs/* 2> /dev/null || true

dist: dist-clean
	@zip -r InvoiceShelf.zip InvoiceShelf

clean:
	@rm build/* 2> /dev/null || true
	@rm -r InvoiceShelf 2> /dev/null || true
	@rm -r public/build 2> /dev/null || true
	@rm -r node_modules 2> /dev/null || true
	@rm -r vendor  2> /dev/null || true

install: composer npm-build
	php artisan migrate

test:
	@if [ -x "vendor/bin/pest" ]; then \
		./vendor/bin/pest --stop-on-failure; \
	else \
		echo ""; \
		echo "Please install pest:"; \
		echo ""; \
		echo "  composer install"; \
		echo ""; \
	fi
