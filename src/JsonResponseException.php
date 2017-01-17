<?php

namespace ApiExceptions;

use RuntimeException;

class JsonResponseException extends RuntimeException
{
    protected $body;

    public function __construct(int $code, string $message, string $description=NULL, array $errors=[])
    {
        $this->body = [
            "code" => $code,
            "message" => $message,
            "description" => $description,
            "errors" => $errors
        ];
    }

    public function getJsonResponse()
    {
        return response()->json($this->body, $this->body["code"]);
    }
}
