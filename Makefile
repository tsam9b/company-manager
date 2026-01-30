dev:
	npm run dev

serve:
	php artisan serve

migrate:
	php artisan migrate

migrate-status:
	php artisan migrate:status

seed:
	php artisan db:seed

link:
	php artisan storage:link

test:
	php artisan test

dev-full:
	@echo "Starting Laravel development services..."
	@echo "Open multiple terminals and run:"
	@echo "  Terminal 1: make serve"
	@echo "  Terminal 2: make dev"
	@echo "  Terminal 3: make queue (optional)"
	@echo "  Terminal 4: make logs (optional)"

queue:
	php artisan queue:listen --tries=1

logs:
	php artisan pail --timeout=0