<?php

namespace ApiExceptions;

use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\Debug\Exception\FlattenException;

use ApiExceptions\JsonResponseException;

class Handler extends ExceptionHandler
{
    /**
     * The errors.
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


    public function __construct()
    {
      $this->errors = [];

      $this->description = null;
    }

    public function render($request, Exception $e)
    {
        if ($e instanceof JsonResponseException)
        {
            return $e->getJsonResponse();
        }

        // create json from exception
        $content = $e->getMessage();
        if ($e instanceof HttpResponseException)
        {
            $code = 400;
        }
        elseif ($e instanceof ModelNotFoundException)
        {
            $code = 404;
        }
        elseif ($e instanceof AuthorizationException)
        {
            $code = 403;
        }
        elseif ($e instanceof ValidationException && $e->getResponse())
        {
            $code = 422;
        }
        // add more cases here
        else
        {
            switch (FlattenException::create($e)->getStatusCode())
            {
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
