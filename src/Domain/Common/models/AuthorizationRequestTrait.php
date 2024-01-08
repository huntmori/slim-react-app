<?php

namespace App\Domain\Common\models;
use Psr\Http\Message\ServerRequestInterface as Request;

trait AuthorizationRequestTrait
{
    static string $tokenHeaderKey = "Authorization";

    public function extractToken(Request $request) : ?string {
        echo self::$tokenHeaderKey.PHP_EOL;
        echo $request->getHeader(self::$tokenHeaderKey)[0].PHP_EOL;
        if($request->hasHeader(self::$tokenHeaderKey)) {
            return $request->getHeader(self::$tokenHeaderKey)[0];
        }
        return null;
    }
}