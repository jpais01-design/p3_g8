<?php
namespace es\ucm\fdi\aw\Formulario;

require_once __DIR__ . '/Formulario.php';
require_once __DIR__ . '/../../includes/PedidoService.php';

class FormularioElegirMesa extends Formulario
{
    

    public function __construct() {
        parent::__construct('formElegirMesa'); 
    }

    protected function generaCamposFormulario(&$datos)
    {
        $mesas = \PedidoService::getMesasDisponibles();
        if (empty($mesas)) {
            return '<p>No hay mesas disponibles ahora mismo.</p>';
        }

        $html = '<form method="POST"><div class="acciones-tipo">';
        foreach ($mesas as $mesa) {
            $html .= '<button type="submit" name="mesa_id" value="' . (int) $mesa['id'] . '">Mesa ' . (int) $mesa['numero_mesa'] . ' (' . (int) $mesa['capacidad_ocupantes'] . ' personas)</button>';
        }
        $html .= '</div></form>';

        return $html;
    }

    protected function procesaFormulario(&$datos)
    {
        $mesa_id = isset($datos['mesa_id']) ? (int) $datos['mesa_id'] : 0;
        if ($mesa_id <= 0) {
            $this->errores[] = 'Mesa inválida';
            return;
        }

        \PedidoService::setMesaCarrito($mesa_id);
        header('Location: ' . RUTA_APP . '/vistas/pedidos/catalogo.php');
        exit;
    }
}
