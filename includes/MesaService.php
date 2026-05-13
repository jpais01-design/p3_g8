<?php
require_once __DIR__ . '/MesaDAO.php';

class MesaService
{
    public static function getMesasDisponibles(): array
    {
        return MesaDAO::getMesasDisponibles();
    }

    public static function asignarMesaAPedido(int $pedido_id, int $mesa_id): bool
    {
        return MesaDAO::asignarMesaAPedido($pedido_id, $mesa_id);
    }
}
