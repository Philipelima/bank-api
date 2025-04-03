<?php 
declare(strict_types=1);

namespace App\Validators;

class DocumentValidator
{
    private string $document;

    public function setDocument(string $document)
    {
        $this->document = $document;
    }

    public function isValidCpf(): bool
    {
        $cpf = preg_replace('/[^0-9]/', '', $this->document); // Remove caracteres não numéricos
        if (strlen($cpf) !== 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        for ($t = 9; $t < 11; $t++) {
            $digit = 0;
            for ($c = 0; $c < $t; $c++) {
                $digit += $cpf[$c] * (($t + 1) - $c);
            }
            $digit = ((10 * $digit) % 11) % 10;
            if ($cpf[$c] != $digit) {
                return false;
            }
        }
        return true;
    }

    public function isValidCnpj(): bool
    {
        $cnpj = preg_replace('/[^0-9]/', '', $this->document);
        if (strlen($cnpj) !== 14) {
            return false;
        }
        $multipliers = [
            5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2, 6, 5, 4, 3, 2
        ];
        for ($t = 12; $t < 14; $t++) {
            $digit = 0;
            for ($c = 0; $c < $t; $c++) {
                $digit += $cnpj[$c] * $multipliers[$c + (14 - $t)];
            }
            $digit = ((10 * $digit) % 11) % 10;
            if ($cnpj[$c] != $digit) {
                return false;
            }
        }
        return true;
    }
}
