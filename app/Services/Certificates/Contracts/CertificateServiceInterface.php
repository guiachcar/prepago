<?php

namespace App\Services\Certificates\Contracts;

interface CertificateServiceInterface
{
    public function setFallbackService(?CertificateServiceInterface $service): void;
    public function getFallbackService(): ?CertificateServiceInterface;
    public function executeServiceMethod(string $serviceKey, array $params = []): array;
}
