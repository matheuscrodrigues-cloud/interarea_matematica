<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller/PaginaAlgebraController.php';
$pagina = new PaginaAlgebraController()->preparar($_GET, $_POST, $_SERVER['REQUEST_METHOD']);
require __DIR__ . '/View/pagina.php';
