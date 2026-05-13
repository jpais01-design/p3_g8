<?php
class Resena
{
    private int $id;
    private int $usuario_id;
    private int $producto_id;
    private int $pedido_id;
    private string $texto;
    private int $valoracion;
    private string $fecha_creacion;
    private string $username;

    public function __construct(int $id, int $usuario_id, int $producto_id, int $pedido_id, string $texto, int $valoracion, string $fecha_creacion, string $username = '')
    {
        $this->id = $id;
        $this->usuario_id = $usuario_id;
        $this->producto_id = $producto_id;
        $this->pedido_id = $pedido_id;
        $this->texto = $texto;
        $this->valoracion = $valoracion;
        $this->fecha_creacion = $fecha_creacion;
        $this->username = $username;
    }

    public function getTexto(): string { return $this->texto; }
    public function getValoracion(): int { return $this->valoracion; }
    public function getFechaCreacion(): string { return $this->fecha_creacion; }
    public function getUsername(): string { return $this->username; }
}
