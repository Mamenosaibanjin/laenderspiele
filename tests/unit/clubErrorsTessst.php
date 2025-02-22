<?php
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

$durchlauf = 1;

$client = HttpClient::create();
$failedClubs = [];
$checkedResponses = []; // Hier speichern wir schon verarbeitete Response-Objekte
$maxClubID = 2500; // Beispiel für 33k IDs

$requests = [];

// Sende asynchrone Anfragen
for ($id = 1; $id <= $maxClubID; $id++) {
    $requests[$id] = $client->request('GET', "http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/club/{$id}");
}

try {
    // Warten auf alle Anfragen und nach und nach auswerten
    foreach ($client->stream($requests) as $response => $chunk) {
        if (!isset($checkedResponses[spl_object_id($response)])) {
            // `$response` ist ein `ResponseInterface`, wir müssen die dazugehörige Club-ID finden
            $id = array_search($response, $requests, true);
            
            if ($id !== false) {
                try {
                    $statusCode = $response->getStatusCode();
                    $content = $response->getContent();
                    
                    // Speichere nur Fehler, wenn sie nicht 404 sind oder einen "PHP Deprecated Warning" enthalten
                    if ($statusCode !== 200 && $statusCode !== 404 || strpos($content, 'PHP Deprecated Warning') !== false) {
                        $failedClubs[] = $id;
                    }
                } catch (TransportExceptionInterface $e) {
                    // Speichere nur, wenn der Fehler nicht ein 404-Fehler ist
                    if (!str_contains($e->getMessage(), '404')) {
                        $failedClubs[] = $id;
                    }
                }
                
                // Markiere die Response als bearbeitet
                $checkedResponses[spl_object_id($response)] = true;
            }
        }
    }
} catch (\Exception $e) {
    echo "Fehler bei der Anfrage: " . $e->getMessage();
}

if (!empty($failedClubs)) {
    echo "Die folgenden Club-IDs verursachten Fehler: " . implode(', ', array_unique($failedClubs)) . "\n";
} else {
    echo "Alle Clubs sind fehlerfrei.\n";
}
