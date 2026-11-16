<?php
// S3/backend/services/ResponseService.php - Tier 2 : Réponses JSON formatées

require_once __DIR__ . '/BaseService.php';

class ResponseService extends BaseService
{
    public function success(array $data, int $status = 200): void
    {
        $this->jsonResponse(array_merge(['success' => true], $data), $status);
    }

    public function error(string $message, int $status = 500, array $extra = []): void
    {
        $this->jsonResponse(
            array_merge(['success' => false, 'message' => $message], $extra),
            $status
        );
    }

    public function jsonResponse(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}

