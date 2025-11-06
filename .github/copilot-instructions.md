# AI Agent Instructions for Project

This is a Laravel application using standard Laravel 10+ architecture with Vite and TailwindCSS for frontend asset handling.

## Project Architecture

### Key Components
- **HTTP Layer**: `app/Http/Controllers/` - HTTP request handling and responses
- **Models**: `app/Models/` - Eloquent ORM models (e.g., `User.php`)
- **Views**: `resources/views/` - Blade templates
- **Routes**: `routes/web.php` - Web route definitions
- **Config**: `config/` - Environment-specific configuration (reference `app.php` for patterns)
- **Tests**: `tests/` - PHPUnit tests following Laravel conventions

### Data Flow
1. Web requests enter through `public/index.php`
2. Routes in `routes/web.php` direct to controllers
3. Controllers interact with models and return views
4. Views are rendered using Blade templating

## Development Workflow

### Environment Setup
```bash
# Install PHP dependencies
composer install

# Install frontend dependencies
npm install

# Set up environment
cp .env.example .env
php artisan key:generate

# Run database migrations
php artisan migrate

# Start development servers
npm run dev     # Vite dev server
php artisan serve  # PHP development server
```

### Testing
- Tests follow Laravel's PHPUnit conventions
- Feature tests in `tests/Feature/`
- Unit tests in `tests/Unit/`
- Run tests with: `php artisan test`

### Asset Compilation
- Frontend assets are managed by Vite
- Development: `npm run dev`
- Production build: `npm run build`

## Project Conventions

### Configuration
- Environment variables in `.env` file
- Reference through `config()` helper or `env()` function
- Default configs in respective `config/*.php` files

### Database
- Migrations in `database/migrations/`
- Factories in `database/factories/`
- Seeders in `database/seeders/`

### Frontend
- TailwindCSS for styling
- JavaScript in `resources/js/`
- CSS in `resources/css/`
- Blade templates in `resources/views/`

## Common Tasks

### Creating New Features
1. Generate controller: `php artisan make:controller FeatureController`
2. Add routes in `routes/web.php`
3. Create views in `resources/views/`
4. Add tests in `tests/Feature/`

### Database Changes
1. Create migration: `php artisan make:migration create_table_name`
2. Add model: `php artisan make:model ModelName`
3. Optional: Add factory and seeder
4. Run migration: `php artisan migrate`

### Debugging
- Enable debug mode in `.env`: `APP_DEBUG=true`
- Check logs in `storage/logs/laravel.log`
- Use `dd()` or `dump()` helpers for inspection
- Laravel Telescope if installed (currently not present)