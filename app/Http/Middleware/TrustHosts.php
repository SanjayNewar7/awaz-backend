<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    public function hosts()
    {
        return [
        $this->allSubdomainsOfApplicationUrl(),
        '192.168.1.70*',  // or your IP directly
    ];
}
}
