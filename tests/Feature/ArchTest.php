<?php

declare(strict_types=1);

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

// Blocks debug and deprecated functions: die, var_dump, and similar.
arch()->preset()->php();

// Blocks risky functions: eval, md5, extract, and similar.
arch()->preset()->security();

arch('the application uses strict types and leaves no debug calls behind')
    ->expect('App')
    ->toUseStrictTypes()
    ->not->toUse(['dd', 'dump', 'die', 'var_dump', 'print_r', 'ray', 'exit']);

arch('environment variables are read only in configuration files')
    ->expect('env')
    ->not->toBeUsed()
    ->ignoring('App\Providers');

arch('controllers are suffixed')
    ->expect('App\Http\Controllers')
    ->toHaveSuffix('Controller');

arch('form requests extend the framework base class')
    ->expect('App\Http\Requests')
    ->toExtend(FormRequest::class)
    ->toHaveSuffix('Request');

arch('actions are final and expose a single entry point')
    ->expect('App\Actions')
    ->classes()
    ->toBeFinal()
    ->toHaveMethod('handle');

arch('actions do not reach for the current request')
    ->expect('App\Actions')
    ->not->toUse(['request', 'Illuminate\Support\Facades\Request']);

arch('services are final')
    ->expect('App\Services')
    ->classes()
    ->toBeFinal();

arch('data objects carry no behaviour beyond construction and conversion')
    ->expect('App\Data')
    ->classes()
    ->toBeFinal();

arch('models extend the framework base class')
    ->expect('App\Models')
    ->toExtend(Model::class)
    ->ignoring('App\Models\Builders');

arch('query builders extend the eloquent builder')
    ->expect('App\Models\Builders')
    ->toExtend(Illuminate\Database\Eloquent\Builder::class);

arch('enums are enums')
    ->expect('App\Enums')
    ->toBeEnums();

arch('jobs are queueable')
    ->expect('App\Jobs')
    ->toImplement(ShouldQueue::class);

arch('policies are suffixed')
    ->expect('App\Policies')
    ->toHaveSuffix('Policy');
