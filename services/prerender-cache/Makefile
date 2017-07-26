# vim: set noet ts=4 sw=4 :

NODE = node
NPM = npm

ESLINT = $(NODE) ./node_modules/.bin/eslint -c ./.eslintrc.js

JS_FILES = ./bin/www $(shell find . -type f -name '*.js' ! -path './node_modules/*' ! -path './.eslint*')

all: lint
.PHONY: all

lint: eslint
.PHONY: lint

eslint:
	$(ESLINT) $(JS_FILES)
.PHONY: eslint
