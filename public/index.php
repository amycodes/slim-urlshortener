<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require '../vendor/autoload.php';

$app = AppFactory::create();
$app->addRoutingMiddleware();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->get('/app/test', function (Request $request, Response $response, $args) {
    $response->getBody()->write("This has been a test.");
    return $response;
});

$app->get('/{slug}', function (Request $request, Response $response, $args) {
    $conn = mysqli_connect(getenv("MYSQLHOST"), getenv("MYSQLUSER"), getenv("MYSQLPASSWORD"), getenv("MYSQLDATABASE"), getenv("MYSQLPORT"));
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    $query = sprintf("select url from railway.aliases where slug = '%s'", $args['slug']);
    $r = mysqli_query($conn, $query);
    $row = mysqli_fetch_array($r,MYSQLI_ASSOC);
    mysqli_close($conn);

    return $response->withHeader('Location', $row['url'])->withStatus(302);
    
});

$app->run();