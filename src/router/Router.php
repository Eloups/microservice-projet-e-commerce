<?php

namespace MicroserviceProjetECommerce\Router;

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Router applicatif basé sur symfony/routing.
 * Déclare les routes et fait correspondre une requête entrante à un contrôleur.
 */
class Router
{
    private RouteCollection $routes;
    private RequestContext $context;
    private UrlMatcher $matcher;

    public function __construct()
    {
        $this->routes = new RouteCollection();
        $this->context = new RequestContext();
        $this->matcher = new UrlMatcher($this->routes, $this->context);
    }

    /**
     * Enregistre une route nommée.
     *
     * @param string $name     Nom unique de la route
     * @param string $path     Chemin, ex. "/cart/{cartId}/articles/{articleId}"
     * @param string $method   Méthode HTTP attendue (GET, POST, ...)
     * @param mixed  $controller Valeur stockée dans "_controller" (callable, [classe, méthode], ...)
     */
    public function add(string $name, string $path, string $method, mixed $controller): void
    {
        $this->routes->add($name, new Route($path, [
            '_controller' => $controller,
        ], requirements: [], options: [], host: '', schemes: [], methods: [$method]));
    }

    /**
     * Raccourcis pour les méthodes HTTP courantes.
     */
    public function get(string $name, string $path, mixed $controller): void
    {
        $this->add($name, $path, 'GET', $controller);
    }

    public function post(string $name, string $path, mixed $controller): void
    {
        $this->add($name, $path, 'POST', $controller);
    }

    public function delete(string $name, string $path, mixed $controller): void
    {
        $this->add($name, $path, 'DELETE', $controller);
    }

    /**
     * Cherche la route correspondant au chemin et à la méthode HTTP.
     *
     * @param string $path   Chemin de la requête, ex. "/cart/1/articles/42"
     * @param string $method  Méthode HTTP de la requête
     * @return array|null Tableau de paramètres de la route (inclut "_controller" et "_route"),
     *                    null si aucune route ne correspond
     */
    public function match(string $path, string $method = 'GET'): ?array
    {
        $this->context->setMethod($method);

        try {
            return $this->matcher->match($path);
        } catch (ResourceNotFoundException | MethodNotAllowedException) {
            return null;
        }
    }
}
