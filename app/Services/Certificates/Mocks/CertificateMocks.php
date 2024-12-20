<?php

namespace App\Services\Certificates\Mocks;

class CertificateMocks
{
    public static function getReceitaFederalMock(string $document, ?string $birthdate = null): array
    {
        if (strlen($document) === 14) { // CNPJ
            return [
                'code' => 200,
                'code_message' => "A requisição foi processada com sucesso.",
                'header' => [
                    'api_version' => 'v2',
                    'api_version_full' => '2.2.22-20241130094909',
                    'product' => 'Consultas',
                    'service' => 'receita-federal/cnpj',
                    'parameters' => [
                        'cnpj' => $document,
                    ],
                    'client_name' => 'Mock Client',
                    'token_name' => 'Mock',
                    'billable' => true,
                    'price' => '0.24',
                    'requested_at' => now()->format('Y-m-d\TH:i:s.000P'),
                    'elapsed_time_in_milliseconds' => 12221,
                    'remote_ip' => '127.0.0.1',
                    'signature' => 'mock_signature'
                ],
                'data_count' => 1,
                'data' => [
                    [
                        'abertura_data' => '19/11/2018',
                        'atividade_economica' => '62.01-5-01 - Desenvolvimento de programas de computador sob encomenda',
                        'cnpj' => preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $document),
                        'consulta_datahora' => now()->format('d/m/Y H:i:s'),
                        'nome_fantasia' => 'EMPRESA MOCK',
                        'razao_social' => 'EMPRESA MOCK LTDA',
                        'situacao_cadastral' => 'ATIVA',
                        'telefone' => '(11) 1234-5678',
                        'email' => 'contato@mock.com',
                        'endereco_logradouro' => 'Rua Mock',
                        'endereco_numero' => '123',
                        'endereco_complemento' => 'Sala 1',
                        'endereco_bairro' => 'Centro',
                        'endereco_municipio' => 'São Paulo',
                        'endereco_uf' => 'SP',
                        'endereco_cep' => '01234-567',
                        'site_receipt' => 'https://mock.com/receipt.pdf'
                    ]
                ],
                'errors' => [],
                'site_receipts' => [
                    'https://mock.com/receipt.pdf'
                ]
            ];
        } else { // CPF
            return [
                'code' => 200,
                'code_message' => "A requisição foi processada com sucesso.",
                'header' => [
                    'api_version' => 'v2',
                    'api_version_full' => '2.2.22-20241130094909',
                    'product' => 'Consultas',
                    'service' => 'receita-federal/cpf',
                    'parameters' => [
                        'birthdate' => $birthdate,
                        'cpf' => $document
                    ],
                    'client_name' => 'Mock Client',
                    'token_name' => 'Mock',
                    'billable' => true,
                    'price' => '0.24',
                    'requested_at' => now()->format('Y-m-d\TH:i:s.000P'),
                    'elapsed_time_in_milliseconds' => 1735,
                    'remote_ip' => '127.0.0.1',
                    'signature' => 'mock_signature'
                ],
                'data_count' => 1,
                'data' => [
                    [
                        'ano_obito' => null,
                        'consulta_comprovante' => 'MOCK123',
                        'consulta_datahora' => now()->format('d/m/Y H:i:s'),
                        'consulta_digito_verificador' => '00',
                        'cpf' => preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $document),
                        'data_inscricao' => '01/01/2000',
                        'data_nascimento' => $birthdate ?? '01/01/1990',
                        'nome' => 'PESSOA MOCK',
                        'nome_civil' => '',
                        'nome_social' => '',
                        'normalizado_ano_obito' => 0,
                        'normalizado_consulta_datahora' => now()->format('d/m/Y H:i:s'),
                        'normalizado_cpf' => $document,
                        'normalizado_data_inscricao' => '01/01/2000',
                        'normalizado_data_nascimento' => $birthdate ?? '01/01/1990',
                        'origem' => 'web',
                        'qrcode_url' => 'https://mock.com/qrcode',
                        'situacao_cadastral' => 'REGULAR',
                        'site_receipt' => 'https://mock.com/receipt.pdf'
                    ]
                ],
                'errors' => [],
                'site_receipts' => [
                    'https://mock.com/receipt.pdf'
                ]
            ];
        }
    }

    public static function getFederalCourtMock(string $document): array
    {
        return [
            'code' => 200,
            'code_message' => "A requisição foi processada com sucesso.",
            'header' => [
                'api_version' => 'v2',
                'api_version_full' => '2.2.22-20241130094909',
                'product' => 'Consultas',
                'service' => 'tribunal/trf3/certidao-distr',
                'parameters' => [
                    'abrangencia' => '1',
                    'documento' => $document,
                    'tipo' => '3'
                ],
                'client_name' => 'Mock Client',
                'token_name' => 'Mock',
                'billable' => true,
                'price' => '0.24',
                'requested_at' => now()->format('Y-m-d\TH:i:s.000P'),
                'elapsed_time_in_milliseconds' => 49751,
                'remote_ip' => '127.0.0.1',
                'signature' => 'mock_signature'
            ],
            'data_count' => 1,
            'data' => [
                [
                    'codigo_verificacao' => 'MOCK123',
                    'cpf_cnpj' => strlen($document) === 11 
                        ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $document)
                        : preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $document),
                    'dados_solicitacao' => [
                        'mensagem' => null,
                        'protocolo' => null,
                        'nome_solicitado' => null,
                        'documento_solicitado' => null,
                        'data_socilitacao' => null,
                        'nome_mae' => null,
                        'data_nascimento' => null,
                        'documento_adicional' => null,
                        'documento' => null,
                        'endereco' => null,
                        'telefone' => null
                    ],
                    'mensagem' => 'NÃO CONSTAM',
                    'nada_consta' => true,
                    'nome' => strlen($document) === 11 ? 'PESSOA MOCK' : 'EMPRESA MOCK LTDA',
                    'nome_social' => null,
                    'normalizado_cpf_cnpj' => $document,
                    'numero_certidao' => date('Y') . '/000000001',
                    'site_receipt' => 'https://mock.com/certidao.pdf'
                ]
            ],
            'errors' => [],
            'site_receipts' => [
                'https://mock.com/certidao.pdf'
            ]
        ];
    }

    public static function getProtestsMock(string $document): array
    {
        return [
            'code' => 612,
            'code_message' => "A consulta não retornou dados no site ou aplicativo de origem no qual a automação foi executada.",
            'header' => [
                'api_version' => 'v2',
                'api_version_full' => '2.2.22-20241201185305',
                'product' => 'Consultas',
                'service' => 'cenprot-sp/protestos',
                'parameters' => [
                    'documento' => $document
                ],
                'client_name' => 'Mock Client',
                'token_name' => 'Mock',
                'billable' => true,
                'price' => '0.26',
                'requested_at' => now()->format('Y-m-d\TH:i:s.000P'),
                'elapsed_time_in_milliseconds' => 2382,
                'remote_ip' => '127.0.0.1',
                'signature' => 'mock_signature'
            ],
            'data_count' => 0,
            'data' => [],
            'errors' => [
                "Não constam protestos nos cartórios participantes, cuja abrangência em SP é de 100%"
            ],
            'site_receipts' => [
                'https://mock.com/protestos.pdf'
            ]
        ];
    }

    public static function getCNDTMock(string $document): array
    {
        return [
            'code' => 200,
            'code_message' => "A requisição foi processada com sucesso.",
            'header' => [
                'api_version' => 'v2',
                'api_version_full' => '2.2.22-20241201185305',
                'product' => 'Consultas',
                'service' => 'tribunal/tst/cndt',
                'parameters' => [
                    'documento' => $document
                ],
                'client_name' => 'Mock Client',
                'token_name' => 'Mock',
                'billable' => true,
                'price' => '0.24',
                'requested_at' => now()->format('Y-m-d\TH:i:s.000P'),
                'elapsed_time_in_milliseconds' => 9983,
                'remote_ip' => '127.0.0.1',
                'signature' => 'mock_signature'
            ],
            'data_count' => 1,
            'data' => [
                [
                    'certidao' => date('Y') . '/000001',
                    'certidao_codigo' => date('Y') . '/000001',
                    'cnpj' => strlen($document) === 14 ? preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $document) : '',
                    'cpf' => strlen($document) === 11 ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $document) : '',
                    'conseguiu_emitir_certidao_negativa' => true,
                    'consta' => false,
                    'emissao_data' => now()->format('d/m/Y'),
                    'expedicao' => now()->format('d/m/Y, \à\s H:i:s'),
                    'mensagem' => 'CERTIDÃO NEGATIVA DE DÉBITOS TRABALHISTAS',
                    'nome' => strlen($document) === 11 ? 'PESSOA MOCK' : 'EMPRESA MOCK LTDA',
                    'normalizado_cnpj' => strlen($document) === 14 ? $document : '',
                    'normalizado_cpf' => strlen($document) === 11 ? $document : '',
                    'normalizado_expedicao' => now()->format('d/m/Y H:i:s'),
                    'normalizado_validade' => now()->addMonths(6)->format('d/m/Y'),
                    'processos_encontrados' => [],
                    'total_de_processos' => '',
                    'validade' => now()->addMonths(6)->format('d/m/Y'),
                    'validade_data' => now()->addMonths(6)->format('d/m/Y'),
                    'site_receipt' => 'https://mock.com/cndt.pdf'
                ]
            ],
            'errors' => [],
            'site_receipts' => [
                'https://mock.com/cndt.pdf'
            ]
        ];
    }

    public static function getDebtCertificateMock(string $document): array
    {
        return [
            'code' => 200,
            'code_message' => "A requisição foi processada com sucesso.",
            'header' => [
                'api_version' => 'v2',
                'api_version_full' => '2.2.23-20241201191413',
                'product' => 'Consultas',
                'service' => 'receita-federal/pgfn/nova',
                'parameters' => [
                    'documento' => $document
                ],
                'client_name' => 'Mock Client',
                'token_name' => 'Mock',
                'billable' => true,
                'price' => '0.26',
                'requested_at' => now()->format('Y-m-d\TH:i:s.000P'),
                'elapsed_time_in_milliseconds' => 17409,
                'remote_ip' => '127.0.0.1',
                'signature' => 'mock_signature'
            ],
            'data_count' => 1,
            'data' => [
                [
                    'certidao' => 'CERTIDÃO NEGATIVA DE DÉBITOS RELATIVOS AOS TRIBUTOS FEDERAIS E À DÍVIDA ATIVA DA UNIÃO',
                    'certidao_codigo' => 'MOCK123',
                    'cnpj' => strlen($document) === 14 ? preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $document) : null,
                    'cpf' => strlen($document) === 11 ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $document) : null,
                    'cnpj_situacao' => null,
                    'comprovante_tipo' => 'pdf',
                    'conseguiu_emitir_certidao_negativa' => true,
                    'consulta_comprovante' => 'MOCK123',
                    'consulta_datahora' => now()->format('d/m/Y H:i:s'),
                    'debitos_pgfn' => false,
                    'debitos_rfb' => false,
                    'descricao' => null,
                    'emissao_data' => now()->format('d/m/Y'),
                    'mensagem' => 'CERTIDÃO NEGATIVA DE DÉBITOS RELATIVOS AOS TRIBUTOS FEDERAIS E À DÍVIDA ATIVA DA UNIÃO',
                    'nome' => strlen($document) === 11 ? 'PESSOA MOCK' : 'EMPRESA MOCK LTDA',
                    'normalizado_cnpj' => strlen($document) === 14 ? $document : '',
                    'normalizado_consulta_datahora' => now()->format('d/m/Y H:i:s'),
                    'normalizado_cpf' => strlen($document) === 11 ? $document : '',
                    'observacoes' => null,
                    'razao_social' => strlen($document) === 11 ? 'PESSOA MOCK' : 'EMPRESA MOCK LTDA',
                    'situacao' => null,
                    'tipo' => null,
                    'validade' => now()->addMonths(6)->format('d/m/Y'),
                    'validade_data' => now()->addMonths(6)->format('d/m/Y'),
                    'validade_prorrogada' => null,
                    'site_receipt' => 'https://mock.com/debt.pdf'
                ]
            ],
            'errors' => [],
            'site_receipts' => [
                'https://mock.com/debt.pdf'
            ]
        ];
    }
}
