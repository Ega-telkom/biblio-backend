.PHONY: dev dev-build dev-rebuild dev-down prod prod-build prod-down prod-migrate migrate fresh seed cache shell shell-prod logs down restart client swagger

# ─── Auto-detect podman / docker ───────────────────────────
DOCKER  := $(shell command -v podman 2>/dev/null || command -v docker)
COMPOSE := $(shell command -v podman-compose 2>/dev/null || echo "$(DOCKER) compose")

DEV_FILE  := docker-compose.dev.yml
PROD_FILE := docker-compose.yml

$(info Using: $(DOCKER))

# ─── Development ───────────────────────────────────────────
dev:
	$(COMPOSE) -f $(DEV_FILE) up -d

dev-build:
	$(COMPOSE) -f $(DEV_FILE) up -d --build
	
dev-rebuild:
	$(COMPOSE) -f $(DEV_FILE) build --no-cache && $(COMPOSE) -f $(DEV_FILE) up -d

dev-down:
	$(COMPOSE) -f $(DEV_FILE) down

# ─── Production ────────────────────────────────────────────
prod:
	$(COMPOSE) -f $(PROD_FILE) up -d

prod-build:
	$(COMPOSE) -f $(PROD_FILE) up -d --build

prod-down:
	$(COMPOSE) -f $(PROD_FILE) down

prod-migrate:
	$(COMPOSE) -f $(PROD_FILE) exec app php artisan migrate --force

# ─── Artisan ───────────────────────────────────────────────
migrate:
	$(COMPOSE) -f $(DEV_FILE) exec app php artisan migrate

fresh:
	$(COMPOSE) -f $(DEV_FILE) exec app php artisan migrate:fresh --seed

seed:
	$(COMPOSE) -f $(DEV_FILE) exec app php artisan db:seed

cache:
	$(COMPOSE) -f $(PROD_FILE) exec app php artisan config:cache
	$(COMPOSE) -f $(PROD_FILE) exec app php artisan route:cache
	$(COMPOSE) -f $(PROD_FILE) exec app php artisan view:cache

# ─── Utilities ─────────────────────────────────────────────
shell:
	$(COMPOSE) -f $(DEV_FILE) exec app sh

shell-prod:
	$(COMPOSE) -f $(PROD_FILE) exec app sh

logs:
	$(COMPOSE) -f $(DEV_FILE) logs -f app

down:
	$(COMPOSE) -f $(DEV_FILE) down

restart:
	$(COMPOSE) -f $(DEV_FILE) restart app

client:
	npx @openapitools/openapi-generator-cli generate \
		-i storage/api-docs/api-docs.json \
		-g kotlin \
		--library jvm-retrofit2 \
		-o ./generated-client \
		--additional-properties packageName=com.example.biblio

swagger:
	php artisan l5-swagger:generate