<?php

namespace App\Services\Certificates\InfoSimples;

use App\Models\Certificate;
use Illuminate\Support\Facades\Http;
use App\Services\Certificates\AbstractCertificateService;
use App\Services\Certificates\Contracts\CourtCertificateInterface;

class InfoSimplesService extends AbstractCertificateService implements CourtCertificateInterface
{
    private const BASE_URL = 'https://api.infosimples.com/api/v2';
    private const DEGREE = 1;
    private readonly string $token;

    public function __construct(string $token)
    {
        parent::__construct();
        $this->token = $token;
    }

    protected function getServiceName(): string
    {
        return 'infosimples';
    }

    public function getStateCourt(string $document, string $state, array $options = []): array
    {
        return $this->executeServiceMethod('state-court', [$document, $state, $options]);
    }

    protected function _getStateCourt(string $document, string $state, array $options = []): array
    {
        $endpoints = [
            'BA' => [
                'certificate' => '/consultas/tribunal/tjba/primeiro-grau',
            ],
            'DF' => [
                'certificate' => '/consultas/tribunal/tjdf/nada-consta',
            ],
            'GO' => [
                'certificate' => '/consultas/tribunal/tjgo/nada-consta',
            ],
            'MG' => [
                'process' => '/consultas/tribunal/tjmg/processo',
            ],
            'RJ' => [
                'certificate' => '/consultas/tribunal/tjrj/pedido-cert',
                'process' => '/consultas/tribunal/tjrj/processo',
            ],
            'RS' => [
                'certificate' => '/consultas/tribunal/tjrs/primeiro-grau',
            ],
            'SC' => [
                'certificate' => '/consultas/tribunal/tjsc/pedido-certidao',
                'process' => '/consultas/tribunal/tjsc/obter-certidao',
            ],
            'SP' => [
                'certificate' => '/consultas/tribunal/tjsp/pedido-certidao',
                'process' => '/consultas/tribunal/tjsp/primeiro-grau',
            ],
        ];
        $state = strtoupper($state);
        if (!isset($endpoints[$state])) {
            return [
                'certificate' => [
                    'error' => 'State not supported'
                ],
                'process' => [
                    'error' => 'State not supported'
                ]
            ];
        }
        $params = [
            'token' => $this->token,
            'uf' => $state,
            'grau' => self::DEGREE
        ];

        if (strlen($document) === 11) {
            $params['cpf'] = $document;
        } else {
            $params['cnpj'] = $document;
        }

        $results = [];
        if (isset($endpoints[$state]['certificate'])) {
            $results['certificate'] = Http::get(self::BASE_URL . $endpoints[$state]['certificate'], $params)->json();
        }
        if (isset($endpoints[$state]['process'])) {
            $results['process'] = Http::get(self::BASE_URL . $endpoints[$state]['process'], $params)->json();
        }

        return $results;
    }

    public function getFederalCourt(string $document, string $region, string $type, array $options = []): array
    {
        $type = (int) $type;
        $typesNames = [1 => 'Certidão Cível', 2 => 'Certidão Criminal', 0 => 'Todos'];
        $results = [];

        if ($type === 0) {
            unset($typesNames[0]);
            foreach ($typesNames as $type => $typeName) {
                $results[$typeName] = $this->executeServiceMethod('federal-court', [$document, $region, $type, $options]);
            }
            return $results;
        }

        return [
            $typesNames[$type] => $this->executeServiceMethod('federal-court', [$document, $region, $type, $options])
        ];
    }

    protected function _getFederalCourt(string $document, string $region, string $type, array $options = []): array
    {
        $region = strtolower($region);
        
        $params = [
            'token' => $this->token,
            'tipo' => $type
        ];

        if (strlen($document) === 11) {
            $params['cpf'] = $document;
        } else {
            $params['cnpj'] = $document;
        }

        if ($region === 'trf1') {
            $params['considera_filiais'] = $options['considera_filiais'] ?? '1';
        }

        if ($region === 'trf2') {
            $params['tipo_certidao'] = $options['tipo_certidao'] ?? '1';
        }

        if ($region === 'trf3') {
            $params['abrangencia'] = $options['abrangencia'] ?? '1';
            $endpoint = "certidao-distr";
        } else {
            $endpoint = "certidao";
        }

        return Http::withHeaders([
            'Accept' => 'application/json',
        ])->timeout(200)->post(self::BASE_URL . "/consultas/tribunal/{$region}/{$endpoint}", $params)->json();
    }

    public function getLabourCourt(string $document, int $region, bool $generateProof = true): array
    {
        return $this->executeServiceMethod('labour-court', [$document, $region, $generateProof]);
    }

    protected function _getLabourCourt(string $document, int $region, bool $generateProof = true): array
    {
        $params = [
            'token' => $this->token,
            'regiao' => $region
        ];

        if (strlen($document) === 11) {
            $params['cpf'] = $document;
        } else {
            $params['cnpj'] = $document;
        }

        return Http::get(self::BASE_URL . "/consultas/tribunal/trt{$region}/ceat", $params)->json();
    }

    public function getProtests(string $document): array
    {
        return $this->executeServiceMethod('protests', [$document]);
    }

    protected function _getProtests(string $document): array
    {
        $params = [
            'token' => $this->token,
        ];

        if (strlen($document) === 11) {
            $params['cpf'] = $document;
        } else {
            $params['cnpj'] = $document;
        }

        return Http::post(self::BASE_URL . '/consultas/cenprot-sp/protestos', $params)->json();
    }

    public function getReceitaFederal(string $document, ?string $birthdate = null): array
    {
        return $this->executeServiceMethod('receita-federal', [$document, $birthdate]);
    }

    protected function _getReceitaFederal(string $document, $birthdate = null): array
    {
        if (strlen($document) === 11) {
            $params = [
                'token' => $this->token,
                'cpf' => $document,
                'birthdate' => $birthdate
            ];
            $method = 'cpf';
        } else {
            $params = [
                'token' => $this->token,
                'cnpj' => $document
            ];
            $method = 'cnpj';
        }

        return Http::post(self::BASE_URL . '/consultas/receita-federal/' . $method, $params)->json();
    }

    public function getDebtCertificate(?string $document = null): array
    {
        return $this->executeServiceMethod('debt-certificate', [$document]);
    }

    protected function _getDebtCertificate(?string $document = null): array
    {
        $params = [
            'token' => $this->token
        ];

        if ($document) {
            if (strlen($document) === 11) {
                $params['cpf'] = $document;
            } else {
                $params['cnpj'] = $document;
            }
        }

        return Http::post(self::BASE_URL . '/consultas/receita-federal/pgfn/nova', $params)->json();
    }

    public function getCNDT(string $document): array
    {
        return $this->executeServiceMethod('cndt', [$document]);
    }

    protected function _getCNDT(string $document): array
    {
        $params = [
            'token' => $this->token,
        ];

        if (strlen($document) === 11) {
            $params['cpf'] = $document;
        } else {
            $params['cnpj'] = $document;
        }

        return Http::post(self::BASE_URL . '/consultas/tribunal/tst/cndt', $params)->json();
    }

    protected function _saveCertificate(string $serviceKey, array $data, array $params): void
    {
        Certificate::create([
            'document' => $params[0],
            'service' => $serviceKey,
            'params' => json_encode($params ?? []),
            'status' => 'completed',
            'message' => $data['code_message'] ?? null,
            'result' => json_encode($data['data'] ?? $data),
            'region' => $params[1] ?? null,
            'url_certificate' => isset($data['site_receipts']) ? $data['site_receipts'][0] : null,
        ]);
    }
}
