<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Symfony\Component\HttpFoundation\Request;

class TrustProxies extends Middleware
{
    /**
     * Confia em todos os proxies — necessário para Railway, Render,
     * Heroku, Vercel, Nginx, Cloudflare etc.
     */
    protected $proxies = '*';

    /**
     * Diz ao Laravel para ler corretamente os headers X-Forwarded
     * e detectar HTTPS real.
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}
