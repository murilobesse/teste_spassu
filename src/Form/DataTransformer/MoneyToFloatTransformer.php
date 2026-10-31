<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class MoneyToFloatTransformer implements DataTransformerInterface
{
    /**
     * Converter o valor do Banco (Float) para o Formulário (String com vírgula)
     */
    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }

        return number_format((float) $value, 2, ',', '.');
    }

    /**
     * Converter o valor do Formulário (String com vírgula) para o Banco (Float)
     */
    public function reverseTransform($value): ?float
    {
        if (null === $value || '' === $value) {
            return null;
        }

        $value = preg_replace('/[^0-9,\.]/', '', $value);
        
        if (empty($value)) {
            return null;
        }

        if (strpos($value, ',') !== false) {
            $value = str_replace('.', '', $value); // Remove pontos de milhar
            $value = str_replace(',', '.', $value); // Troca vírgula por ponto
        }

        return is_numeric($value) ? (float) $value : null;
    }
}