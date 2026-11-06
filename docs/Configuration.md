# Configuration

## App version

All outputs of the plugin read the version from CakePHP's `Configure` registry.
`App.version` is the **single source of truth**:

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

With the [header middleware](Usage.md#http-response-header) this will add a HTTP response header:

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
        'name' => 'app-version',
    ],
    // CLI
    'cli' => [
        'enabled' => true,  // the `bin/cake app_version` command
    ],
],
```

| Key                      | Purpose                                     | Default         |
|--------------------------|---------------------------------------------|-----------------|
| `AppVersion.meta.name`   | The `name` attribute of the meta tag        | `"app-version"` |
| `AppVersion.cli.enabled` | Register the `bin/cake app_version` command | `true`          |

The [version endpoint](Usage.md#version-endpoint) has no configuration,
it is enabled, placed and restricted by connecting its route in your application.

### Security considerations

Exposing the exact version publicly can help attackers to fingerprint your app.
That is why the plugin never connects the version endpoint on its own.
Connect its route only where you need it, e.g. only on staging, and restrict it with route middleware if necessary
(see [Version endpoint](Usage.md#version-endpoint)).

For the same reason the endpoint only returns the app version, but not the PHP or CakePHP version.
Those would tell attackers exactly which known vulnerabilities of the platform to try,
while they are of no use to deploy pipelines or monitors.
Use `bin/cake app_version --full` on the server if you need them.

---

Back to the [Documentation](../README.md).
