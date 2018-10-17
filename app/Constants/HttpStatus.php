<?php
namespace App\Constants;

/**
 * Class HttpStatus
 * contains http response status codes
 */
final class HttpStatus {

    /**
     * HttpStatus constructor.
     * prevent instantiation.
     */
    private function __construct() {
    }

    const OK                    = 200;
    const CREATED               = 201;
    const NO_CONTENT            = 204;
    const BAD_REQUEST           = 400;
    const UNAUTHORIZED          = 401;
    const FORBIDDEN             = 403;
    const NOT_FOUND             = 404;
    const METHOD_NOT_ALLOWED    = 405;
    const INTERNAL_SERVER_ERROR = 500;


    public static $codesMessages = [
        200 => 'Ok',
        201 => 'Created',
        204 => 'No Content',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        500 => 'Internal Server Error'
    ];
}