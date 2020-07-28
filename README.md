# Laravel Settings

This package allows you to save the configuration in a more persistent way. Use the database to save your settings, you can save values in json format. You can also override the Laravel configuration.

## Getting Started

### 1. Install

Run the following command:

```bash
composer require byancode/settings
```

### 2. Register (for Laravel > 7.0)

Register the service provider in `config/app.php`

```php
Byancode\Settings\Provider::class,
```

Add alias if you want to use the facade.

```php
'Setting' => Byancode\Settings\Facade::class,
```

### 3. Publish

Publish config file.

```bash
php artisan vendor:publish --provider="Byancode\Settings\Provider"
```


### 4. Configure

You can change the options of your app from `config/settings.php` file

## Usage

You can either use the helper method like `settings('foo')` or the facade `Settings::get('foo')`

### Facade

```php
Settings::get('foo');
Settings::set('foo', 'bar');
$settings = Settings::all();
```

### Helper

```php
settings('foo');
settings('foo', 'bar');
$settings = settings();
```

### Blade Directive

You can get the settings directly in your blade templates using the helper method or the blade directive like `@settings('foo')`
