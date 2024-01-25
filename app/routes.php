<?php

declare(strict_types=1);

use App\Application\Common\MemberPasswordEncrypt;
use App\Application\Middleware\JwtMiddleware;
use App\Domain\Profile\controller\ProfileController;
use App\Domain\User\actions\UserCreateAction;
use App\Domain\User\controller\UserController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $app->options('/{routes:.+}', function ($request, $response, $args) {
        echo 'options'.PHP_EOL;
        return $response;
    });
    $app->add(function ($request, $handler) {
        echo 'cors middleware' .PHP_EOL;
        $response = $handler->handle($request);
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/api/user', function (RouteCollectorProxy $group) use ($app) {
        $group->post('', UserController::class.':createUser');
        $group->post('/login', UserController::class.':userLogin');
    });

    $app->get(
        '/api/user/id:{id}',
        UserController::class.':getUser'
    )->add(JwtMiddleware::class);

    $app->group("/api/profile", function (RouteCollectorProxy $group) {
        $group->put(
            "",
            ProfileController::class . ':createUserProfile'
        )->add(JwtMiddleware::class);

        $group->get(
            "/{uid}",
            ProfileController::class.":getProfile"
        )->add(JwtMiddleware::class);

        $group->get(
            "",
            ProfileController::class.":getProfiles"
        )->add(JwtMiddleware::class);

        $group->patch(
            "/{uid}/activation",
            ProfileController::class.":updateProfileActivation"
        )->add(JwtMiddleware::class);
    });
    /*
    $app->get("/test", function(Request $request, Response $response) use ($app) {
        $settings = $app->getContainer()->get(SettingsInterface::class);
        $body = ['profile'=>$settings->get("profile"), 'config'=>$settings->get("config")];
        echo json_encode($body).PHP_EOL;
        $response->getBody()->write(json_encode($body));
        $response->withStatus(200);
        return $response->withHeader("Content-Type", "application/json");
    });

    $app->get("/conn", function(Request $request, Response $response) use ($app) {
        /** @var PDO $pdo
        $pdo = $app->getContainer()->get(PDO::class);

        $stmt = $pdo->prepare("select 1 as one, now() as now");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $body = [
            'result'=>true,
            'data'=>$rows,
        ];
        $response->getBody()->write(json_encode($body));
        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json');
    });

    $app->get('/redis-test', function(Request $request, Response $response) use ($app) {
        /** @var Predis\Client $client
        $client = $app->getContainer()->get(Predis\Client::class);

        $client->set("test", 1);
        return $response->withStatus(200);
    });


    $app->get('/enc-test', function(Request $request, Response $response) use ($app) {
        /** @var MemberPasswordEncrypt $memberEncrypt
        $memberEncrypt = $app->getContainer()->get(MemberPasswordEncrypt::class);
        $stringTest = "test";

        if($request->getQueryParams()['target']) {
           $stringTest = $request->getQueryParams()['target'];
        }
        $enc = $stringTest;

        $enc = $memberEncrypt->encrypt($enc);

        $body = [
           'plainText' => $stringTest,
           'encryptedText' => $enc
        ];

        $response->getBody()->write(json_encode($body));
        return $response->withStatus(200)
           ->withHeader('Content-Type', 'Application/json');

    });
*/
    $app->get('/dec-test', function(Request $request, Response $response) use ($app) {
        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $app->getContainer()->get(\Psr\Log\LoggerInterface::class);
        $logger->info("/dec-test");
        /** @var MemberPasswordEncrypt $memberEncrypt*/
        $memberEncrypt = $app->getContainer()->get(MemberPasswordEncrypt::class);

        $stringTest = $request->getQueryParams()['target'];
        $origin = $request->getQueryParams()['origin'];

        $enc = $memberEncrypt->decrypt($stringTest);

        $body = [
            'plainText' => $origin,
            'encryptedText' => $stringTest,
            'dec' => $enc,
            'verify' => $enc === $origin
        ];

        $response->getBody()->write(json_encode($body));
        return $response->withStatus(200)
            ->withHeader('Content-Type', 'Application/json');

    });
    /**/
};
