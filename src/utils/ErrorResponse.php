<?php

declare(strict_types=1);

namespace MicroserviceProjetECommerce\Utils;

/**
 * Fallback d'erreur : repond en JSON { "code": ..., "message": ... }
 * avec le statut HTTP correspondant, journalise l'erreur, puis
 * termine le script.
 */
class ErrorResponse
{
    public static function send(int $code, string $message): never
    {
        Logger::log('error', $message, ['code' => $code, 'status' => $code]);

        http_response_code($code);
        header('Content-Type: application/json');
        if (Logger::getRequestId() !== null) {
            header('X-Request-Id: ' . Logger::getRequestId());
        }
        echo json_encode(['code' => $code, 'message' => $message]);
        exit;
    }
}
