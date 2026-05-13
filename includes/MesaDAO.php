<?php
require_once __DIR__ . '/application.php';
require_once __DIR__ . '/../entities/Mesa.php';

class MesaDAO
{
    public static function getMesasDisponibles(): array
    {
        global $conn;

        $stmt = $conn->prepare("SELECT id, numero_mesa, capacidad_ocupantes, ocupada FROM mesas WHERE ocupada = 0 ORDER BY numero_mesa ASC");
        $stmt->execute();
        $result = $stmt->get_result();

        $mesas = [];
        while ($fila = $result->fetch_assoc()) {
            $mesas[] = new Mesa((int) $fila['id'], (int) $fila['numero_mesa'], (int) $fila['capacidad_ocupantes'], (bool) $fila['ocupada']);
        }

        $result->free();
        $stmt->close();

        return $mesas;
    }

    public static function asignarMesaAPedido(int $pedido_id, int $mesa_id): bool
    {
        global $conn;

        $stmt = $conn->prepare("INSERT INTO pedidos_mesa (id_mesa, id_pedido) VALUES (?, ?)");
        $stmt->bind_param("ii", $mesa_id, $pedido_id);
        $ok = $stmt->execute();
        $stmt->close();

        if (!$ok) {
            return false;
        }

        $stmt2 = $conn->prepare("UPDATE mesas SET ocupada = 1 WHERE id = ?");
        $stmt2->bind_param("i", $mesa_id);
        $ok2 = $stmt2->execute();
        $stmt2->close();

        return $ok2;
    }
}
