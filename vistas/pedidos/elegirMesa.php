<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/util.php';
require_once __DIR__ . '/../../includes/Formulario/FormularioElegirMesa.php';
require_once __DIR__ . '/../../includes/PedidoService.php';

require_login();

if (PedidoService::getTipoCarrito() !== 'local') {
    redirect('elegirTipo.php');
}

if (PedidoService::getMesaCarrito() !== null) {
    redirect('catalogo.php');
}

$form = new \es\ucm\fdi\aw\Formulario\FormularioElegirMesa();
$htmlForm = $form->gestiona();

$tituloPagina = 'Elegir mesa | Bistro FDI';
ob_start();
?>
<main>
<div class="panel">
<h2>Selecciona una mesa disponible</h2>
<?= $htmlForm ?>
</div>
</main>
<?php
$contenidoPrincipal = ob_get_clean();
require __DIR__ . '/../../includes/plantilla.php';
