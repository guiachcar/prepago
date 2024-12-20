<?php

namespace App\Interfaces;

interface CertificateServiceInterface
{
    public function getProtestCertificate(string $document): array;
    public function getFederalRevenueCertificate(string $document, ?string $birthDate = null): array;
    public function getLabourCourtCertificate(string $document, string $region): array;
    public function getFederalCourtCertificate(string $document, string $region, string $type): array;
    public function getStateCertificate(string $document, string $state): array;
}
