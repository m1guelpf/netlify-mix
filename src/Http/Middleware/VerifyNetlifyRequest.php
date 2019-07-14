<?php

namespace M1guelpf\NetlifyMix\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Facades\Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class VerifyNetlifyRequest
{
    public function handle(Request $request, Closure $next)
    {
        if (! $this->isDeployCompleted($request) || ($this->shouldVerifyHeader() && ! $this->matchesSecret($request))) {
            abort(404);
        }

        return $next($request);
    }

    protected function isDeployCompleted(Request $request) : bool
    {
        return $request->header('X-Netlify-Event') === 'deploy_created';
    }

    protected function shouldVerifyHeader() : bool
    {
        return ! is_null(config('netlify-mix.secret'));
    }

    public function matchesSecret(Request $request)
    {
        if (! $request->header('X-Webhook-Signature')) {
            return false;
        }

        $token = Parser::parse($request->header('X-Webhook-Signature'));

        return $token->getClaim('iss') === 'netlify' && $token->verify(new Sha256, config('netlify-mix.secret'));
    }
}
