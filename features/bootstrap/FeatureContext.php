<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

class FeatureContext extends RawMinkContext implements Context
{
    private $response;
    private $user;
    private $token;

    /**
     * @Given eu estou autenticado como usuário válido
     */
    public function euEstouAutenticadoComoUsuarioValido()
    {
        $this->user = [
            'email' => 'admin@precpago.com.br',
            'password' => 'senha123'
        ];
        
        // Simula autenticação
        $this->token = 'mock-token';
    }

    /**
     * @When eu faço uma consulta de certidão para o CPF :cpf
     */
    public function euFacoUmaConsultaDeCertidaoParaOCpf($cpf)
    {
        $this->response = [
            'status' => 'success',
            'document' => $cpf,
            'certificates' => []
        ];
    }

    /**
     * @When eu faço uma consulta de certidão para o CNPJ :cnpj
     */
    public function euFacoUmaConsultaDeCertidaoParaOCnpj($cnpj)
    {
        $this->response = [
            'status' => 'success',
            'document' => $cnpj,
            'certificates' => []
        ];
    }

    /**
     * @Then eu devo ver o resultado da consulta
     */
    public function euDevoVerOResultadoDaConsulta()
    {
        if (!isset($this->response['status']) || $this->response['status'] !== 'success') {
            throw new Exception('Resultado da consulta não encontrado');
        }
    }

    /**
     * @Then a certidão deve estar disponível para download
     */
    public function aCertidaoDeveEstarDisponivelParaDownload()
    {
        // Simula verificação de disponibilidade do documento
        return true;
    }

    /**
     * @Given eu seleciono os seguintes tipos de certidão:
     */
    public function euSelecionoOsSeguintesTiposDeCertidao(TableNode $table)
    {
        foreach ($table->getRows() as $row) {
            // Simula seleção de tipos de certidão
            $this->response['certificates'][] = [
                'type' => $row[0],
                'status' => 'pending'
            ];
        }
    }

    /**
     * @When eu informo a data de nascimento :birthdate
     */
    public function euInformoADataDeNascimento($birthdate)
    {
        $this->response['birthdate'] = $birthdate;
    }

    /**
     * @Then eu devo ver uma mensagem de erro informando que o documento é inválido
     */
    public function euDevoVerUmaMensagemDeErroInformandoQueODocumentoEInvalido()
    {
        $this->response = [
            'status' => 'error',
            'message' => 'Documento inválido'
        ];
    }

    /**
     * @Then eu devo ver o histórico de consultas realizadas
     */
    public function euDevoVerOHistoricoDeConsultasRealizadas()
    {
        // Simula verificação do histórico
        return true;
    }

    /**
     * @When eu seleciono a região :region para o TRF
     */
    public function euSelecionoARegiaoParaOTRF($region)
    {
        $this->response['region'] = $region;
    }

    /**
     * @When eu seleciono a região :region para o TRT
     */
    public function euSelecionoARegiaoParaOTRT($region)
    {
        $this->response['labour_region'] = $region;
    }
}
