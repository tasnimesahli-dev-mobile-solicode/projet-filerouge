<?php
// S3/backend/services/ResponseService.php
// Tier 2 — Formatage unifié de toutes les réponses JSON HTTP

require_once __DIR__ . '/BaseService.php';

class ResponseService extends BaseService
{
    /** Réponse JSON de succès */
    public function success(array $data, int $status = 200): void
    {
        $this->jsonResponse(array_merge(['success' => true], $data), $status);
    }

    /** Réponse JSON d'erreur */
    public function error(string $message, int $status = 500, array $extra = []): void
    {
        $this->jsonResponse(
            array_merge(['success' => false, 'message' => $message], $extra),
            $status
        );
    }

    /** Envoi brut JSON avec code HTTP */
    public function jsonResponse(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}

