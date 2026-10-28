<?php

declare(strict_types=1);

namespace MicroserviceProjetECommerce\Utils;

/**
 * Logger minimal : lignes JSON horodatees dans data/logs/app.log,
 * correlees par un identifiant de requete (requestId).
 */
class Logger
{
    private static ?string $requestId = null;

    /**
     * Identifiant de correlation de la requete courante.
     */
    public static function setRequestId(?string $requestId): void
    {
        self::$requestId = $requestId;
    }

    public static function getRequestId(): ?string
    {
        return self::$requestId;
    }

    /**
     * Ecrit une ligne de log JSON : timestamp, niveau, message,
     * requestId et contexte eventuel.
     *
     * @param array<string, mixed> $context
     */
    public static function log(string $level, string $message, array $context = []): void
    {
        $dir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'logs';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $line = json_encode(array_merge([
            'timestamp' => date('c'),
            'level' => $level,
            'requestId' => self::$requestId,
            'message' => $message,
        ], $context));

        if ($line !== false) {
            file_put_contents($dir . DIRECTORY_SEPARATOR . 'app.log', $line . PHP_EOL, FILE_APPEND);
        }
    }
}
