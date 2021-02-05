# PHP Implementation of osTicket API

Uses the osTickets JSON API to create tickets.

Install via composer require dimitar-kunchev/osticketapi

Use:

```
use \OSTicketAPI\Ticket;
use \OSTicketAPI\TicketException;

$ticket = new Ticket();
$ticket->setName('John Doe')
    ->setEmail('john@doe.com)
    ->setMessage('This is my message')
    ->setSubject('This is my subject')
    // ->setTopicId(1)    // optional - id of a topic from osTicket
    // ->setSubmitterIP($_SERVER['REMOTE_ADDR'])   // recommended
    // ->setSource('Web')    // recommended, but be careful - there is a handful supported (API, Web, Phone, ...)
    //->setLogger( monolog instance )     // useful if you want to log details about what is sent
    ;

try {
	$apiKey = 'obtained via os ticket admin panel';
	$uri = 'osticket-install/api/tickets.json;
    $ticketId = $ticket->create($uri, $apiKey);
} catch (TicketException $e) {
    // handle some error
}
```

There are a few more options that can be set to the ticket. See the class for yourself. Attachments are not supported.

Read about the Tickets model here: <https://docs.osticket.com/en/latest/Developer%20Documentation/API/Tickets.html>

Use as is, I don't guarantee it will work for you.

License: free for any use