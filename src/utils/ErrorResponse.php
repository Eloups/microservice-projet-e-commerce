<?php

declare(strict_types=1);

namespace MicroserviceProjetECommerce\Utils;

/**
 * Fallback d'erreur : repond en JSON { "code": ..., "message": ... }
 * avec le statut HTTP correspondant, puis termine le script.
 */
class ErrorResponse
{
    public static function send(int $code, string $message): never
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['code' => $code, 'message' => $message]);
        exit;
    }
}
