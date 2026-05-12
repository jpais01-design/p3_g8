<?php
require_once __DIR__ . '/application.php';

class ResenaDAO
{
    public static function pedidoYaResenadoPorUsuario(int $usuario_id, int $pedido_id): bool
    {
        global $conn;
        $stmt = $conn->prepare("SELECT id FROM resenas WHERE usuario_id = ? AND pedido_id = ? LIMIT 1");
        $stmt->bind_param("ii", $usuario_id, $pedido_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $ya = (bool) $result->fetch_assoc();
        $result->free();
        $stmt->close();
        return $ya;
    }

    public static function getProductosResenablesPedido(int $pedido_id): array
    {
        global $conn;
        $stmt = $conn->prepare(
            "SELECT pep.producto_id, p.nombre, p.imagen
             FROM productos_en_pedido pep
             JOIN productos p ON p.id = pep.producto_id
             WHERE pep.pedido_id = ?
             ORDER BY p.nombre ASC"
        );
        $stmt->bind_param("i", $pedido_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $result->free();
        $stmt->close();
        return $rows;
    }

    public static function insertarResena(int $usuario_id, int $producto_id, int $pedido_id, string $texto, int $valoracion): bool
    {
        global $conn;
        $stmt = $conn->prepare(
            "INSERT INTO resenas (usuario_id, producto_id, pedido_id, texto, valoracion, fecha_creacion)
             VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $stmt->bind_param("iiisi", $usuario_id, $producto_id, $pedido_id, $texto, $valoracion);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public static function getResenasByProducto(int $producto_id): array
    {
        global $conn;
        $stmt = $conn->prepare(
            "SELECT r.texto, r.valoracion, r.fecha_creacion, u.username
             FROM resenas r
             JOIN usuarios u ON u.id = r.usuario_id
             WHERE r.producto_id = ?
             ORDER BY r.fecha_creacion DESC"
        );
        $stmt->bind_param("i", $producto_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $result->free();
        $stmt->close();
        return $rows;
    }
}
