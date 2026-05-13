<?php
require_once __DIR__ . '/IncidenciaDAO.php';

class IncidenciaService
{
    private static function asegurarSesion(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['incidencias_borrador']) || !is_array($_SESSION['incidencias_borrador'])) {
            $_SESSION['incidencias_borrador'] = [];
        }
    }

    public static function guardarBorrador(int $pedido_id, string $texto): void
    {
        self::asegurarSesion();
        $_SESSION['incidencias_borrador'][$pedido_id] = trim($texto);
    }

    public static function getBorrador(int $pedido_id): string
    {
        self::asegurarSesion();
        return (string) ($_SESSION['incidencias_borrador'][$pedido_id] ?? '');
    }

    public static function cancelar(int $pedido_id): void
    {
        self::asegurarSesion();
        unset($_SESSION['incidencias_borrador'][$pedido_id]);
    }

    public static function finalizar(int $pedido_id): void
    {
        self::asegurarSesion();
        $texto = trim((string) ($_SESSION['incidencias_borrador'][$pedido_id] ?? ''));
        if ($texto !== '') {
            IncidenciaDAO::insertarIncidencia($pedido_id, $texto);
        }
        unset($_SESSION['incidencias_borrador'][$pedido_id]);
    }
}
