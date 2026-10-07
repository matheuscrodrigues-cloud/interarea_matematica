<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Controller/SistemasController.php';

class SistemasTipoTest extends TestCase
{
    private SistemasController $sistemas;

    protected function setUp(): void
    {
        $this->sistemas = new SistemasController();
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function spd_tem_uma_solucao(): void
    {
        $r = $this->sistemas->resolver([[2, 3], [1, -1]], [8, 1]);

        $this->assertSame('SPD', $r['tipo']);
        $this->assertEqualsWithDelta(2.2, $r['solucao'][0], 0.0001);
        $this->assertEqualsWithDelta(1.2, $r['solucao'][1], 0.0001);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function spd_3x3_classico(): void
    {
        $r = $this->sistemas->resolver([[1, 1, 1], [2, 1, 3], [1, 2, 1]], [6, 13, 8]);

        $this->assertSame('SPD', $r['tipo']);
        $this->assertEqualsWithDelta(1.0, $r['solucao'][0], 0.0001);
        $this->assertEqualsWithDelta(2.0, $r['solucao'][1], 0.0001);
        $this->assertEqualsWithDelta(3.0, $r['solucao'][2], 0.0001);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function si_nao_tem_solucao(): void
    {
        $r = $this->sistemas->resolver([[1, 2], [2, 4]], [5, 11]);

        $this->assertSame('SI', $r['tipo']);
        $this->assertNull($r['solucao']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function spi_tem_infinitas(): void
    {
        $r = $this->sistemas->resolver([[1, -1], [3, -3]], [4, 12]);

        $this->assertSame('SPI', $r['tipo']);
        $this->assertNull($r['solucao']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function troca_de_linha_quando_pivo_e_zero(): void
    {
        $r = $this->sistemas->resolver([[0, 2], [3, 1]], [6, 8]);

        $this->assertSame('SPD', $r['tipo']);
        $this->assertEqualsWithDelta(5 / 3, $r['solucao'][0], 0.0001);
        $this->assertEqualsWithDelta(3.0, $r['solucao'][1], 0.0001);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function rotulos_usam_siglas_do_professor(): void
    {
        $this->assertStringContainsString('SPD', $this->sistemas->rotulo('SPD'));
        $this->assertStringContainsString('SPI', $this->sistemas->rotulo('SPI'));
        $this->assertStringContainsString('SI', $this->sistemas->rotulo('SI'));
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function matriz_torta_e_rejeitada(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->sistemas->resolver([[1, 2, 3], [4, 5, 6]], [1, 2]);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function letra_no_lugar_de_numero_e_rejeitada(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->sistemas->resolver([['x', 1], [2, 3]], [1, 2]);
    }
}
