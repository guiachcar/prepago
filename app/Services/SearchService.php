<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Services\Certificates\Contracts\CourtCertificateInterface;
use App\Rules\CpfCnpj;
use App\Jobs\ProcessCertificateSearch;
use Illuminate\Support\Facades\Bus;

class SearchService
{
    public function __construct(private readonly CourtCertificateInterface $certificateService) {}

    public function performSearch(Request $request, bool $useQueue = true)
    {
        try {
            $validated = $request->validate([
                'document' => ['required', 'string', new CpfCnpj()],
                'services' => 'required|array',
                'state' => 'nullable|string',
                'region' => 'nullable|string',
                'certificateType' => 'nullable|string',
                'labourRegion' => 'nullable|integer',
                'birthdate' => 'nullable|string',
            ]);

            if (!$useQueue) {
                return $this->processServicesSync($validated);
            }

            return $this->processServicesAsync($validated);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception('Erro ao realizar a busca: ' . $e->getMessage());
        }
    }

    private function processServicesSync(array $validated)
    {
        $results = [];
        foreach ($validated['services'] as $service) {
            $results[$service] = $this->executeService($service, $validated);
        }
        return $results;
    }

    private function processServicesAsync(array $validated)
    {
        $jobs = [];
        foreach ($validated['services'] as $service) {
            $jobs[] = new ProcessCertificateSearch($service, $validated, $this->certificateService);
        }

        $batch = Bus::batch($jobs)
            ->allowFailures()
            ->dispatch();

        return [
            'batch_id' => $batch->id,
            'status' => 'processing',
            'message' => 'Certificates search jobs queued successfully'
        ];
    }

    private function executeService(string $service, array $data)
    {
        return match($service) {
            'state-court' => $this->certificateService->getStateCourt(
                $data['document'],
                $data['state'],
                []
            ),
            'federal-court' => $this->certificateService->getFederalCourt(
                $data['document'],
                $data['region'],
                $data['certificateType'],
                []
            ),
            'labour-court' => $this->certificateService->getLabourCourt(
                $data['document'],
                $data['labourRegion'],
                true
            ),
            'protests' => $this->certificateService->getProtests(
                $data['document']
            ),
            'receita-federal' => $this->certificateService->getReceitaFederal(
                $data['document'],
                $data['birthdate'] ?? null
            ),
            'debt-certificate' => $this->certificateService->getDebtCertificate(
                $data['document']
            ),
            'cndt' => $this->certificateService->getCNDT(
                $data['document']
            ),
            default => throw new \InvalidArgumentException("Invalid service: {$service}")
        };
    }
}
