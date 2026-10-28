# Configuration

## App version

All outputs of the plugin read the version from CakePHP's `Configure` registry.
`App.version` is the single source of truth:

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

With the [header middleware](Usage.md#http-response-header) this will produce the HTTP response header:

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

## Output options

The additional output options are configured below the `AppVersion` key.
These are all keys with their defaults:

```php
'AppVersion' => [
    // HTML meta tag rendered by $this->AppVersion->meta()
    'meta' => [
        'enabled' => false,
        'name'    => 'app-version',
    ],
    // JSON endpoint, e.g. GET /version → {"version":"1.2.3"}
    'endpoint' => [
        'enabled'    => false,
        'path'       => '/version',
        'middleware' => [],
    ],
    // CLI
    'cli' => [
        'enabled' => true,  // the `bin/cake app_version` command
        'banner'  => false, // version line before every CLI command
    ],
],
```

| Key                              | Purpose                                                                    | Default         |
|----------------------------------|----------------------------------------------------------------------------|-----------------|
| `AppVersion.meta.enabled`        | Render the HTML meta tag                                                   | `false`         |
| `AppVersion.meta.name`           | The `name` attribute of the meta tag                                       | `"app-version"` |
| `AppVersion.endpoint.enabled`    | Connect the JSON version endpoint                                          | `false`         |
| `AppVersion.endpoint.path`       | The path of the endpoint                                                   | `"/version"`    |
| `AppVersion.endpoint.middleware` | Names of route middleware to apply to the endpoint, e.g. to restrict it    | `[]`            |
| `AppVersion.cli.enabled`         | Register the `bin/cake app_version` command (and allow the banner)         | `true`          |
| `AppVersion.cli.banner`          | Print PHP, CakePHP and app version before every CLI command (to stderr)    | `false`         |

### Security considerations

Exposing the exact version publicly can help attackers to fingerprint your app.
That is why the outputs reachable from outside the server (meta tag and endpoint) are disabled by default.

Enable them only where you need them, e.g. per environment in `config/app_local.php`:

```php
// config/app_local.php on staging
'AppVersion' => [
    'meta'     => ['enabled' => true],
    'endpoint' => ['enabled' => true],
],
```

To restrict the endpoint, register a route middleware in your application's `config/routes.php`
and reference it by name:

```php
// config/routes.php
$routes->registerMiddleware('versionAuth', new MyIpAllowListMiddleware());
```

```php
// config/app.php
'AppVersion' => [
    'endpoint' => [
        'enabled'    => true,
        'middleware' => ['versionAuth'],
    ],
],
```

---

Back to the [Documentation](../README.md).
