<?php


# Para facilitar o laboratorio, não tendo que ficar criando virtual hosts
$patch = '/edcesar/laboratorio/fastRouter';
$_SERVER['REQUEST_URI'] = str_replace($patch, '', $_SERVER['REQUEST_URI']);



require_once __DIR__ . '/vendor/autoload.php';

$dispatcher = \FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/teste', 'App\Controlers\UserControler/getUser');

      $r->addRoute('GET', '/', function(){
        echo '/';
      });

	 $r->addRoute('GET', '/users', function(){
	 	print 'function ok';
	 });
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];


if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        print '404';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        print '405 Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
       
        if (is_callable($handler)) {
        	$handler($vars);
        	break;
        }
        
        list($class, $method) = explode("/", $handler, 2);
        call_user_func_array(array(new $class, $method), $vars);

        break;
}

