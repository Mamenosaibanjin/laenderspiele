<?php
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

$client = HttpClient::create();
$failedClubs = [];
$maxClubID = 30; // Beispiel für 33k IDs

$requests = [];

// Sende asynchrone Anfragen
for ($id = 1; $id <= $maxClubID; $id++) {
    $requests[$id] = $client->request('GET', "http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/club/{$id}");
}

try {
    // Warten auf alle Anfragen und nach und nach auswerten
    foreach ($client->stream($requests) as $response => $chunk) {
        // `$response` ist ein `ResponseInterface`, wir müssen die dazugehörige Club-ID finden
        $id = array_search($response, $requests, true);
        
        if ($id === false) {
            continue;
        }
        
        try {
            if ($response->getStatusCode() !== 200 || strpos($response->getContent(), 'PHP Deprecated Warning') !== false) {
                $failedClubs[] = $id;
            }
        } catch (TransportExceptionInterface $e) {
            $failedClubs[] = $id; // Fehlerhafte Anfragen speichern
        }
    }
} catch (\Exception $e) {
    echo "Fehler bei der Anfrage: " . $e->getMessage();
}

if (!empty($failedClubs)) {
    echo "Die folgenden Club-IDs verursachten Fehler: " . implode(', ', $failedClubs) . "\n";
} else {
    echo "Alle Clubs sind fehlerfrei.\n";
}
    