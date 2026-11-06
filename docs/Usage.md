# Usage

Once the plugin is installed and loaded (see [Installation](Installation.md)), the app version can be exposed in several ways.

All of them read the same `App.version` value (see [Configuration](Configuration.md)).

| Output                                        | Example                                             | Available                                           |
|-----------------------------------------------|-----------------------------------------------------|-----------------------------------------------------|
| [HTTP response header](#http-response-header) | `X-App-Version: 1.2.3`                              | When the middleware is added                        |
| [View helper](#view-helper)                   | `$this->AppVersion->get()`                          | When the helper is loaded                           |
| [HTML meta tag](#html-meta-tag)               | `<meta name="app-version" content="1.2.3">`         | When `meta()` is used in a template or layout       |
| [Version endpoint](#version-endpoint)         | `GET /app-version.json` → `{"app-version":"1.2.3"}` | When you connect its route                          |
| [CLI command](#cli-command)                   | `bin/cake app_version`                              | Always (unless `AppVersion.cli.enabled` is `false`) |

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

<?= $this->Html->script('app.js?v=' . $this->AppVersion->getUrlEncoded()) ?>
```

`get()` returns the raw version, escape it with `h()` when printing it in HTML.
`getUrlEncoded()` returns the URL encoded version, e.g. for cache busting query strings.
Both return `"Unknown"` if `App.version` is not set.

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

The name can be changed with `AppVersion.meta.name`.

To append it to the `meta` view block instead, pass `['block' => true]`.

The version can then be read from frontend code or E2E tests:

```js
document.querySelector('meta[name="app-version"]')?.content;
```

## Version endpoint

The plugin does not connect any route on its own, so the endpoint is only available if you connect it
in your application's `config/routes.php`:

```php
$routes->scope('/', function (RouteBuilder $builder): void {
    $builder->get(
        '/app-version',
        ['plugin' => 'AppVersion', 'controller' => 'Version', 'action' => 'index'],
        'AppVersion.version',
    )->setExtensions(['json', 'html']);
});
```

You are free to choose the path, and you decide in which environments the route is connected.

The response format depends on the file extension:

```
$ curl https://example.com/app-version.json
{"app-version":"1.2.3"}

$ curl https://example.com/app-version.html
$ curl https://example.com/app-version
<!DOCTYPE html> ... App version: <span class="app-version">1.2.3</span> ...
```

Without an extension, HTML is returned. The HTML template can be overridden in your application with
`templates/plugin/AppVersion/Version/index.php`.

This lets deploy pipelines, smoke tests, uptime monitors and load balancers verify which release is live.
The response is sent with `Cache-Control: no-store`.

Only the app version is returned, deliberately not the PHP or CakePHP version, see
[Security considerations](Configuration.md#security-considerations).

To restrict access, apply route middleware the same way as for any other route of your application:

```php
$routes->scope('/', function (RouteBuilder $builder): void {
    $builder->registerMiddleware('versionAuth', new MyIpAllowListMiddleware());

    $builder->get(
        '/app-version',
        ['plugin' => 'AppVersion', 'controller' => 'Version', 'action' => 'index'],
        'AppVersion.version',
    )
        ->setExtensions(['json', 'html'])
        ->setMiddleware(['versionAuth']);
});
```

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
