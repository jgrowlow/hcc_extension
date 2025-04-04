<?php
use Flarum\Extend;
use Flarum\Http\Middleware\CheckCsrfToken;
use CustomApi\Controllers\LoginController;
use CustomApi\Providers\CustomApiServiceProvider;

return [
    (new Extend\Routes('api'))
        ->post('/neoncrm/login', 'neoncrm.login', LoginController::class),

    (new Extend\ServiceProvider())
        ->register(CustomApiServiceProvider::class),

    // Add CheckCsrfToken middleware to the API pipeline
    (new Extend\Middleware('api'))
        ->add(CheckCsrfToken::class),
];

