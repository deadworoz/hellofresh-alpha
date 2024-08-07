DOCKER_COMPOSE = docker compose --project-directory ./ --file docker/compose.yaml
build::
	$(DOCKER_COMPOSE) run --rm --user `id -u`:`id -g` --build php composer install

shell::
	$(DOCKER_COMPOSE) run --rm --user `id -u`:`id -g` php

run::
	$(DOCKER_COMPOSE) --progress quiet run --rm --user `id -u`:`id -g` php ./bin/console app:recipe-stats-calculator $(params)
