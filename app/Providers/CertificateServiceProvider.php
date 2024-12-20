<?php

namespace App\Providers;

use App\Services\Certificates\Contracts\CourtCertificateInterface;
use App\Services\Certificates\DirectData\DirectDataService;
use App\Services\Certificates\InfoSimples\InfoSimplesService;
use Illuminate\Support\ServiceProvider;

class CertificateServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CourtCertificateInterface::class, function ($app) {
            $primaryService = config('services.certificates.primary');
            $fallbackService = config('services.certificates.fallback');

            $service = match($primaryService) {
                'directdata' => new DirectDataService(config('services.directdata.token')),
                'infosimples' => new InfoSimplesService(config('services.infosimples.token')),
                default => throw new \InvalidArgumentException('Invalid certificate service configuration')
            };

            if ($fallbackService) {
                $fallback = match($fallbackService) {
                    'directdata' => new DirectDataService(config('services.directdata.token')),
                    'infosimples' => new InfoSimplesService(config('services.infosimples.token')),
                    default => null
                };

                if ($fallback) {
                    $service->setFallbackService($fallback);
                }
            }

            return $service;
        });
    }
}
