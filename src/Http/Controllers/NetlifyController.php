<?php

namespace M1guelpf\NetlifyMix\Http\Controllers;

use Illuminate\Http\Request;

class NetlifyController
{
    public function __invoke(Request $request)
    {
        $this->updateManifest($request->input('url'));

        return 'OK';
    }

    protected function updateManifest($baseUrl) : void
    {
        file_put_contents(public_path('mix-manifest.json'), $this->replaceManifestPaths($this->getManifest($baseUrl), $baseUrl));
    }

    protected function getManifest(string $baseUrl) : array
    {
        return json_decode(file_get_contents("$baseUrl/mix-manifest.json"), true);
    }

    protected function replaceManifestPaths(array $manifest, string $baseUrl) : string
    {
        return collect($manifest)->map(function ($path) use ($baseUrl) {
            return $baseUrl.$path;
        })->toJson();
    }
}
