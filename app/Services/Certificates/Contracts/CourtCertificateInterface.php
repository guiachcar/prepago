<?php

namespace App\Services\Certificates\Contracts;

interface CourtCertificateInterface extends CertificateServiceInterface
{
    public function getStateCourt(string $document, string $state, array $options = []): array;
    public function getFederalCourt(string $document, string $region, string $type, array $options = []): array;
    public function getLabourCourt(string $document, int $region, bool $generateProof = true): array;
    public function getProtests(string $document): array;
    public function getReceitaFederal(string $document, ?string $birthdate = null): array;
    public function getDebtCertificate(?string $document = null): array;
    public function getCNDT(string $document): array;
}
