<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../Controller/PaginaAlgebraController.php';
final class PaginaAlgebraTest extends TestCase
{
    public function testNavegacaoConfiguracaoESeguranca(): void
    {
        $controle = new PaginaAlgebraController();
        $pagina = $controle->preparar(
            ['atividade' => 'inexistente', 'ordem' => '999', 'operacao' => []],
            [],
            'GET',
        );
        self::assertSame('matrizes', $pagina['atividade']);
        self::assertSame(2, $pagina['ordem']);
        self::assertSame('soma', $pagina['operacao']);
        self::assertSame(3, $controle->preparar(['ordem' => '3'], [], 'GET')['ordem']);
        self::assertSame(
            'sistemas',
            $controle->preparar(['atividade' => 'sistemas'], [], 'GET')['atividade'],
        );
    }
    public function testOperacaoUnariaNaoExigeMatrizDireita(): void
    {
        $pagina = new PaginaAlgebraController()->preparar(
            ['operacao' => 'determinante'],
            ['matrizEsquerda' => [[1, 2], [3, 4]]],
            'POST',
        );
        self::assertSame([], $pagina['erros']);
        self::assertFalse($pagina['usaDireita']);
        self::assertEquals(-2, $pagina['resultado']['dados']);
    }
    public function testSomaTresPorTres(): void
    {
        $identidade = [[1, 0, 0], [0, 1, 0], [0, 0, 1]];
        $pagina = new PaginaAlgebraController()->preparar(
            ['ordem' => '3'],
            ['matrizEsquerda' => $identidade, 'matrizDireita' => $identidade],
            'POST',
        );
        self::assertSame([], $pagina['erros']);
        self::assertEquals([[2, 0, 0], [0, 2, 0], [0, 0, 2]], $pagina['resultado']['dados']);
    }
    public function testEscalarETransposta(): void
    {
        $controle = new PaginaAlgebraController();
        $entrada = ['matrizEsquerda' => [[1, 2], [3, 4]], 'escalar' => '-2'];
        self::assertEquals(
            [[-2, -4], [-6, -8]],
            $controle->preparar(['operacao' => 'escalar'], $entrada, 'POST')['resultado']['dados'],
        );
        self::assertEquals(
            [[1, 3], [2, 4]],
            $controle->preparar(['operacao' => 'transposta'], $entrada, 'POST')['resultado']['dados'],
        );
    }
    public function testGeradoresNaoExigemValores(): void
    {
        $controle = new PaginaAlgebraController();
        self::assertEquals(
            [[1, 0], [0, 1]],
            $controle->preparar(['operacao' => 'identidade'], [], 'POST')['resultado']['dados'],
        );
        self::assertEquals(
            [[0, 0], [0, 0]],
            $controle->preparar(['operacao' => 'nula'], [], 'POST')['resultado']['dados'],
        );
    }
    public function testSingularEValoresInvalidosPreservados(): void
    {
        $controle = new PaginaAlgebraController();
        $pagina = $controle->preparar(
            ['operacao' => 'inversa'],
            ['matrizEsquerda' => [[1, 2], [2, 4]]],
            'POST',
        );
        self::assertArrayHasKey('calculo', $pagina['erros']);
        self::assertNull($pagina['resultado']);
        $pagina = $controle->preparar(
            ['operacao' => 'determinante'],
            ['matrizEsquerda' => [['abc', '1e999'], [[], 4]]],
            'POST',
        );
        self::assertCount(3, $pagina['erros']);
        self::assertSame('abc', $pagina['valores']['matrizEsquerda.0.0']);
    }
    public function testClassificaOsTresTiposDeSistema(): void
    {
        $controle = new PaginaAlgebraController();
        $consulta = ['atividade' => 'sistemas'];
        foreach (
            [
                ['coeficientes' => [[0, 1], [1, 1]], 'termosIndependentes' => [2, 3], 'tipo' => 'SPD'],
                ['coeficientes' => [[1, 1], [2, 2]], 'termosIndependentes' => [2, 4], 'tipo' => 'SPI'],
                ['coeficientes' => [[1, 1], [2, 2]], 'termosIndependentes' => [2, 5], 'tipo' => 'SI'],
            ]
            as $caso
        ) {
            $pagina = $controle->preparar($consulta, $caso, 'POST');
            self::assertSame($caso['tipo'], $pagina['resultado']['dados']['tipo']);
        }
    }
    public function testSistemaTresPorTres(): void
    {
        $pagina = new PaginaAlgebraController()->preparar(
            ['atividade' => 'sistemas', 'ordem' => '3'],
            ['coeficientes' => [[1, 0, 0], [0, 1, 0], [0, 0, 1]], 'termosIndependentes' => [1, 2, 3]],
            'POST',
        );
        self::assertSame('SPD', $pagina['resultado']['dados']['tipo']);
        self::assertEquals([1, 2, 3], $pagina['resultado']['dados']['solucao']);
    }
}
