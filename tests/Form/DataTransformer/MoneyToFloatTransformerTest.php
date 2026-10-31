<?php

namespace App\Tests\Form\DataTransformer;

use App\Form\DataTransformer\MoneyToFloatTransformer;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class MoneyToFloatTransformerTest extends TestCase
{
    private MoneyToFloatTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new MoneyToFloatTransformer();
    }

    #[DataProvider('floatToStringProvider')]
    public function testTransformFloatToString(?float $input, string $expected): void
    {
        $this->assertSame($expected, $this->transformer->transform($input));
    }

    public static function floatToStringProvider(): array
    {
        return [
            'Null deve retornar string vazia' => [null, ''],
            'Float formatado para o padrão BR' => [1234.56, '1.234,56'],
            'Zero formatado com duas casas'   => [0.0, '0,00'],
            'Valor menor que um'              => [0.99, '0,99'],
        ];
    }

    // Testar Float para String | Do Banco para o Form
    /*public function testTransformNullReturnsEmptyString(): void
    {
        $this->assertSame('', $this->transformer->transform(null));
    }

    public function testTransformFloatToBrazilianFormat(): void
    {
        $this->assertSame('1.234,56', $this->transformer->transform(1234.56));
    }

    public function testTransformZero(): void
    {
        $this->assertSame('0,00', $this->transformer->transform(0));
    }

    public function testTransformSmallValue(): void
    {
        $this->assertSame('0,99', $this->transformer->transform(0.99));
    }

    public function testTransformLargeValue(): void
    {
        $this->assertSame('1.000.000,00', $this->transformer->transform(1000000));
    }*/

    // Testar String para Float | Do Form para o Banco
    public function testReverseTransformNullReturnsNull(): void
    {
        $this->assertNull($this->transformer->reverseTransform(null));
    }

    public function testReverseTransformEmptyStringReturnsNull(): void
    {
        $this->assertNull($this->transformer->reverseTransform(''));
    }

    public function testReverseTransformBrazilianFormat(): void
    {
        $this->assertSame(1234.56, $this->transformer->reverseTransform('1.234,56'));
    }

    public function testReverseTransformWithoutThousands(): void
    {
        $this->assertSame(59.9, $this->transformer->reverseTransform('59,90'));
    }

    public function testReverseTransformWithCurrencySymbol(): void
    {
        $this->assertSame(59.9, $this->transformer->reverseTransform('R$ 59,90'));
    }

    public function testReverseTransformWithSpaces(): void
    {
        $this->assertSame(1234.56, $this->transformer->reverseTransform(' 1.234,56 '));
    }

    public function testReverseTransformIntegerValue(): void
    {
        $this->assertSame(100.0, $this->transformer->reverseTransform('100'));
    }

    public function testReverseTransformLargeValue(): void
    {
        $this->assertSame(1000000.0, $this->transformer->reverseTransform('1.000.000,00'));
    }

    public function testReverseTransformInvalidStringReturnsNull(): void
    {
        $this->assertNull($this->transformer->reverseTransform('abc'));
    }

    public function testReverseTransformOnlySymbolsReturnsNull(): void
    {
        $this->assertNull($this->transformer->reverseTransform('R$ '));
    }

}