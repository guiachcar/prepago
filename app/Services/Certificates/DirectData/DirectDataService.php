<?php

namespace App\Services\Certificates\DirectData;

use App\Models\Certificate;
use Illuminate\Support\Facades\Http;
use App\Services\Certificates\AbstractCertificateService;
use App\Services\Certificates\Contracts\CourtCertificateInterface;

class DirectDataService extends AbstractCertificateService implements CourtCertificateInterface
{
    private const BASE_URL = 'https://apiv3.directd.com.br/api';
    private const DEGREE = 1;
    private readonly string $token;

    public function __construct(string $token)
    {
        parent::__construct();
        $this->token = $token;
    }

    protected function getServiceName(): string
    {
        return 'directdata';
    }

    public function getStateCourt(string $document, string $state, array $options = []): array
    {
        return $this->executeServiceMethod('state-court', [$document, $state, $options]);
    }

    protected function _getStateCourt(string $document, string $state, array $options = []): array
    {
        $params = [
            'TOKEN' => $this->token,
            'UF' => $state,
            'GRAU' => self::DEGREE,
        ];

        if (strlen($document) === 11) {
            $params['CPF'] = $document;
        } else {
            $params['CNPJ'] = $document;
        }

        return Http::get(self::BASE_URL . '/TribunalJustica', $params)->json();
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

        if (array_key_exists($type, $typesNames)) {
            return [
                $typesNames[$type] => $this->executeServiceMethod('federal-court', [$document, $region, $type, $options])
            ];
        }

        throw new \InvalidArgumentException("Tipo inválido: {$type}");
    }

    protected function _getFederalCourt(string $document, string $region, string $type, array $options = []): array
    {
        $params = [
            'token' => $this->token,
            'regiao' => $region,
            'tipo' => $type,
            'gerarComprovante' => true
        ];

        if (strlen($document) === 11) {
            $params['cpf'] = $document;
        } else {
            $params['cnpj'] = $document;
        }

        return Http::get(self::BASE_URL . '/TribunalRegionalFederal', $params)->json();
    }

    public function getLabourCourt(string $document, int $region, bool $generateProof = true): array
    {
        return $this->executeServiceMethod('labour-court', [$document, $region, $generateProof]);
    }

    protected function _getLabourCourt(string $document, int $region, bool $generateProof = true): array
    {
        $params = [
            'TOKEN' => $this->token,
            'REGIAO' => $region,
            'GERARCOMPROVANTE' => $generateProof ? 'true' : 'false'
        ];

        if (strlen($document) === 11) {
            $params['CPF'] = $document;
        } else {
            $params['CNPJ'] = $document;
        }

        return Http::get(self::BASE_URL . '/TribunalRegionalTrabalho', $params)->json();
    }

    public function getProtests(string $document): array
    {
        return $this->executeServiceMethod('protests', [$document]);
    }

    protected function _getProtests(string $document): array
    {
        $params = [
            'TOKEN' => $this->token
        ];

        if (strlen($document) === 11) {
            $params['CPF'] = $document;
        } else {
            $params['CNPJ'] = $document;
        }

        return Http::get(self::BASE_URL . '/Protestos', $params)->json();
    }

    public function getReceitaFederal(string $document, ?string $birthdate = null): array
    {
        return $this->executeServiceMethod('receita-federal', [$document, $birthdate]);
    }

    protected function _saveCertificate(string $serviceKey, array $data, array $params): void
    {
        Certificate::create([
            'document' => $params[0],
            'service' => $serviceKey,
            'params' => json_encode($params ?? []),
            'status' => 'completed',
            'message' => isset($data['retorno']) ? $data['retorno']['observacoes'] : null,
            'result' => json_encode($data['retorno'] ?? $data),
            'region' => $params[1] ?? null,
            'url_certificate' => isset($data['site_receipts']) ? $data['site_receipts'][0] : null,
        ]);
    }

    protected function _getReceitaFederal(string $document, ?string $birthdate = null): array
    {
        return [];
    }

    public function getDebtCertificate(?string $document = null): array
    {
        return $this->executeServiceMethod('debt-certificate', [$document]);
    }

    protected function _getDebtCertificate(?string $document = null): array
    {
        return [];
    }

    public function getCNDT(string $document): array
    {
        return $this->executeServiceMethod('cndt', [$document]);
    }

    protected function _getCNDT(string $document): array
    {
        return [];
    }
}
