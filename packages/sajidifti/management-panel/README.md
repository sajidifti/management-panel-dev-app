# Laravel Management Panel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sajidifti/management-panel.svg?style=flat-square)](https://packagist.org/packages/sajidifti/management-panel)
[![Total Downloads](https://img.shields.io/packagist/dt/sajidifti/management-panel.svg?style=flat-square)](https://packagist.org/packages/sajidifti/management-panel)

A database-independent management panel for Laravel applications that works even when your database is down. Perfect for emergency maintenance, system management, and troubleshooting.

## Features

- ✅ **Zero Database Dependency** - Works even when database is unavailable
- ✅ **File-Based Sessions** - Independent session management
- ✅ **Prebuilt Assets** - No NPM/Vite required
- ✅ **Emergency Access** - Manage your app during outages
- ✅ **Artisan Commands** - Execute commands via web interface
- ✅ **Environment Management** - Edit .env variables through UI
- ✅ **Maintenance Mode** - Toggle with bypass URL generation
- ✅ **System Information** - View PHP, Laravel, and server details
- ✅ **Standalone Auth** - Uses .env credentials, no database needed

## Installation

### Step 1: Install via Composer

```bash
composer require sajidifti/management-panel
```

### Step 2: Publish Assets (Optional)

```bash
php artisan vendor:publish --tag=management-panel-config
php artisan vendor:publish --tag=management-panel-assets
```

### Step 3: Configure Environment

Add these variables to your `.env` file:

```env
# Management Panel Configuration
MANAGEMENT_ROUTE_PREFIX=management/secret
MANAGEMENT_USERNAME=admin
MANAGEMENT_PASSWORD=your-secure-password
MANAGEMENT_SESSION_LIFETIME=120
```

**Important:** Change `MANAGEMENT_ROUTE_PREFIX` to something hard to guess for security!

### Step 4: Access the Panel

Navigate to: `https://yourdomain.com/management/secret` (or your custom prefix)

## Configuration

The package is configured via `config/management.php`:

```php
return [
    'route_prefix' => env('MANAGEMENT_ROUTE_PREFIX', 'management/secret'),
    'username' => env('MANAGEMENT_USERNAME', 'admin'),
    'password' => env('MANAGEMENT_PASSWORD', 'password'),
    'session' => [
        'driver' => 'file',
        'lifetime' => env('MANAGEMENT_SESSION_LIFETIME', 120),
        'path' => storage_path('framework/management_sessions'),
        'cookie' => 'management_session_id',
    ],
];
```

## Usage

### Accessing the Panel

1. Navigate to your configured route prefix
2. Login with credentials from `.env`
3. Use the interface to manage your application

### Features Available

#### System Information

- PHP Version
- Laravel Version
- Environment Mode
- Debug Status
- Database Connection

#### Artisan Commands

- Optimization commands (cache, config, routes)
- Clear cache commands
- Database migrations
- Queue management
- Custom commands

#### Environment Management

- Edit .env variables through secure UI
- Grouped by category (App, Database, Mail, etc.)
- Safe updates with confirmation

#### Maintenance Mode

- Toggle maintenance mode on/off
- Generate secure bypass URLs
- Copy bypass URL to clipboard

## How It Works

### Database Independence

The management panel is completely independent of your application's database:

1. **File-Based Sessions**: Uses its own session storage in `storage/framework/management_sessions/`
2. **No Web Middleware**: Bypasses Laravel's database-dependent middleware
3. **Standalone Routes**: Loads without web middleware group
4. **No Database Queries**: Authentication uses .env, not database

### Security Features

- ✅ Customizable route prefix
- ✅ Environment-based credentials
- ✅ File-based authentication
- ✅ HTTPOnly session cookies
- ✅ CSRF protection
- ✅ Secure password handling

## Requirements

- PHP 8.1 or higher
- Laravel 10.x or 11.x
- Write permissions for `storage/framework/management_sessions/`

## Prebuilt Assets

This package ships with prebuilt Tailwind CSS in the `public` directory, so consumers do not need Node.js or NPM to use the package in production.

**Development / Rebuilding Assets**

If you need to modify the frontend and rebuild the assets, the package includes a Vite + Tailwind v4 build setup. From the package root run:

```pwsh
cd packages/sajidifti/management-panel
npm install
npm run build
```

The build writes compiled JS/CSS into the package `public` directory. After building, publish the package assets into your application with:

```pwsh
php artisan vendor:publish --tag=management-panel-assets
```

Notes:
- The package `package.json` and build config are intended for package maintainers. Built assets should be committed to the package `public` folder so consumers don't need a build step.
- The build outputs are placed in `public/js` and `public/css` inside the package and will be published to `public/vendor/management-panel` by the service provider.

## Troubleshooting

### Panel Not Loading

1. Check that route prefix is correct
2. Clear config cache: `php artisan config:clear`
3. Verify storage permissions: `storage/framework/management_sessions/` needs to be writable

### Session Issues

```bash
# Clear management sessions
php artisan management:clean-sessions
```

### Cannot Access During Database Outage

This is the main feature! The panel should work even when:

- Database is down
- Tables don't exist
- Connection fails
- During migrations

If it's not working, check your view composers aren't querying the database.

## Advanced Configuration

### Custom Session Path

```php
// config/management.php
'session' => [
    'path' => storage_path('custom/session/path'),
],
```

### Custom Middleware

Register additional middleware in `bootstrap/app.php`:

```php
$middleware->alias([
    'management.custom' => \App\Http\Middleware\CustomManagementMiddleware::class,
]);
```

## Security Best Practices

1. **Change Default Route**: Use a unique, hard-to-guess route prefix
2. **Strong Credentials**: Use strong username and password
3. **HTTPS Only**: Always use HTTPS in production
4. **IP Whitelist**: Consider adding IP restrictions
5. **Regular Audits**: Monitor access logs

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Credits

- [Sajid Ifti](https://github.com/sajidifti)
- [All Contributors](../../contributors)

## Support

For support, email <support@jssoftsolution.com> or open an issue on GitHub.
