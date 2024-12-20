<?php

namespace App\Services\Certificates;

use App\Models\Certificate;
use Illuminate\Support\Facades\Log;
use App\Services\Certificates\Mocks\CertificateMocks;
use App\Services\Certificates\Contracts\CertificateServiceInterface;

abstract class AbstractCertificateService implements CertificateServiceInterface
{
    protected ?CertificateServiceInterface $fallbackService = null;
    protected array $serviceConfigs;

    public function __construct()
    {
        $this->serviceConfigs = config('certificate-services.services', []);
    }

    public function setFallbackService(?CertificateServiceInterface $service): void
    {
        $this->fallbackService = $service;
    }

    public function getFallbackService(): ?CertificateServiceInterface
    {
        return $this->fallbackService;
    }

    protected function shouldUseMocks(): bool
    {
        return app()->environment('local', 'testing');
    }

    protected function getMockResponse(string $method, array $params = []): ?array
    {
        if (!$this->shouldUseMocks()) {
            return null;
        }

        $mockMethod = match($method) {
            'getReceitaFederal' => 'getReceitaFederalMock',
            'getFederalCourt' => 'getFederalCourtMock',
            'getProtests' => 'getProtestsMock',
            'getCNDT' => 'getCNDTMock',
            'getDebtCertificate' => 'getDebtCertificateMock',
            default => null
        };

        if ($mockMethod === null) {
            return null;
        }

        return CertificateMocks::$mockMethod(...$params);
    }

    protected function shouldHandleRequest(string $serviceKey): bool
    {
        $config = $this->serviceConfigs[$serviceKey] ?? null;
        if (!$config) {
            return true;
        }

        $currentServiceName = $this->getServiceName();
        return $config['primary'] === $currentServiceName;
    }

    protected function getServiceName(): string
    {
        $className = class_basename(static::class);
        if (str_ends_with($className, 'Service')) {
            $className = substr($className, 0, -7);
        }
        return strtolower($className);
    }

    protected function tryWithFallback(string $method, array $params = [])
    {
        $mockResponse = $this->getMockResponse($method, $params);
        if ($mockResponse !== null) {
            return $mockResponse;
        }

        try {
            $actualMethod = "_{$method}";
            $response = $this->$actualMethod(...$params);
            $saveMethod = "_saveCertificate";
            $this->$saveMethod($method, $response, $params);
            return $response;
        } catch (\Exception $e) {
            Log::error("Error in {$method}", [
                'service' => static::class,
                'error' => $e->getMessage(),
                'params' => $params
            ]);

            if ($this->fallbackService && method_exists($this->fallbackService, $method)) {
                return $this->fallbackService->$method(...$params);
            }

            throw $e;
        }
    }

    public function executeServiceMethod(string $serviceKey, array $params = []): array
    {
        $config = $this->serviceConfigs[$serviceKey] ?? null;
        
        if (!$config) {
            throw new \InvalidArgumentException("Service configuration not found for {$serviceKey}");
        }

        if (!$this->shouldHandleRequest($serviceKey)) {
            if ($this->fallbackService) {
                return $this->fallbackService->executeServiceMethod($serviceKey, $params);
            }
            throw new \RuntimeException("Service {$this->getServiceName()} is not configured as primary for {$serviceKey} and no fallback is available");
        }

        $method = $config['method'];
        $response = $this->tryWithFallback($method, $params);
        return $response;
    }
}
