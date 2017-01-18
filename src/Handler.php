<?php

namespace ApiExceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exception\HttpResponseException;
use Symfony\Component\Debug\Exception\FlattenException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * The errors in the exception.
     *
     * @var array
     */
    private $errors;

    /**
     * Description for exception.
     *
     * @var string
     */
    private $description;

    /**
     * Handler constructor.
     */
    public function __construct()
    {
        $this->errors = [];

        $this->description = null;
    }

    public function render($request, Exception $e)
    {
        if ($e instanceof JsonResponseException) {
            return $e->getJsonResponse();
        }

        // create json from exception
        $content = $e->getMessage();

        if ($e instanceof HttpResponseException) {
            $code = 400;
        } elseif ($e instanceof ModelNotFoundException) {
            $code = 404;
        } elseif ($e instanceof AuthorizationException) {
            $code = 403;
        } elseif ($e instanceof ValidationException && $e->getResponse()) {
            $errors = json_decode($e->getResponse()->getContent());
            $this->errors = json_decode(json_encode($errors), true);
            $code = 422;
        }
        // add more cases here
        else {
            switch (FlattenException::create($e)->getStatusCode()) {
                case 404:
                    $code = 404;
                    $content = 'Sorry, the page you are looking for could not be found';
                    break;
                default:
                    $code = 500;
                    $content = 'Whoops, looks like something went wrong';
            }
        }

        return (new JsonResponseException($code, $content, $this->description, $this->errors))->getJsonResponse();
    }
}
