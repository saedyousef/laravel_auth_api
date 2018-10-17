<?php
namespace App\Constants;

/**
 * Class HttpStatus
 * contains http response status codes
 */
final class Roles {

    /**
     * HttpStatus constructor.
     * prevent instantiation.
     */
    private function __construct() {
    }

    const SYSTEM_ADMIN = 1;
    const CLIENT       = 2;
    const CUSTOMER     = 3;

}