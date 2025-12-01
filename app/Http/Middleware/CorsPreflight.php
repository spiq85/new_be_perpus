<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsPreflight
{
    public function handle(Request $req, Closure $next)
    {
        $res = $next($req);

        $origin = $req->headers->get('Origin');
        $allowed = ['http://localhost:3000','http://127.0.0.1:3000'];

        if ($origin && in_array($origin, $allowed, true)) {
            $res->headers->set('Access-Control-Allow-Origin', $origin);
            $res->headers->set('Vary', 'Origin');
            $res->headers->set('Access-Control-Allow-Methods','GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $res->headers->set('Access-Control-Allow-Headers','Content-Type, Authorization, X-Requested-With,');
            $res->headers->set('Access-Control-Allow-Credentials','true'); // aman walau kamu pakai token
            $res->headers->set('Access-Control-Max-Age','86400');
        }

        if ($req->isMethod('OPTIONS')) {
            return response('', 204)->withHeaders($res->headers->all());
        }

        return $res;
    }
}
