<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batchable;
use App\Services\Certificates\Contracts\CourtCertificateInterface;

class ProcessCertificateSearch implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $service,
        private readonly array $data,
        private readonly CourtCertificateInterface $certificateService
    ) {}

    public function handle()
    {
        return match($this->service) {
            'state-court' => $this->certificateService->getStateCourt(
                $this->data['document'],
                $this->data['state'],
                []
            ),
            'federal-court' => $this->certificateService->getFederalCourt(
                $this->data['document'],
                $this->data['region'],
                $this->data['certificateType'],
                []
            ),
            'labour-court' => $this->certificateService->getLabourCourt(
                $this->data['document'],
                $this->data['labourRegion'],
                true
            ),
            'protests' => $this->certificateService->getProtests(
                $this->data['document']
            ),
            'receita-federal' => $this->certificateService->getReceitaFederal(
                $this->data['document'],
                $this->data['birthdate'] ?? null
            ),
            'debt-certificate' => $this->certificateService->getDebtCertificate(
                $this->data['document']
            ),
            'cndt' => $this->certificateService->getCNDT(
                $this->data['document']
            ),
            default => throw new \InvalidArgumentException("Invalid service: {$this->service}")
        };
    }
}
