<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

require __DIR__ . '/routes/produtos.php';
require __DIR__ . '/routes/usersAdmin.php';
require __DIR__ . '/routes/contracts.php';



// return function (App $app) {
//     $container = $app->getContainer();

//     $app->get('/[{name}]', function (Request $request, Response $response, array $args) use ($container) {
//         // Sample log message
//         $container->get('logger')->info("Slim-Skeleton '/' route");

//         // Render index view
//         return $container->get('renderer')->render($response, 'index.phtml', $args);
//     });
// };

$app->get('/', function (Request $request, Response $response) {
   
    return $response->withRedirect('/cliente');
});

$app->get('/check-login', function (Request $request, Response $response) {
       
    if (isset($_SESSION['user'])) {
       
        return $response->withJson(['loggedIn' => true]);
    } else {
        
        return $response->withJson(['loggedIn' => false]);
    }
});

$app->get('/logoutadmin', function ($request, $response) {
    
    session_destroy();
    
    
    return $response->withRedirect('/admin'); 
});
$app->get('/logout', function ($request, $response) {
   
    session_destroy();
    
    
    return $response->withRedirect('/'); // vai para a página de login
});