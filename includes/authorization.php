<?php

declare(strict_types=1);

function sessionString(string $key, string $default = ''): string
{
    return isset($_SESSION[$key]) ? trim((string)$_SESSION[$key]) : $default;
}

function sessionInt(string $key, int $default = 0): int
{
    return isset($_SESSION[$key]) ? (int)$_SESSION[$key] : $default;
}

function normalizedRole(?string $role): string
{
    return strtolower(trim((string)$role));
}

function hasAnyRole(string $role, array $allowedRoles): bool
{
    return in_array(normalizedRole($role), array_map('normalizedRole', $allowedRoles), true);
}

function requireRole(array $allowedRoles): void
{
    requireAuthenticatedSession();

    if (!hasAnyRole(sessionString('rol'), $allowedRoles)) {
        redirectToLogin();
    }
}
