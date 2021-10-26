<?php

namespace Denarx\laravelInstaller;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Env;
use Illuminate\Support\ServiceProvider;

class InstallServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->installed()) return;
        $this->laravel = app() instanceof \Illuminate\Foundation\Application;

        if (method_exists(app(), 'withFacades')) app()->withFacades();
        
        $envFile = App::basePath() . '\.env';
        if (!file_exists($envFile)) {
            if (!file_exists($envFile . '.example'))  abort(502, 'File ".env.example" not found');
            if (!@copy($envFile . '.example', $envFile)) abort(503, 'File ".env" can\'t be created');
            if ($this->laravel) Artisan::call('key:generate');
        }
        Route::get('/install', function () {
            Artisan::call('migrate --seed');
            Artisan::call("env:set APP_INSTALLED=true");
            return redirect('/');
        });
        Route::post('/', ['as' => 'install', function () {
            $r = validator(request()->all(), [
                'host' => 'required',
                'username' => 'required',
                'password' => 'required',
                'database' => 'required'
            ])->validate();
            $env = [
                'DB_HOST' => $r['host'],
                'DB_USERNAME' => $r['username'],
                'DB_PASSWORD' => $r['password'],
                'DB_DATABASE' => $r['database'],
            ];
            foreach ($env as $k => $v) {
                Artisan::call("env:set $k=$v");
            }
            return redirect('/install');
        }]);

        $template = $this->template;
        if ($this->laravel) {
            Route::get('{url}', function () use ($template) {
                return $template;
            })->where(['url' => '|install']);
            Route::fallback(function () {
                return redirect('/');
            });
        } else {
            Route::get('', function () use ($template) {
                return $template;
            });
            Route::get('{url}', function () {
                return redirect('/');
            });
        }
        return;
    }

    /**
     * If application is already installed
     *
     * @return bool
     */
    public function installed()
    {
        return Env::get('APP_INSTALLED');
    }

    /**
     * Flag to determine Framework.
     */
    private $laravel;

    /**
     * Template with installing form.
     */
    private $template = '
    <html lang="en">

        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>First run installation</title>
            <link rel="dns-prefetch" href="https://fonts.gstatic.com">
            <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>

        <body>
            <div class="container py-4">
                <div class="row justify-content-center">
                    <div class="col-sm-11 col-md-9 col-lg-7 col-xl-6 col-xxl-5">
                        <div class="card">
                            <div class="card-header">First run installation</div>
                            <div class="card-body">
                                <legend>Mysql Database setup</legend>
                                <form method="POST" action>
                                    <div class="form-floating mb-3">
                                        <input name="host" type="text" class="form-control" id="host" placeholder="127.0.0.1" required autofocus>
                                        <label for="host">Host</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input name="username" type="text" class="form-control" id="username" placeholder="1@1.com" required autocomplete="username">
                                        <label for="username">User</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input name="password" type="password" class="form-control" id="password" placeholder="password" required autocomplete="password">
                                        <label for="password">Password</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input name="database" type="text" class="form-control form-control-sm" id="database" placeholder="db_name" required>
                                        <label for="database">Database name</label>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary center">Install</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>  
    </html>
    ';
}
