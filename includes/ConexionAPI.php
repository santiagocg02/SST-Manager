<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

class ConexionAPI
{
    private string $urlBase;

    public function __construct(?string $urlBase = null)
    {
        $this->urlBase = rtrim($urlBase ?? APP_DEFAULT_API_BASE_URL, '/') . '/';
    }

    /**
     * Método genérico para realizar peticiones a la API.
     *
     * @return array{status:int,data:mixed,error?:string}
     */
    public function solicitar(string $endpoint, string $metodo = 'GET', ?array $datos = null, ?string $token = null): array
    {
        $metodoNormalizado = strtoupper(trim($metodo));
        $urlFull = $this->urlBase . ltrim($endpoint, '/');

        $ch = curl_init($urlFull);

        if ($ch === false) {
            return ['status' => 500, 'data' => null, 'error' => 'No fue posible iniciar cURL.'];
        }

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        if (!empty($token)) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $metodoNormalizado,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_CONNECTTIMEOUT => 8,
        ]);

        if (!empty($datos) && in_array($metodoNormalizado, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
        }

        $respuesta = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $errorMsg = curl_error($ch);
            curl_close($ch);
            return ['status' => 500, 'data' => null, 'error' => $errorMsg];
        }

        curl_close($ch);

        if ($respuesta === false || $respuesta === '') {
            return ['status' => $httpCode, 'data' => null];
        }

        $decoded = json_decode($respuesta, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => $httpCode,
                'data' => null,
                'error' => 'Respuesta JSON inválida: ' . json_last_error_msg(),
            ];
        }

        return [
            'status' => $httpCode,
            'data' => $decoded,
        ];
    }
}
