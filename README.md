- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Project Quick Start

Start the stack:

```bash
# build and start containers
docker compose up --build -d

# open the app at http://127.0.0.1:8000
# open MailCatcher UI at http://127.0.0.1:1080
```

Notes:
- MailCatcher listens on SMTP port 1025 and the web UI at 1080.
- The app service mounts the project directory into the container and runs `php artisan serve` on port 8000.
- Environment variables for mail are set in `docker-compose.yml` (MAIL_HOST=mailcatcher, MAIL_PORT=1025).

To stop the stack:

```bash
docker compose down
```

# Implementations

- ✅ Basic Laravel Auth (login/logout). Registration is disabled in routes; login works via auth controllers.
- ✅ Admin seed user (admin@admin.com / password).
- ✅ CRUD for Companies and Employees. UI pages exist and API endpoints handle create/read/update/delete.
- ✅ Companies table fields (name, email, logo, website) in migration.
- ✅ Employees table fields (first name, last name, company_id, email, phone) in migration.
- ✅ Migrations created for both schemas.
- ✅ Company logos stored on public disk and served via /storage (public/storage exists; Storage::url used).
- ✅ Resource controllers with default methods. Routes now use `Route::resource` with standard controller methods.
- ✅ Validation via Request classes. Dedicated FormRequest classes for company/employee.
- ✅ Pagination (10 per page) via paginate() and UI defaultPerPage=10.
- ✅ Starter kit auth + theme; registration removed (register routes commented, canRegister false).
- ✅ Multilanguage via lang folder implemented (JSON files in resources/lang used by vue-i18n).
- ✅ PHPUnit tests present (Feature tests for Company/Employee/Profile/Auth).

- Dummy data are imported via seeders for testing.


## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Docker (development with MailCatcher)

A simple Docker Compose setup is included to run the application and MailCatcher for local email testing.

