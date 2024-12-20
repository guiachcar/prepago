<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Certificate;
use Illuminate\Http\Request;
use App\Services\Certificates\Contracts\CourtCertificateInterface;
use App\Services\SearchService;

class CertificateController extends Controller
{
    public function __construct(
        private readonly CourtCertificateInterface $certificateService,
        private readonly SearchService $searchService
    ) {}

    public function index()
    {
        $certificates = Certificate::latest()
            ->get()
            ->groupBy('document')
            ->map(function ($group) {
                return $group->map(fn ($certificate) => [
                    'document' => $certificate->document,
                    'service' => $this->getServiceName($certificate->service),
                    'status' => $certificate->status,
                    'message' => $certificate->message,
                    'created_at' => $certificate->created_at,
                ]);
            });

        $formattedResults = [];
        foreach ($certificates as $document => $items) {
            foreach ($items as $item) {
                $formattedResults[$document][] = [
                    'service' => $item['service'],
                    'status' => $item['status'],
                    'message' => $item['message'],
                ];
            }
        }

        return Inertia::render('Certificates/Index', [
            'documents' => $formattedResults,
        ]);
    }

    public function getDocumentDetails($document)
    {
        $certificates = Certificate::where('document', $document)
            ->latest()
            ->get()
            ->map(function ($certificate) {
                return [
                    'document' => $certificate->document,
                    'service' => $this->getServiceName($certificate->service),
                    'params' => json_decode($certificate->params, true),
                    'status' => $certificate->status,
                    'message' => $certificate->message,
                    'result' => json_decode($certificate->result, true),
                    'region' => $certificate->region,
                    'url_certificate' => urldecode($certificate->url_certificate),
                    'created_at' => $certificate->created_at,
                ];
            });

        return response()->json($certificates);
    }

    public function search(Request $request)
    {
        try {
            $useQueue = $request->input('async', true);
            $results = $this->searchService->performSearch($request, $useQueue);
            return Inertia::render('Certificates/Index', ['results' => $results]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('certificates.index')->withErrors($e->validator->errors());
        } catch (\Exception $e) {
            return redirect()->route('certificates.index')->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function apiSearch(Request $request)
    {
        try {
            $useQueue = $request->input('async', true);
            $results = $this->searchService->performSearch($request, $useQueue);
            return response()->json(['results' => $results]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getServiceName($service)
    {
        $serviceNames = [
            'getFederalCourt' => 'Consulta ao Tribunal Federal',
            'getLabourCourt' => 'Consulta ao Tribunal do Trabalho',
            'getProtests' => 'Consulta de Protestos',
            'getReceitaFederal' => 'Consulta à Receita Federal',
            'getDebtCertificate' => 'Certidão de Débitos',
            'getCNDT' => 'Certidão Negativa de Débitos Trabalhistas (CNDT)',
        ];

        return $serviceNames[$service] ?? $service;
    }
}
