<?php
require_once __DIR__ . '/ResenaDAO.php';

class ResenaService
{
    private static function asegurarSesionResenas(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['resenas_borrador']) || !is_array($_SESSION['resenas_borrador'])) {
            $_SESSION['resenas_borrador'] = [];
        }
    }

    public static function agregarResenaBorrador(int $usuario_id, int $pedido_id, int $producto_id, string $texto, int $valoracion): array
    {
        self::asegurarSesionResenas();
        if ($valoracion < 1 || $valoracion > 5) {
            return [false, 'La valoración debe estar entre 1 y 5.'];
        }
        if (ResenaDAO::pedidoYaResenadoPorUsuario($usuario_id, $pedido_id)) {
            return [false, 'Este pedido ya tiene su valoración finalizada.'];
        }

        if (!isset($_SESSION['resenas_borrador'][$pedido_id])) {
            $_SESSION['resenas_borrador'][$pedido_id] = [];
        }

        $_SESSION['resenas_borrador'][$pedido_id][$producto_id] = [
            'texto' => trim($texto),
            'valoracion' => $valoracion,
        ];

        return [true, 'Reseña guardada temporalmente.'];
    }

    public static function getBorradorPedido(int $pedido_id): array
    {
        self::asegurarSesionResenas();
        return $_SESSION['resenas_borrador'][$pedido_id] ?? [];
    }

    public static function cancelarProcesoPedido(int $pedido_id): void
    {
        self::asegurarSesionResenas();
        unset($_SESSION['resenas_borrador'][$pedido_id]);
    }

    public static function finalizarProcesoPedido(int $usuario_id, int $pedido_id): array
    {
        self::asegurarSesionResenas();
        if (ResenaDAO::pedidoYaResenadoPorUsuario($usuario_id, $pedido_id)) {
            unset($_SESSION['resenas_borrador'][$pedido_id]);
            return [false, 'Este pedido ya fue reseñado previamente.'];
        }

        $borrador = $_SESSION['resenas_borrador'][$pedido_id] ?? [];
        if (empty($borrador)) {
            return [false, 'Debes añadir al menos una reseña antes de finalizar.'];
        }

        foreach ($borrador as $producto_id => $data) {
            ResenaDAO::insertarResena($usuario_id, (int) $producto_id, $pedido_id, (string) ($data['texto'] ?? ''), (int) ($data['valoracion'] ?? 0));
        }

        unset($_SESSION['resenas_borrador'][$pedido_id]);
        return [true, 'Valoración finalizada correctamente.'];
    }
}
