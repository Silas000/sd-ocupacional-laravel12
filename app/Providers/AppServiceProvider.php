<?php

namespace App\Providers;

use App\Http\Controllers\SettingsController;
use App\Models\Exam;
use App\Models\HealthRecord;
use App\Models\Incident;
use App\Models\Risk;
use App\Models\User;
use App\Policies\ExamPolicy;
use App\Policies\HealthRecordPolicy;
use App\Policies\IncidentPolicy;
use App\Policies\RiskPolicy;
use App\Policies\SettingsPolicy;
use App\Policies\UserPolicy;
use App\Services\AppSettings;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\Translator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AppSettings::class);

        $this->app->extend(Translator::class, function (Translator $translator, $app) {
            $locale = $app['config']['app.locale'] ?? 'pt_BR';

            $overridePath = base_path('lang/'.$locale.'.php');
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
        Gate::policy(Exam::class, ExamPolicy::class);
        Gate::policy(HealthRecord::class, HealthRecordPolicy::class);
        Gate::policy(Incident::class, IncidentPolicy::class);
        Gate::policy(Risk::class, RiskPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(SettingsController::class, SettingsPolicy::class);

        /*
         * Toda tela recebe as configurações já resolvidas, o que evita
         * repetir o mesmo tratamento de cores e ícones em cada Blade.
         */
        View::composer('*', function ($view) {
            $view->with('appSettings', $this->app->make(AppSettings::class));
        });
    }
}
