<?php

use App\Providers\AppServiceProvider;
use App\Providers\AutomationToolServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\SourceControlServiceProvider;

return [
    AppServiceProvider::class,
    AutomationToolServiceProvider::class,
    FortifyServiceProvider::class,
    SourceControlServiceProvider::class,
];
