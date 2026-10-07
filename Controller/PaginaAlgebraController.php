<?php
declare(strict_types=1);
require_once __DIR__ . '/MatrizesController.php';
require_once __DIR__ . '/SistemasController.php';

final class PaginaAlgebraController
{
    private const OPERACOES = [
        'soma' => 'Soma',
        'subtracao' => 'Subtração',
        'produto' => 'Multiplicação',
        'escalar' => 'Multiplicação por escalar',
        'transposta' => 'Transposta',
        'determinante' => 'Determinante',
        'inversa' => 'Inversa',
        'identidade' => 'Matriz identidade',
        'nula' => 'Matriz nula',
    ];

    public function preparar(array $consulta, array $entrada, string $metodo): array
    {
        $atividade = ($consulta['atividade'] ?? '') === 'sistemas' ? 'sistemas' : 'matrizes';
        $ordem = in_array($consulta['ordem'] ?? null, ['3', 3], true) ? 3 : 2;
        $operacao =
            is_string($consulta['operacao'] ?? null) && isset(self::OPERACOES[$consulta['operacao']])
            ? $consulta['operacao']
            : 'soma';
        $pagina = [
            'atividade' => $atividade,
            'ordem' => $ordem,
            'operacao' => $operacao,
            'operacoes' => self::OPERACOES,
            'valores' => [],
            'erros' => [],
            'resultado' => null,
            'usaDireita' => in_array($operacao, ['soma', 'subtracao', 'produto'], true),
            'usaEsquerda' => !in_array($operacao, ['identidade', 'nula'], true),
        ];
        if ($metodo !== 'POST') {
            return $pagina;
        }
        if ($atividade === 'sistemas') {
            $coeficientes = $this->lerMatriz($entrada, 'coeficientes', $ordem, $pagina);
            $termosIndependentes = [];
            for ($linha = 0; $linha < $ordem; $linha++) {
                $termosIndependentes[] = $this->lerNumero($entrada, ['termosIndependentes', $linha], $pagina);
            }
            if ($pagina['erros'] !== []) {
                return $pagina;
            }
            $pagina['resultado'] = [
                'tipo' => 'sistema',
                'dados' => new SistemasController()->resolver($coeficientes, $termosIndependentes),
            ];
            return $pagina;
        }
        $matrizEsquerda = $pagina['usaEsquerda']
            ? $this->lerMatriz($entrada, 'matrizEsquerda', $ordem, $pagina)
            : [];
        $matrizDireita = $pagina['usaDireita']
            ? $this->lerMatriz($entrada, 'matrizDireita', $ordem, $pagina)
            : [];
        $escalar = $operacao === 'escalar' ? $this->lerNumero($entrada, ['escalar'], $pagina) : 0;
        if ($pagina['erros'] !== []) {
            return $pagina;
        }
        $calculos = new MatrizesController();
        try {
            $resposta = match ($operacao) {
                'soma' => $calculos->somar($matrizEsquerda, $matrizDireita),
                'subtracao' => $calculos->subtrair($matrizEsquerda, $matrizDireita),
                'produto' => $calculos->multiplicar($matrizEsquerda, $matrizDireita),
                'escalar' => $calculos->porEscalar($matrizEsquerda, $escalar),
                'transposta' => $calculos->transpor($matrizEsquerda),
                'determinante' => $calculos->determinante($matrizEsquerda),
                'inversa' => $calculos->inversa($matrizEsquerda),
                'identidade' => $calculos->identidade($ordem),
                'nula' => $calculos->zerada($ordem, $ordem),
            };
            $pagina['resultado'] = [
                'tipo' => is_array($resposta) ? 'matriz' : 'numero',
                'dados' => $resposta,
            ];
        } catch (InvalidArgumentException $erro) {
            $pagina['erros']['calculo'] = $erro->getMessage();
        }
        return $pagina;
    }

    private function lerMatriz(array $entrada, string $nome, int $ordem, array &$pagina): array
    {
        $matriz = [];
        for ($linha = 0; $linha < $ordem; $linha++) {
            for ($coluna = 0; $coluna < $ordem; $coluna++) {
                $matriz[$linha][$coluna] = $this->lerNumero($entrada, [$nome, $linha, $coluna], $pagina);
            }
        }
        return $matriz;
    }

    private function lerNumero(array $entrada, array $caminho, array &$pagina): float
    {
        $valor = $entrada;
        foreach ($caminho as $parte) {
            $valor = is_array($valor) ? $valor[$parte] ?? '' : '';
        }
        $chave = implode('.', $caminho);
        $pagina['valores'][$chave] = is_scalar($valor) ? (string) $valor : '';
        if (
            !is_scalar($valor) ||
            trim((string) $valor) === '' ||
            !is_numeric($valor) ||
            !is_finite((float) $valor)
        ) {
            $pagina['erros'][$chave] = 'Preencha com um número válido.';
            return 0;
        }
        return (float) $valor;
    }
}
