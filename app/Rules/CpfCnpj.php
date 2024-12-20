<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CpfCnpj implements Rule
{
    public function passes($attribute, $value)
    {
        return $this->validateCpf($value) || $this->validateCnpj($value);
    }

    public function message()
    {
        return 'O :attribute deve ser um CPF ou CNPJ válido.';
    }

    private function validateCpf($cpf)
    {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/\D/', '', $cpf);

        // Verifica se o CPF tem 11 dígitos
        if (strlen($cpf) != 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        // Validação do primeiro dígito verificador
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += ($cpf[$i] * (10 - $i));
        }
        $resto = $soma % 11;
        $digito1 = $resto < 2 ? 0 : 11 - $resto;

        if ($cpf[9] != $digito1) {
            return false;
        }

        // Validação do segundo dígito verificador
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += ($cpf[$i] * (11 - $i));
        }
        $resto = $soma % 11;
        $digito2 = $resto < 2 ? 0 : 11 - $resto;

        return $cpf[10] == $digito2;
    }

    private function validateCnpj($cnpj)
    {
        // Remove caracteres não numéricos
        $cnpj = preg_replace('/\D/', '', $cnpj);

        // Verifica se o CNPJ tem 14 dígitos
        if (strlen($cnpj) != 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        // Validação do primeiro dígito verificador
        $pesosPrimeiroDigito = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $soma = 0;
        for ($i = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $pesosPrimeiroDigito[$i];
        }
        $resto = $soma % 11;
        $digito1 = $resto < 2 ? 0 : 11 - $resto;

        if ($cnpj[12] != $digito1) {
            return false;
        }

        // Validação do segundo dígito verificador
        $pesosSegundoDigito = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $soma = 0;
        for ($i = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $pesosSegundoDigito[$i];
        }
        $resto = $soma % 11;
        $digito2 = $resto < 2 ? 0 : 11 - $resto;

        return $cnpj[13] == $digito2;
    }
}
