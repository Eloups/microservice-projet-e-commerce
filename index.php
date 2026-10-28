<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use MicroserviceProjetECommerce\Adapters\RoutingAdapter;
use MicroserviceProjetECommerce\Adapters\SqliteDatabaseAdapter;
use MicroserviceProjetECommerce\Adapters\SqliteEventAdapter;
use MicroserviceProjetECommerce\Domain\Errors\CartAlreadyValidatedException;
use MicroserviceProjetECommerce\Router\Router;
use MicroserviceProjetECommerce\Utils\ErrorResponse;
use MicroserviceProjetECommerce\Utils\Logger;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

// Correlation : chaque requete recoit un identifiant unique (ou reprend
// celui du client via X-Request-Id), visible dans les logs et les reponses
$requestId = $_SERVER['HTTP_X_REQUEST_ID'] ?? ('req_' . bin2hex(random_bytes(8)));
Logger::setRequestId($requestId);
header('X-Request-Id: ' . $requestId);
$requestStart = microtime(true);

$router = new Router();
$serializer = new Serializer([new ObjectNormalizer()]);

$router->post('cart_add_article', '/cart/addArticle', function (array $params) use ($serializer): array {
    $cartId = (int) ($params['cartId'] ?? 0);
    $articleId = (int) ($params['articleId'] ?? 0);
    if ($cartId <= 0 || $articleId <= 0) {
        ErrorResponse::send(400, 'Parametres requis : cartId (entier) et articleId (entier)');
    }

    $routingAdapter = new RoutingAdapter();
    $cart = $routingAdapter->runAddArticleToCastCase(
        $cartId,
        $articleId,
        new SqliteDatabaseAdapter(),
        new SqliteEventAdapter()
    );

    return $serializer->normalize($cart);
});

$router->post('cart_validate', '/cart/validate', function (array $params) use ($serializer): array {
    $cartId = (int) ($params['cartId'] ?? 0);
    if ($cartId <= 0) {
        ErrorResponse::send(400, 'Parametre requis : cartId (entier)');
    }

    $routingAdapter = new RoutingAdapter();
    $cart = $routingAdapter->runValidateCartCase(
        $cartId,
        new SqliteDatabaseAdapter(),
        new SqliteEventAdapter()
    );

    return $serializer->normalize($cart);
});

// Consultation de la file par un consommateur externe :
// n'accede qu'a la base evenements (isolee de la base metier)
$router->get('events_pending', '/events/pending', function (array $params): array {
    $limit = max(1, min(100, (int) ($params['limit'] ?? 10)));

    $routingAdapter = new RoutingAdapter();
    return $routingAdapter->runListPendingEventsCase($limit, new SqliteEventAdapter());
});

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

Logger::log('info', 'requete recue', ['method' => $method, 'path' => $path]);

$match = $router->match($path, $method);

if ($match === null) {
    ErrorResponse::send(404, 'Route introuvable');
}

Logger::log('info', 'route matchee', ['route' => $match['_route']]);

$controller = $match['_controller'];

// Parametres de la requete : corps JSON, formulaire, ou query string
$body = json_decode(file_get_contents('php://input') ?: '[]', true) ?? [];
$params = array_merge($_GET, $_POST, $body);

try {
    $response = $controller($params);
} catch (CartAlreadyValidatedException $e) {
    // Conflit metier : le panier est deja valide
    ErrorResponse::send(409, $e->getMessage());
} catch (PDOException $e) {
    // Panne de stockage simulee (verrou exclusif) ou base inouvrable :
    // le service se degrade proprement sans crash
    $message = strtolower($e->getMessage());
    if (str_contains($message, 'locked') || str_contains($message, 'unable to open')) {
        ErrorResponse::send(503, 'Service momentanement indisponible (stockage indisponible)');
    }
    ErrorResponse::send(500, $e->getMessage());
} catch (RuntimeException $e) {
    // Erreur metier attendue (panier ou article inconnu)
    ErrorResponse::send(404, $e->getMessage());
} catch (Throwable $e) {
    // Fallback global : toute erreur inattendue
    ErrorResponse::send(500, $e->getMessage());
}

Logger::log('info', 'reponse envoyee', [
    'status' => 200,
    'durationMs' => round((microtime(true) - $requestStart) * 1000, 1),
]);

http_response_code(200);
header('Content-Type: application/json');
echo json_encode($response);
