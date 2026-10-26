<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use MicroserviceProjetECommerce\Adapters\RoutingAdapter;
use MicroserviceProjetECommerce\Adapters\SqliteDatabaseAdapter;
use MicroserviceProjetECommerce\Router\Router;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

$router = new Router();

$router->post('cart_add_article', '/cart/addArticle', function (int $cartId, int $articleId): array {
    $routingAdapter = new RoutingAdapter();
    $databaseAdapter = new SqliteDatabaseAdapter();
    $cart = $routingAdapter->runAddArticleToCastCase($cartId, $articleId, $databaseAdapter);

    // Normalisation du panier en tableau brut, l'encodage JSON final
    // est fait en fin de script sur la reponse du controleur
    $serializer = new Serializer([new ObjectNormalizer()]);
    return $serializer->normalize($cart);
});

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

$match = $router->match($path, $method);

if ($match === null) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Route introuvable']);
    exit;
}

$controller = $match['_controller'];

// Paramètres de la requête : corps JSON, formulaire, ou query string
$body = json_decode(file_get_contents('php://input') ?: '[]', true) ?? [];
$cartId = (int) ($body['cartId'] ?? $_POST['cartId'] ?? $_GET['cartId'] ?? 0);
$articleId = (int) ($body['articleId'] ?? $_POST['articleId'] ?? $_GET['articleId'] ?? 0);

if ($cartId <= 0 || $articleId <= 0) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Parametres requis : cartId (entier) et articleId (entier)']);
    exit;
}

$response = $controller($cartId, $articleId);

http_response_code(200);
header('Content-Type: application/json');
echo json_encode($response);
