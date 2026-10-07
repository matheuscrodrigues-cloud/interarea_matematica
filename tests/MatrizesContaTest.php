<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Controller/MatrizesController.php';

class MatrizesContaTest extends TestCase
{
    private MatrizesController $contas;

    protected function setUp(): void
    {
        $this->contas = new MatrizesController();
    }

    private function perto(array $esperada, array $real, float $tol = 0.0001): void
    {
        $this->assertCount(count($esperada), $real);
        foreach ($esperada as $i => $linha) {
            foreach ($linha as $j => $valor) {
                $this->assertEqualsWithDelta($valor, $real[$i][$j], $tol);
            }
        }
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function soma_subtracao_basicas(): void
    {
        $this->perto([[6, 8], [10, 12]], $this->contas->somar([[1, 2], [3, 4]], [[5, 6], [7, 8]]));
        $this->perto([[0, 1], [1, 0]], $this->contas->subtrair([[1, 2], [3, 4]], [[1, 1], [2, 4]]));
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function produto_2x2(): void
    {
        $this->perto([[19, 22], [43, 50]], $this->contas->multiplicar([[1, 2], [3, 4]], [[5, 6], [7, 8]]));
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function escalar_e_transposta(): void
    {
        $this->perto([[3, 6], [9, 12]], $this->contas->porEscalar([[1, 2], [3, 4]], 3.0));
        $this->perto([[1, 4], [2, 5], [3, 6]], $this->contas->transpor([[1, 2, 3], [4, 5, 6]]));
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function identidade_neutra_e_zerada_neutra(): void
    {
        $a = [[2, 5], [7, 1]];
        $this->perto($a, $this->contas->multiplicar($a, $this->contas->identidade(2)));
        $this->perto($a, $this->contas->somar($a, $this->contas->zerada(2, 2)));
        $this->perto([[1, 0, 0], [0, 1, 0], [0, 0, 1]], $this->contas->identidade(3));
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function determinantes_1x1_2x2_3x3(): void
    {
        $this->assertEqualsWithDelta(9.0, $this->contas->determinante([[9]]), 0.0001);
        $this->assertEqualsWithDelta(-2.0, $this->contas->determinante([[1, 2], [3, 4]]), 0.0001);
        $this->assertEqualsWithDelta(0.0, $this->contas->determinante([[2, 4], [1, 2]]), 0.0001);
        $this->assertEqualsWithDelta(
            1.0,
            $this->contas->determinante([[1, 2, 3], [0, 1, 4], [5, 6, 0]]),
            0.0001,
        );
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function inversa_confere_na_multiplicacao(): void
    {
        $a = [[4, 7], [2, 6]];
        $inv = $this->contas->inversa($a);
        $this->perto([[0.6, -0.7], [-0.2, 0.4]], $inv);
        $this->perto($this->contas->identidade(2), $this->contas->multiplicar($a, $inv));
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function tamanhos_diferentes_nao_somam(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->contas->somar([[1, 2], [3, 4]], [[1, 2, 3]]);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function produto_incompativel_falha(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->contas->multiplicar([[1, 2]], [[1, 2], [3, 4], [5, 6]]);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function singular_nao_tem_inversa(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->contas->inversa([[3, 6], [1, 2]]);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function negativos_e_decimais(): void
    {
        $this->perto([[-0.5, 2.5]], $this->contas->somar([[-1.5, 1.0]], [[1.0, 1.5]]));
        $this->assertEqualsWithDelta(2.5, $this->contas->determinante([[1.5, 0.5], [1.0, 2.0]]), 0.0001);
    }
}
