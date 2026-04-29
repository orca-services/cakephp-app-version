# Usage

Once the plugin is installed and loaded (see [Installation](Installation.md)), add the middleware to your middleware queue in `src/Application.php`:

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

Configure the header value and prefix as described in [Configuration](Configuration.md).

Every response will then include an HTTP header of the form:

```
{Prefix}-App-Version: {Version}
```

---

Back to the [Documentation](../README.md).
