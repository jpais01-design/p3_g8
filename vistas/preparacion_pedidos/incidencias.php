<?php
require_once __DIR__ . '/../../includes/application.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/util.php';
require_once __DIR__ . '/../../includes/IncidenciaDAO.php';

$user = require_role('gerente');
$incidencias = IncidenciaDAO::getAllConPedido();

$tituloPagina = 'Incidencias | Panel Gerente';
ob_start();
?>
<div class="panel">
    <div class="actions-inline flex-between">
        <h2>⚠️ Historial de incidencias</h2>
        <a href="panel_gerente.php" class="btn">← Volver panel</a>
    </div>

    <div class="table-wrap">
        <table class="tabla-panel tabla-movil">
            <thead>
                <tr class="tabla-panel-cabecera">
                    <th>ID incidencia</th>
                    <th>Pedido</th>
                    <th>Cliente</th>
                    <th>Fecha pedido</th>
                    <th>Incidencia</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($incidencias)): ?>
                    <tr><td colspan="5" class="tabla-panel-vacia">No hay incidencias registradas.</td></tr>
                <?php else: ?>
                    <?php foreach ($incidencias as $i): ?>
                        <tr class="tabla-panel-fila">
                            <td>#<?= (int)$i['id'] ?></td>
                            <td>#<?= escaparHtml((string)$i['numero_pedido']) ?> (id <?= (int)$i['pedido_id'] ?>)</td>
                            <td><?= escaparHtml($i['username']) ?></td>
                            <td><?= escaparHtml($i['fecha_hora']) ?></td>
                            <td><?= nl2br(escaparHtml($i['incidencia'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$contenidoPrincipal = ob_get_clean();
require __DIR__ . '/../../includes/plantilla.php';
