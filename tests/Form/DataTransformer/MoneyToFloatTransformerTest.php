<?php

namespace App\Tests\Form\DataTransformer;

use App\Form\DataTransformer\MoneyToFloatTransformer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MoneyToFloatTransformerTest extends TestCase
{
    private MoneyToFloatTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new MoneyToFloatTransformer();
    }

    // Testar Float para String | Do Banco para o Form
    #[DataProvider('floatToStringProvider')]
    public function testTransformFloatToString(?float $input, string $expected): void
    {
        $this->assertSame($expected, $this->transformer->transform($input));
    }

    /**
     * @return array<string, array{float|null, string}>
     */
    public static function floatToStringProvider(): array
    {
        return [
            'Null deve retornar string vazia' => [null, ''],
            'Float formatado para o padrão BR' => [1234.56, '1.234,56'],
            'Zero formatado com duas casas' => [0.0, '0,00'],
            'Valor menor que um' => [0.99, '0,99'],
            'Valor grande com separador de milhar' => [1000000.0, '1.000.000,00'],
        ];
    }

    // Testar String para Float | Do Form para o Banco
    #[DataProvider('stringToFloatProvider')]
    public function testReverseTransformStringToFloat(?string $input, ?float $expected): void
    {
        $this->assertSame($expected, $this->transformer->reverseTransform($input));
    }

    /**
     * @return array<string, array{string|null, float|null}>
     */
    public static function stringToFloatProvider(): array
    {
        return [
            'Null deve retornar null' => [null, null],
            'String vazia deve retornar null' => ['', null],
            'Valor no formato brasileiro' => ['1.234,56', 1234.56],
            'Valor sem separador de milhar' => ['59,90', 59.9],
            'Valor com símbolo de moeda' => ['R$ 59,90', 59.9],
            'Valor com espaços em branco' => [' 1.234,56 ', 1234.56],
            'Valor inteiro' => ['100', 100.0],
            'Valor grande com separador de milhar' => ['1.000.000,00', 1000000.0],
            'String inválida deve retornar null' => ['abc', null],
            'Apenas símbolo de moeda deve retornar null' => ['R$ ', null],
        ];
    }
}
