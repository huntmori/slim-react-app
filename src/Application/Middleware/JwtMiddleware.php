<?php

namespace App\Application\Middleware;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Actions\AppResponsePayload;
use App\Application\Settings\SettingsInterface;
use DI\Container;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\StreamFactory;

class JwtMiddleware implements MiddlewareInterface
{
    private string $secretKey;

    private string $HEADER_KEY_NAME = "Authorization";
    protected LoggerInterface $logger;

    public function __construct(
        SettingsInterface $settings,
        Container $container,
        LoggerInterface $logger
    )
    {
        $this->logger = $logger;
        $this->secretKey = $settings->get('config')['ENCRYPT_KEY'];
    }


    public function extractToken(ServerRequestInterface $request) : ?string
    {
        if(!$request->hasHeader($this->HEADER_KEY_NAME)) {
            return null;
        }
        return $request->getHeader($this->HEADER_KEY_NAME)[0];
    }

    public function validateTokenReturnUserIdx(string $token) : bool
    {
        // 암호화 디코드
        $tokenDecoded = JwtHandler::decryptToken($this->secretKey, $token);
        // claims 디코드
        $claims = JwtHandler::decodeJwt($tokenDecoded, $this->secretKey);

        // 유효기간 확인
        $expiredAt = $claims['exp'];
        $now = strtotime("now");
        if ($expiredAt < $now) {
            return false;
        }
        $userId = $claims['user_id'];
        // 세션 확인
        //session_start();
        //$tokens = $_SESSION[$userId];
        //if (!array_search($token, $tokens)) {
        //return false;
        //}

        // 유저 확인
        return $userId;
    }

    public function validateToken(string $token) : bool
    {
        return is_numeric($this->validateTokenReturnUserIdx($token));
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $token = $this->extractToken($request); // JWT 토큰 추출 메서드 구현

        $this->logger->info(
            "TOKEN PROCESS {$request->getUri()} : ".PHP_EOL
            ."TOKEN : ".$token
        );

        if ($token) {
            if ($this->validateToken($token)) { // JWT 토큰 검증 메서드 구현
                return $handler->handle($request);
            }
        }

        // 토큰이 유효하지 않은 경우 또는 토큰이 없는 경우 처리
        $actionPayload = new AppResponsePayload(
            401,
            null,
            new ActionError("authorization", "access denied")
        );
        $actionPayload->result = false;

        $response = new \Slim\Psr7\Response();
        $bodyStream = new StreamFactory();
        $body = $bodyStream->createStream(json_encode($actionPayload));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(401, 'Unauthorized')
            ->withBody($body);
    }
}