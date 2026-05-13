<?php

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/application.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/util.php';
require_once __DIR__ . '/../../includes/PedidoService.php';
require_once __DIR__ . '/../../includes/OfertaDAO.php';
require_once __DIR__ . '/../../includes/ProductoDAO.php';

$user = require_login();
if (!PedidoService::carritoTieneTipo()) {
    redirect('elegirTipo.php');
}

$menuDelDia = OfertaDAO::getMenuDelDiaActivo();

$tituloPagina = 'Menú del día | Bistro FDI';
$rutaCSS = RUTA_APP . '/CSS/estilo.css';

ob_start();
?>

<main>
  <div class="panel">
    <h2>🍽️ Menú del día</h2>

    <?php if (!$menuDelDia): ?>
      <p>No hay menú del día activo actualmente.</p>
      <a href="catalogo.php" class="btn">← Volver al catálogo</a>
    <?php else: ?>
      <?php $productos = ProductoDAO::getProductosDeOferta($menuDelDia->getId()); ?>

      <p><strong>Nombre:</strong> <?= escaparHtml($menuDelDia->getNombre()) ?></p>
      <p><strong>Descripción:</strong> <?= nl2br(escaparHtml($menuDelDia->getDescripcion())) ?></p>
      <p><strong>Válido hasta:</strong> <?= escaparHtml(date('d/m/Y H:i', strtotime($menuDelDia->getFechaFin()))) ?></p>

      <?php $precioTotal = 0.0; ?>
      <h3>Incluye</h3>
      <ul>
        <?php foreach ($productos as $p):
          $cantidad = (int)($p->cantidad ?? 1);
          $subtotal = (float)$p->getPrecioFinal() * $cantidad;
          $precioTotal += $subtotal;
        ?>
          <li><?= escaparHtml($p->getNombre()) ?> x<?= $cantidad ?> — <?= round($subtotal, 2) ?> €</li>
        <?php endforeach; ?>
      </ul>

      <?php $precioFinal = $menuDelDia->aplicarDescuento($precioTotal); ?>
      <p><strong>Precio productos:</strong> <?= round($precioTotal, 2) ?> €</p>
      <p><strong>Descuento menú:</strong> <?= round((float)$menuDelDia->getDescuento(), 2) ?> %</p>
      <p><strong>Precio final menú:</strong> <?= round($precioFinal, 2) ?> €</p>

      <div class="actions-inline">
        <a href="catalogo.php" class="btn">← Volver al catálogo</a>
        <form method="POST" action="../../scripts/pedidos/addMenuDelDia.php" style="display:inline;">
          <button type="submit" class="btn primary">Quiero el menú del día</button>
        </form>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php
$contenidoPrincipal = ob_get_clean();
require __DIR__ . '/../../includes/plantilla.php';
