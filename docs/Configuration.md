# Configuration

## App version

All outputs of the plugin read the version from CakePHP's `Configure` registry.
`App.version` is the **single source of truth**:

| Key                 | Purpose                               | Fallback    |
|---------------------|---------------------------------------|-------------|
| `App.version`       | The version string to expose          | `"Unknown"` |
| `App.versionPrefix` | The vendor prefix for the header name | `"X"`       |

Set them in `config/app.php` (or `config/app_local.php`), like so:

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

To keep the version in sync with your release process, read it dynamically in `config/bootstrap.php`
from a version file:

```php
use AppVersion\AppVersion;

Configure::write('App.version', AppVersion::extractVersion());
```

By default, `AppVersion::extractVersion()` reads the `VERSION` file in your application's root directory
(`AppVersion::DEFAULT_VERSION_FILE`) and removes leading and trailing whitespace.
Pass another path to read a different file, e.g. `AppVersion::extractVersion(CONFIG . 'version.txt')`.
If the file is missing, unreadable or empty, `"Unknown"` is returned.

Make sure this runs before the middleware queue is built.

---

Continue with the [usage](./Usage.md) or Back to the [documentation](../README.md).
