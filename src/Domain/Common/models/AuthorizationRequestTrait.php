<?php

namespace App\Domain\Common\models;
use Psr\Http\Message\ServerRequestInterface as Request;

trait AuthorizationRequestTrait
{
    static string $tokenHeaderKey = "Authorization";

    public function extractToken(Request $request) : ?string {
        if($request->hasHeader(self::$tokenHeaderKey)) {
            $request->getHeader(self::$tokenHeaderKey);
        }
        return null;
    }
}