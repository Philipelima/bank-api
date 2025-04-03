<?php 
declare(strict_types=1);

namespace App\Validators;

class DocumentValidator
{
    public static function isValidCpf(string $cpf): bool
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf); // Remove caracteres não numéricos
        if (strlen($cpf) !== 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    public static function isValidCnpj(string $cnpj): bool
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        if (strlen($cnpj) !== 14) {
            return false;
        }
        $multiplicadores = [
            5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2, 6, 5, 4, 3, 2
        ];
        for ($t = 12; $t < 14; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cnpj[$c] * $multiplicadores[$c + (14 - $t)];
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cnpj[$c] != $d) {
                return false;
            }
        }
        return true;
    }
}
