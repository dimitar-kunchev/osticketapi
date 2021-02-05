<?php 

namespace OSTicketAPI;

class Ticket {
    public $email = ''; // required
    
    public $name = ''; // required
    
    public $subject = ''; // required
    
    public $message = ''; // required;
    
    public $messageContentType = 'text/plain';
    
    public $alert = true;
    
    public $autorespond = true;
    
    public $submitterIP = null;
    
    public $priorityId = null;
    
    public $source = 'API';
    
    public $topicId = null;
    
    public $logger = null; // Optional: PSR-3 logger where the ticket JSON will be printed as a debug output
    
    public function __call($name, $arguments) {
        $action = substr($name, 0, 3);
        switch ($action) {
            case 'get':
                $property = lcfirst(substr($name, 3));
                if (property_exists($this, $property)) {
                    return $this->{$property};
                } else{
                    throw new \RuntimeException('No such property '.$property.' of class '.get_class($this));
                }
                break;
            case 'set':
                $property = lcfirst(substr($name, 3));
                if (property_exists($this, $property)) {
                    $this->{$property} = $arguments[0];
                    return $this;
                } else {
                    throw new \RuntimeException('No such property '.$property.' of class '.get_class($this));
                }
                
                break;
            default :
                throw new \BadMethodCallException('No such method '.$name.' of class '.get_class($this));
        }
    }

    //public $attachments = [];
    
    /**
     * Create a ticket
     * 
     * Returns the ticket id or null
     * Throws \OSTicketAPI\TicketException on problem
     */
    public function create (string $osTicketURI, string $osTicketAPIKey): int {
        $ticketData = [];
        
        if (!$this->email) {
            throw new TicketException('Email is required');
        }
        $ticketData['email'] = $this->email;
        
        if (!$this->name) {
            throw new TicketException('Name is required');
        }
        $ticketData['name'] = $this->name;
        
        if (!$this->subject) {
            throw new TicketException('Subject is required');
        }
        $ticketData['subject'] = $this->subject;
        
        if (!$this->message) {
            throw new TicketException('Message is required');
        }
        if ($this->messageContentType && $this->messageContentType != 'text/plain') {
            $ticketData['message'] = 'data:'.$this->messageContentType.','.$this->message;
        } else {
            $ticketData['message'] = $this->message;
        }
        
        if ($this->alert == false) {
            $ticketData['alert'] = false;
        }
        if ($this->autorespond == false) {
            $ticketData['autorespond'] = false;
        }
        if ($this->submitterIP) {
            $ticketData['ip'] = $this->submitterIP;
        }
        if ($this->priorityId) {
            $ticketData['priority'] = $this->priorityId;
        }
        if ($this->source && $this->source != 'API') {
            $ticketData['source'] = $this->source;
        }
        if ($this->topicId) {
            $ticketData['topicId'] = $this->topicId;
        }
        
        // attachments
        
        $serialized = json_encode($ticketData);
        
        if ($this->logger != null) {
            $this->logger->debug('Creating ticket at '.$osTicketURI.' with JSON '.$serialized);
        }
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $osTicketURI);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'X-API-Key: '.$osTicketAPIKey ]);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $serialized);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        
        $resultBody = curl_exec($curl);
        
        if (curl_errno($curl)) {
            throw new TicketException("CURL error ", curl_errno($curl));
        }
        
        $http_response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        
        if ($http_response_code != 201) {
            throw new TicketException($resultBody, $http_response_code);
        }
            
        return (int)trim($resultBody);
    }
}