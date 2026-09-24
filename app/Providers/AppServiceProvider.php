<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\Translator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->extend(Translator::class, function (Translator $translator, $app) {
            $locale = $app['config']['app.locale'] ?? 'pt_BR';

            $overridePath = base_path('lang/' . $locale . '.php');
            if (file_exists($overridePath)) {
                $messages = require $overridePath;
                foreach ($messages as $key => $value) {
                    $translator->addLines([$key => $value], $locale);
                }
            }

            return $translator;
        });
    }

    public function boot(): void
    {
        //
    }
}