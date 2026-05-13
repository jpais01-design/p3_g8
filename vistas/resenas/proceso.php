<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/application.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/PedidoDAO.php';
require_once __DIR__ . '/../../includes/ResenaService.php';
require_once __DIR__ . '/../../includes/ResenaDAO.php';
require_once __DIR__ . '/../../includes/IncidenciaService.php';
require_once __DIR__ . '/../../includes/IncidenciaDAO.php';

$user = require_login();
$usuario_id = (int) $user->getId();
$pedido_id = isset($_GET['pedido_id']) ? (int) $_GET['pedido_id'] : (int) ($_POST['pedido_id'] ?? 0);

$pedido = $pedido_id > 0 ? PedidoDAO::getPedidoById($pedido_id) : null;
if (!$pedido || (int) $pedido->getUsuario_id() !== $usuario_id || $pedido->getEstado() !== 'entregado') {
    flash_set('error', 'Pedido no válido para reseñar.');
    redirect(RUTA_APP . '/vistas/usuarios/perfil.php?tab=historico');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    if ($accion === 'guardar_producto') {
        $producto_id = (int) ($_POST['producto_id'] ?? 0);
        $texto = trim((string) ($_POST['texto'] ?? ''));
        $valoracion = (int) ($_POST['valoracion'] ?? 0);
        [$ok, $msg] = ResenaService::agregarResenaBorrador($usuario_id, $pedido_id, $producto_id, $texto, $valoracion);
        flash_set($ok ? 'success' : 'error', $msg);
    } elseif ($accion === 'guardar_incidencia') {
        $incidencia = trim((string) ($_POST['incidencia'] ?? ''));
        IncidenciaService::guardarBorrador($pedido_id, $incidencia);
        flash_set('success', 'Incidencia guardada temporalmente.');
    } elseif ($accion === 'cancelar') {
        ResenaService::cancelarProcesoPedido($pedido_id);
        IncidenciaService::cancelar($pedido_id);
        flash_set('info', 'Proceso de valoración cancelado.');
        redirect(RUTA_APP . '/vistas/usuarios/perfil.php?tab=historico');
    } elseif ($accion === 'finalizar') {
        [$ok, $msg] = ResenaService::finalizarProcesoPedido($usuario_id, $pedido_id);
        if ($ok) {
            IncidenciaService::finalizar($pedido_id);
        }
        flash_set($ok ? 'success' : 'error', $msg);
        if ($ok) {
            redirect(RUTA_APP . '/vistas/usuarios/perfil.php?tab=historico');
        }
    }
}

$productos = ResenaDAO::getProductosResenablesPedido($pedido_id);
$borrador = ResenaService::getBorradorPedido($pedido_id);

$tituloPagina = 'Reseñar pedido | Bistro FDI';
$rutaCSS = RUTA_APP . '/CSS/estilo.css';
ob_start();
?>
<main>
    <div class="panel">
        <h2>Reseñar pedido #<?= escaparHtml((string) $pedido->getNumero_pedido()) ?></h2>

        <?php foreach (flash_get_all() as $f): ?>
            <div class="mensaje-<?= escaparHtml($f['type']) ?>"><?= escaparHtml($f['message']) ?></div>
        <?php endforeach; ?>

        <p>Selecciona los productos que quieras reseñar. Se guardan en borrador hasta finalizar.</p>

        <?php foreach ($productos as $prod):
            $pid = (int) $prod['producto_id'];
            $data = $borrador[$pid] ?? ['texto' => '', 'valoracion' => 5];
        ?>
            <div class="panel mb-12">
                <h3><?= escaparHtml($prod['nombre']) ?></h3>
                <form method="POST">
                    <input type="hidden" name="pedido_id" value="<?= $pedido_id ?>">
                    <input type="hidden" name="producto_id" value="<?= $pid ?>">
                    <input type="hidden" name="accion" value="guardar_producto">
                    <label>Valoración (1-5)</label>
                    <input type="number" name="valoracion" min="1" max="5" required value="<?= (int) $data['valoracion'] ?>">
                    <label>Reseña</label>
                    <textarea name="texto" rows="3" maxlength="1000"><?= escaparHtml((string) $data['texto']) ?></textarea>
                    <button type="submit" class="btn small">Confirmar reseña del producto</button>
                </form>
            </div>
        <?php endforeach; ?>

        <?php $incidenciaBorrador = IncidenciaService::getBorrador($pedido_id); ?>
        <div class="panel mb-12">
            <h3>Incidencia del pedido (opcional)</h3>
            <form method="POST">
                <input type="hidden" name="pedido_id" value="<?= $pedido_id ?>">
                <input type="hidden" name="accion" value="guardar_incidencia">
                <textarea name="incidencia" rows="3" maxlength="1000" placeholder="Describe la incidencia..."><?= escaparHtml($incidenciaBorrador) ?></textarea>
                <button type="submit" class="btn small">Guardar incidencia</button>
            </form>
        </div>

        <div class="actions-inline">
            <form method="POST" onsubmit="return confirm('¿Cancelar todo el proceso de valoración?');">
                <input type="hidden" name="pedido_id" value="<?= $pedido_id ?>">
                <input type="hidden" name="accion" value="cancelar">
                <button type="submit" class="btn danger">Cancelar valoración</button>
            </form>

            <form method="POST">
                <input type="hidden" name="pedido_id" value="<?= $pedido_id ?>">
                <input type="hidden" name="accion" value="finalizar">
                <button type="submit" class="btn primary">Finalizar valoración</button>
            </form>
        </div>
    </div>
</main>
<?php
$contenidoPrincipal = ob_get_clean();
require __DIR__ . '/../../includes/plantilla.php';
