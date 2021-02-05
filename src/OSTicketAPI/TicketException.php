<?php 

namespace OSTicketAPI;

class TicketException extends \Exception {
    public function __construct(string $message, $httpCode) {
        parent::__construct($message, $httpCode, null);
    }
}