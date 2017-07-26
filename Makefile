# vim: set noet ts=4 sw=4 :

DIR_ASSETS = ./src/Navicu/InfrastructureBundle/Resources/public/assets

NODE := $(shell which node)
ifndef NODE
NODE := docker exec -it navicu_node_1 node
endif

RUBY := $(shell which ruby)

ifndef RUBY
RUBY := $(shell which /.rvm/bin/rvm-auto-ruby)
endif

ifndef RUBY
RUBY := docker exec -it navicu_ruby_1 ruby
endif

PHP := $(shell which php)
ifndef PHP
PHP := docker exec -it navicu_php-apache_1 php
endif

GRUNT = $(NODE) ./node_modules/.bin/grunt
BOWER = $(NODE) ./node_modules/.bin/bower
WEBPACK = $(NODE) ./node_modules/.bin/webpack
DOCKERCOMPOSE = docker-compose


build: assets
.PHONY: build

clean: clean_assets
.PHONY: clean

distclean: clean
.PHONY: distclean

watch:
	$(WEBPACK) --watch-poll --display-error-details
.PHONY: watch

assets: images_prod css js_prod
.PHONY: assets

clean_assets: clean_js
.PHONY: clean_assets

clean_js: distclean_js
.PHONY: clean_js

distclean_js:
	find '$(DIR_ASSETS)/navicu/dist/angular-apps/' -name '*.js' -exec $(RM) {} \;
.PHONY: clean_js

js:
	$(WEBPACK) --display-error-details
.PHONY: js

js_prod:
	NODE_ENV=production $(WEBPACK) --bail --display-error-details
.PHONY: js_prod

css:
	$(GRUNT)
.PHONY: css

images_prod: js_prod
	$(RUBY) ./scripts/include-assets
.PHONY: images_prod

images: js
	$(RUBY) ./scripts/include-assets
.PHONY: images

bower:
	$(BOWER) install
.PHONY: bower

services: up
.PHONY: services

up:
	$(DOCKERCOMPOSE) up --build
.PHONY: up

down:
	$(DOCKERCOMPOSE) down
.PHONY: down

reset:
	git checkout web/builds/
.PHONY: reset

s:
	$(PHP) app/console d:m:s
.PHONY: s

m:
	$(PHP) app/console d:m:m
.PHONY: m

sleep:
	sleep 1m
.PHONY: sleep
