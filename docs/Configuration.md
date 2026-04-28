# Configuration

The plugin reads two values from CakePHP's `Configure` registry:

| Key                 | Purpose                               | Default     |
|---------------------|---------------------------------------|-------------|
| `App.version`       | The version string to expose          | `"Unknown"` |
| `App.versionPrefix` | The vendor prefix for the header name | `"X"`       |

Set them in `config/app.php` (or `config/app_local.php`):

```php
'App' => [
    'version'       => '1.2.3',
    'versionPrefix' => 'Acme',
],
```

This will produce the HTTP response header:

```
Acme-App-Version: 1.2.3
```

To keep the version in sync with your release process, read it dynamically in `config/bootstrap.php`, for example from a `VERSION` file:

```php
Configure::write(
    'App.version',
    trim((string)@file_get_contents(ROOT . DS . 'VERSION')) ?: 'Unknown'
);
```

Make sure this runs before the middleware queue is built.

---

Back to the [Documentation](../README.md).
