# Usage

Once the plugin is installed and loaded (see [Installation](Installation.md)), the app version can be exposed in several ways.
All of them read the same `App.version` value (see [Configuration](Configuration.md)).

| Output                                          | Example                                     | Enabled by default |
|-------------------------------------------------|---------------------------------------------|--------------------|
| [HTTP response header](#http-response-header)   | `X-App-Version: 1.2.3`                      | When added         |
| [View helper](#view-helper)                     | `$this->AppVersion->get()`                  | When loaded        |
| [HTML meta tag](#html-meta-tag)                 | `<meta name="app-version" content="1.2.3">` | No                 |
| [Version endpoint](#version-endpoint)           | `GET /version` → `{"version":"1.2.3"}`      | No                 |
| [CLI command](#cli-command)                     | `bin/cake app_version`                      | Yes                |

## HTTP response header

Add the middleware to your middleware queue in `src/Application.php`:

```php
use AppVersion\Middleware\AppVersionHeaderMiddleware;

public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
{
    $middlewareQueue
        ->add(new AppVersionHeaderMiddleware())
        // ...your other middleware
        ->add(new ErrorHandlerMiddleware(Configure::read('Error'), $this));

    return $middlewareQueue;
}
```

Add `AppVersionHeaderMiddleware` **before** `ErrorHandlerMiddleware` if you want the header to be present on error responses as well.

Every response will then include an HTTP header of the form:

```
{Prefix}-App-Version: {Version}
```

## View helper

Load the helper in your `src/View/AppView.php`:

```php
public function initialize(): void
{
    parent::initialize();

    $this->addHelper('AppVersion.AppVersion');
}
```

Then use it in your templates, e.g. in a footer:

```php
<footer>Version <?= h($this->AppVersion->get()) ?></footer>

<?= $this->Html->script('app.js?v=' . rawurlencode($this->AppVersion->get())) ?>
```

`get()` returns `"Unknown"` if `App.version` is not set.

## HTML meta tag

Add the meta tag to your layout, e.g. `templates/layout/default.php`:

```php
<head>
    <?= $this->AppVersion->meta() ?>
</head>
```

This renders:

```html
<meta name="app-version" content="1.2.3">
```

The tag is only rendered when `AppVersion.meta.enabled` is `true`, so you can keep the call in your layout
and switch the tag on or off per environment. The name can be changed with `AppVersion.meta.name`.

To append it to the `meta` view block instead, pass `['block' => true]`.

The version can then be read from frontend code or E2E tests:

```js
document.querySelector('meta[name="app-version"]')?.content;
```

## Version endpoint

Enable the endpoint in your configuration:

```php
'AppVersion' => [
    'endpoint' => ['enabled' => true],
],
```

The plugin then connects a `GET` route (named `AppVersion.version`) that returns the version as JSON:

```
$ curl https://example.com/version
{"version":"1.2.3"}
```

This lets deploy pipelines, smoke tests, uptime monitors and load balancers verify which release is live.
The response is sent with `Cache-Control: no-store`.

The path can be changed with `AppVersion.endpoint.path` and access can be restricted with route middleware,
see [Configuration](Configuration.md#security-considerations).

Note: The plugin's `routes` hook must be enabled (it is by default when loading the plugin).

## CLI command

Print the version on the server or in deploy scripts without HTTP access:

```
$ bin/cake app_version
1.2.3

$ bin/cake app_version --full
PHP 8.3.6, CakePHP 5.3.6 (debug: false), app version 1.2.3
```

The command can be disabled with `AppVersion.cli.enabled = false`.

---

Back to the [Documentation](../README.md).
