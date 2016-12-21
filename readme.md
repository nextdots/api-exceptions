# API Exceptions

Convert your **Lumen** exceptions into JSON:

    {
      "code": nnn,
      "message": "a message",
      "description": "a description",
      "errors": [
        "error 1",
        "error 2",
        ...
      ]
    }

## Installation

Set your `composer.json` to allow less stable packages:

    "minimum-stability" : "dev",  
    "prefer-stable" : true

Require the package as usual:

```bash
composer require nextdots/api-exceptions
```

Change `app/Exceptions/Handler.php`:

- Change this:

    ```php
    use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
    ```

- To this:

    ```php
    use ApiExceptions\Handler as ExceptionHandler;
    ```

On Linux, you can do the same as above with the following command:

```bash
sed -i 's/Laravel\\Lumen\\Exceptions\\Handler/ApiExceptions\\Handler/' app/Exceptions/Handler.php
```

## Example

```php
use ApiExceptions\JsonResponseException;

$app->get('/throw-exception', function () {
    throw new JsonResponseException(400, "an exception", "a description");
});
```
