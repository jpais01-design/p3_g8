<?php
require_once __DIR__ . '/application.php';
require_once __DIR__ . '/../entities/Incidencia.php';

class IncidenciaDAO
{
    public static function insertarIncidencia(int $pedido_id, string $incidencia): bool
    {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO incidencias (pedido_id, incidencia) VALUES (?, ?)");
        $stmt->bind_param("is", $pedido_id, $incidencia);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public static function getIncidenciasByPedido(int $pedido_id): array
    {
        global $conn;
        $stmt = $conn->prepare("SELECT id, pedido_id, incidencia FROM incidencias WHERE pedido_id = ? ORDER BY id DESC");
        $stmt->bind_param("i", $pedido_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = new Incidencia((int)$row['id'], (int)$row['pedido_id'], (string)$row['incidencia']);
        }
        $result->free();
        $stmt->close();
        return $rows;
    }

    public static function getAllConPedido(): array
    {
        global $conn;
        $sql = "SELECT i.id, i.pedido_id, i.incidencia, p.numero_pedido, p.fecha_hora, u.username
                FROM incidencias i
                JOIN pedidos p ON p.id = i.pedido_id
                JOIN usuarios u ON u.id = p.usuario_id
                ORDER BY i.id DESC";
        $result = $conn->query($sql);
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = new Incidencia((int)$row['id'], (int)$row['pedido_id'], (string)$row['incidencia'], (string)$row['numero_pedido'], (string)$row['fecha_hora'], (string)$row['username']);
        }
        $result->free();
        return $rows;
    }
}
