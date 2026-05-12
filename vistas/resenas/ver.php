<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/application.php';
require_once __DIR__ . '/../../includes/util.php';
require_once __DIR__ . '/../../includes/ResenaDAO.php';
require_once __DIR__ . '/../../includes/ProductoDAO.php';

$producto_id = isset($_GET['producto_id']) ? (int) $_GET['producto_id'] : 0;
$producto = $producto_id > 0 ? ProductoDAO::getById($producto_id) : null;
if (!$producto) {
    redirect(RUTA_APP . '/vistas/pedidos/catalogo.php');
}
$resenas = ResenaDAO::getResenasByProducto($producto_id);

$tituloPagina = 'Reseñas de producto | Bistro FDI';
$rutaCSS = RUTA_APP . '/CSS/estilo.css';
ob_start();
?>
<main>
    <div class="panel">
        <h2>Reseñas de <?= escaparHtml($producto->getNombre()) ?></h2>
        <?php if (empty($resenas)): ?>
            <p>Este producto todavía no tiene reseñas.</p>
        <?php else: ?>
            <?php foreach ($resenas as $r): ?>
                <article class="panel mb-12">
                    <p><strong>Usuario:</strong> <?= escaparHtml($r['username']) ?></p>
                    <p><strong>Valoración:</strong> <?= (int) $r['valoracion'] ?>/5</p>
                    <p><strong>Reseña:</strong> <?= nl2br(escaparHtml($r['texto'])) ?></p>
                    <p><strong>Fecha:</strong> <?= escaparHtml($r['fecha_creacion']) ?></p>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
        <a href="javascript:history.back()" class="btn">← Volver</a>
    </div>
</main>
<?php
$contenidoPrincipal = ob_get_clean();
require __DIR__ . '/../../includes/plantilla.php';
