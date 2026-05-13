<?php

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/application.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/PedidoService.php';
require_once __DIR__ . '/../../includes/OfertaDAO.php';
require_once __DIR__ . '/../../includes/ProductoDAO.php';
require_once __DIR__ . '/../../includes/OfertaService.php';
require_once __DIR__ . '/../../includes/util.php';

$user = require_login();
if (!PedidoService::carritoTieneTipo()) {
    redirect('../../vistas/pedidos/elegirTipo.php');
}

$menuDelDia = OfertaDAO::getMenuDelDiaActivo();

if (!$menuDelDia) {
    flash_set('error', 'No hay ningún menú del día activo ahora mismo.');
    redirect('../../vistas/pedidos/catalogo.php');
}

$productosMenu = ProductoDAO::getProductosDeOferta($menuDelDia->getId());

if (empty($productosMenu)) {
    flash_set('error', 'El menú del día no tiene productos configurados.');
    redirect('../../vistas/pedidos/catalogo.php');
}

foreach ($productosMenu as $productoMenu) {
    $cantidad = max(1, (int)($productoMenu->cantidad ?? 1));
    PedidoService::agregarProductoAlCarrito(
        (int)$productoMenu->getId(),
        (float)$productoMenu->getPrecioFinal(),
        $cantidad
    );
}

if (!isset($_SESSION['ofertas_seleccionadas']) || !is_array($_SESSION['ofertas_seleccionadas'])) {
    $_SESSION['ofertas_seleccionadas'] = [];
}

if (!in_array($menuDelDia->getId(), $_SESSION['ofertas_seleccionadas'])) {
    $_SESSION['ofertas_seleccionadas'][] = $menuDelDia->getId();
}

$errores = OfertaService::aplicarOfertas($_SESSION['ofertas_seleccionadas']);
$_SESSION['errores_ofertas'] = $errores;

flash_set('success', 'Menú del día añadido al carrito.');
redirect('../../vistas/pedidos/carrito.php');
