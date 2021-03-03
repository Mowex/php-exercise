<?php
require_once('db/clsconn.php');
require_once('controllers/ProductController.php');

$controller = 'Product';

if(!isset($_REQUEST['c'])) {
    require_once ('controllers/'.$controller.'Controller.php');
    $controller = ucwords($controller) . 'Controller';
    $controller = new $controller;
    $controller->index();    
} else {
    // Obtenemos el controlador que queremos cargar
    $controller = strtolower($_REQUEST['c']);
    $accion = isset($_REQUEST['a']) ? $_REQUEST['a'] : 'index';

    // Instanciamos el controlador
    if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) && !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        require_once ('controllers/'.$controller.'Controller.php');
    }
    $controller = ucwords($controller) . 'Controller';
    $controller = new $controller;

    // Llama la accion
    call_user_func( array( $controller, $accion ) );
}