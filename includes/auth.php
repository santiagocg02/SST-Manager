<?php

declare(strict_types=1);

function startSessionIfNeeded(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function redirectToLogin(): void
{
    header('Location: ' . APP_LOGIN_PATH);
    exit;
}

function requireAuthenticatedSession(): void
{
    startSessionIfNeeded();

    if (!isset($_SESSION['usuario'])) {
        redirectToLogin();
    }
}
