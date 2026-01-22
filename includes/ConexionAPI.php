<?php
class ConexionAPI {
    // La URL base que aparece en tu Swagger
    private $urlBase = "http://localhost/sstmanager-backend/public/"; 

    /**
     * Método genérico para realizar peticiones a la API
     */
    public function solicitar($endpoint, $metodo = "GET", $datos = null, $token = null) {
        $urlFull = $this->urlBase . $endpoint;
        $ch = curl_init($urlFull);

        $headers = [
            "Content-Type: application/json",
            "Accept: application/json"
        ];

        // Si ya tienes un Token JWT (como indica tu Swagger), lo adjuntamos
        if ($token) {
            $headers[] = "Authorization: Bearer " . $token;
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($datos && ($metodo == "POST" || $metodo == "PUT" || $metodo == "PATCH")) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
        }

        $respuesta = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return ["status" => 500, "error" => $error_msg];
        }

        curl_close($ch);
        return [
            "status" => $httpCode,
            "data" => json_decode($respuesta, true)
        ];
    }
}