<?php
require_once __DIR__ . '/../entities/Oferta.php';
require_once __DIR__ . '/../includes/application.php';

class OfertaDAO
{
    public static function getMenuDelDiaActivo(): ?Oferta
    {
        global $conn;

        $stmt = $conn->prepare(
            "SELECT id, nombre, descripcion, fecha_inicio, fecha_fin, descuento, es_menu_dia
             FROM ofertas
             WHERE NOW() BETWEEN fecha_inicio AND fecha_fin
               AND es_menu_dia = 1
             ORDER BY fecha_inicio DESC
             LIMIT 1"
        );

                $stmt->execute();
        $result = $stmt->get_result();
        $fila = $result->fetch_assoc();

        $result->free();
        $stmt->close();

        if (!$fila) {
            return null;
        }

        return new Oferta(
            $fila['id'],
            $fila['nombre'] ?? '',
            $fila['descripcion'] ?? '',
            $fila['fecha_inicio'],
            $fila['fecha_fin'],
            $fila['descuento'],
            (bool)($fila['es_menu_dia'] ?? 0)
        );
    }

    // Obtener todas las ofertas
    public static function getAll()
    {
        global $conn;

        $stmt = $conn->prepare("
            SELECT id, nombre, descripcion, fecha_inicio, fecha_fin, descuento, es_menu_dia 
            FROM ofertas 
            ORDER BY fecha_inicio DESC
        ");
        $stmt->execute();
        $result = $stmt->get_result();

        $ofertas = [];
        while ($fila = $result->fetch_assoc()) {
            $ofertas[] = new Oferta(
                $fila['id'],
                $fila['nombre'] ?? '',
                $fila['descripcion'] ?? '',
                $fila['fecha_inicio'],
                $fila['fecha_fin'],
                $fila['descuento'],
                (bool)($fila['es_menu_dia'] ?? 0)
            );
        }

        $result->free();
        $stmt->close();

        return $ofertas;
    }

    public static function getAllActivas()
    {
        global $conn;

        $stmt = $conn->prepare("
            SELECT id, nombre, descripcion, fecha_inicio, fecha_fin, descuento, es_menu_dia 
            FROM ofertas 
            WHERE NOW() BETWEEN fecha_inicio AND fecha_fin
        ");
        $stmt->execute();
        $result = $stmt->get_result();

        $ofertas = [];
        while ($fila = $result->fetch_assoc()) {
            $ofertas[] = new Oferta(
                $fila['id'],
                $fila['nombre'] ?? '',
                $fila['descripcion'] ?? '',
                $fila['fecha_inicio'],
                $fila['fecha_fin'],
                $fila['descuento'],
                (bool)($fila['es_menu_dia'] ?? 0)
            );
        }

        $result->free();
        $stmt->close();

        return $ofertas;
    }

    // Obtener oferta por ID
    public static function getById($id)
    {
        global $conn;

        $stmt = $conn->prepare("
            SELECT id, nombre, descripcion, fecha_inicio, fecha_fin, descuento, es_menu_dia 
            FROM ofertas 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $fila = $result->fetch_assoc();

        $result->free();
        $stmt->close();

        if (!$fila) {
            return null;
        }

        return new Oferta(
            $fila['id'],
            $fila['nombre'] ?? '',
            $fila['descripcion'] ?? '',
            $fila['fecha_inicio'],
            $fila['fecha_fin'],
            $fila['descuento'],
            (bool)($fila['es_menu_dia'] ?? 0)
        );
    }

    // Crear nueva oferta
    public static function crearOferta($nombre, $descripcion, $fecha_inicio, $fecha_fin, $descuento, $es_menu_dia = 0)
    {
        global $conn;

        $stmt = $conn->prepare("
            INSERT INTO ofertas (nombre, descripcion, fecha_inicio, fecha_fin, descuento, es_menu_dia)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("ssssdi", $nombre, $descripcion, $fecha_inicio, $fecha_fin, $descuento, $es_menu_dia);
        $stmt->execute();

        // 1. Obtenemos el ID de la oferta recién creada
        $id_generado = $stmt->insert_id;

        $stmt->close();

        // 2. Lo devolvemos
        return $id_generado;
    }

    // Editar oferta existente
    public static function editarOferta($id, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $descuento, $es_menu_dia = 0)
    {
        global $conn;

        $stmt = $conn->prepare("
            UPDATE ofertas 
            SET nombre = ?, descripcion = ?, fecha_inicio = ?, fecha_fin = ?, descuento = ?, es_menu_dia = ?
            WHERE id = ?
        ");

        $stmt->bind_param("ssssdii", $nombre, $descripcion, $fecha_inicio, $fecha_fin, $descuento, $es_menu_dia, $id);
        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }

    // Borrar oferta
    public static function borrarOferta($id)
    {
        global $conn;

        $stmt = $conn->prepare("DELETE FROM ofertas WHERE id = ?");
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }

    public static function ofertaEnUso($oferta_id)
    {
        global $conn;

        $stmt = $conn->prepare("
            SELECT pedido_id
            FROM ofertas_en_pedido
            WHERE oferta_id = ?
        ");

        $stmt->bind_param("i", $oferta_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $resultado = [];
        while ($fila = $result->fetch_assoc()) {
            $resultado[] = $fila;
        }

        $result->free();
        $stmt->close();

        return $resultado;
    }

}
