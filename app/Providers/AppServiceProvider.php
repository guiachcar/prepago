<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Certificates\InfoSimples\InfoSimplesService;
use App\Services\Certificates\DirectData\DirectDataService;
use App\Services\Certificates\Contracts\CourtCertificateInterface;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CourtCertificateInterface::class, function ($app) {
            $infoSimples = new InfoSimplesService(config('services.infosimples.token'));
            $directData = new DirectDataService(config('services.directdata.token'));

            // Registra os serviços disponíveis
            $services = [
                'infosimples' => $infoSimples,
                'directdata' => $directData
            ];

            // Configura o fallback para cada serviço com base na configuração
            foreach (config('certificate-services.services') as $serviceConfig) {
                $primaryService = $services[$serviceConfig['primary']] ?? null;
                $fallbackService = $services[$serviceConfig['fallback']] ?? null;

                if ($primaryService && $fallbackService) {
                    $primaryService->setFallbackService($fallbackService);
                }
            }

            // Retorna o serviço configurado como primário para o primeiro serviço configurado
            // ou fallback para o InfoSimples se nenhuma configuração estiver disponível
            $firstService = array_values(config('certificate-services.services'))[0] ?? null;
            return $services[$firstService['primary'] ?? 'infosimples'] ?? $infoSimples;
        });
    }

    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    public function boot(): void
    {
        //
    }
}
