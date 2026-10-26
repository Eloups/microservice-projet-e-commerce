<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use MicroserviceProjetECommerce\Adapters\RoutingAdapter;
use MicroserviceProjetECommerce\Adapters\SqliteDatabaseAdapter;
use MicroserviceProjetECommerce\Adapters\SqliteEventAdapter;
use MicroserviceProjetECommerce\Router\Router;
use MicroserviceProjetECommerce\Utils\ErrorResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

$router = new Router();

$router->post('cart_add_article', '/cart/addArticle', function (int $cartId, int $articleId): array {
    $routingAdapter = new RoutingAdapter();
    $databaseAdapter = new SqliteDatabaseAdapter();
    $eventAdapter = new SqliteEventAdapter();
    $cart = $routingAdapter->runAddArticleToCastCase($cartId, $articleId, $databaseAdapter, $eventAdapter);

    // Normalisation du panier en tableau brut, l'encodage JSON final
    // est fait en fin de script sur la reponse du controleur
    $serializer = new Serializer([new ObjectNormalizer()]);
    return $serializer->normalize($cart);
});

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

$match = $router->match($path, $method);

if ($match === null) {
    ErrorResponse::send(404, 'Route introuvable');
}

$controller = $match['_controller'];

// Paramètres de la requête : corps JSON, formulaire, ou query string
$body = json_decode(file_get_contents('php://input') ?: '[]', true) ?? [];
$cartId = (int) ($body['cartId'] ?? $_POST['cartId'] ?? $_GET['cartId'] ?? 0);
$articleId = (int) ($body['articleId'] ?? $_POST['articleId'] ?? $_GET['articleId'] ?? 0);

if ($cartId <= 0 || $articleId <= 0) {
    ErrorResponse::send(400, 'Parametres requis : cartId (entier) et articleId (entier)');
}

try {
    $response = $controller($cartId, $articleId);
} catch (PDOException $e) {
    // PDOException etend RuntimeException : a catcher avant elle
    ErrorResponse::send(500, $e->getMessage());
} catch (RuntimeException $e) {
    // Erreur metier attendue (panier ou article inconnu)
    ErrorResponse::send(404, $e->getMessage());
} catch (Throwable $e) {
    // Fallback global : toute erreur inattendue
    ErrorResponse::send(500, $e->getMessage());
}

http_response_code(200);
header('Content-Type: application/json');
echo json_encode($response);
