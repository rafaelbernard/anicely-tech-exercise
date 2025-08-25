COMPOSER_AUTH='{ "bitbucket-oauth": { "bitbucket.org": { "consumer-key": "$(BITBUCKET_CONSUMER_KEY)", "consumer-secret": "$(BITBUCKET_CONSUMER_SECRET)" } } }'

help:           ## Show this help.
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'

bash:		## bash into console container
	docker-compose exec app sh

build: up

build-nd:		## Build console, login and api - no-detached
	docker-compose up --build --remove-orphans

down:
	docker-compose down --rmi=local --remove-orphans --volumes

fix-permissions:		## Fix file permissions
	sudo chown -R $(whoami): .

logs:
	docker compose logs -f

stop-all:		## Stop all running containers 
	docker stop $$(docker ps -q)

up: up-nd

up-nd:
	docker-compose up --build --remove-orphans
up-d:
	docker-compose up --build --remove-orphans -d

assets-dev:		# Build frontned assets
	docker-compose exec app npm run dev

clear-all:
	sleep 5
	rm -rf public/build/
	rm -rf var
	rm -rf node_modules
	rm -rf vendor

purge: down fix-permissions clear-all
