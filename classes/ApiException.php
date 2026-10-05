<?php
declare(strict_types=1);
namespace Apgenic\Classes;

/**
 * Error of an API request: the code is the HTTP status of the answer
 */
class ApiException extends \Exception
{
    /** Added to the "error" object of the answer, ex: array('fields' => array('name' => 'Required')) */
    public array $details = array();

    public function __construct(int $status, string $message, array $details = array()){
        parent::__construct($message, $status);
        $this->details = $details;
    }
}
