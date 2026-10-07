<?php

declare(strict_types=1);

class MatrizesController
{
    public function tamanho(array $matriz): array
    {
        if ($matriz === [] || !is_array($matriz[0])) {
            throw new InvalidArgumentException('Matriz vazia.');
        }
        $colunas = count($matriz[0]);
        foreach ($matriz as $linha) {
            if (!is_array($linha) || count($linha) !== $colunas) {
                throw new InvalidArgumentException(
                    'Todas as linhas devem ter a mesma quantidade de colunas.',
                );
            }
            foreach ($linha as $valor) {
                if (!is_numeric($valor)) {
                    throw new InvalidArgumentException('A matriz só aceita números.');
                }
            }
        }
        return [count($matriz), $colunas];
    }

    public function somar(array $matrizEsquerda, array $matrizDireita): array
    {
        [$linhasEsquerda, $colunasEsquerda] = $this->tamanho($matrizEsquerda);
        [$linhasDireita, $colunasDireita] = $this->tamanho($matrizDireita);
        if ($linhasEsquerda !== $linhasDireita || $colunasEsquerda !== $colunasDireita) {
            throw new InvalidArgumentException(
                "Soma exige matrizes do mesmo tamanho ({$linhasEsquerda}x{$colunasEsquerda} e {$linhasDireita}x{$colunasDireita}).",
            );
        }
        $saida = [];
        for ($i = 0; $i < $linhasEsquerda; $i++) {
            for ($j = 0; $j < $colunasEsquerda; $j++) {
                $saida[$i][$j] = $matrizEsquerda[$i][$j] + $matrizDireita[$i][$j];
            }
        }
        return $saida;
    }

    public function subtrair(array $matrizEsquerda, array $matrizDireita): array
    {
        [$linhasEsquerda, $colunasEsquerda] = $this->tamanho($matrizEsquerda);
        [$linhasDireita, $colunasDireita] = $this->tamanho($matrizDireita);
        if ($linhasEsquerda !== $linhasDireita || $colunasEsquerda !== $colunasDireita) {
            throw new InvalidArgumentException(
                "Subtração exige matrizes do mesmo tamanho ({$linhasEsquerda}x{$colunasEsquerda} e {$linhasDireita}x{$colunasDireita}).",
            );
        }
        $saida = [];
        for ($i = 0; $i < $linhasEsquerda; $i++) {
            for ($j = 0; $j < $colunasEsquerda; $j++) {
                $saida[$i][$j] = $matrizEsquerda[$i][$j] - $matrizDireita[$i][$j];
            }
        }
        return $saida;
    }

    public function multiplicar(array $matrizEsquerda, array $matrizDireita): array
    {
        [$linhasEsquerda, $colunasEsquerda] = $this->tamanho($matrizEsquerda);
        [$linhasDireita, $colunasDireita] = $this->tamanho($matrizDireita);
        if ($colunasEsquerda !== $linhasDireita) {
            throw new InvalidArgumentException(
                "Para multiplicar, as colunas de A ({$colunasEsquerda}) devem igualar as linhas de B ({$linhasDireita}).",
            );
        }
        $saida = [];
        for ($i = 0; $i < $linhasEsquerda; $i++) {
            for ($j = 0; $j < $colunasDireita; $j++) {
                $total = 0;
                for ($multiplicador = 0; $multiplicador < $colunasEsquerda; $multiplicador++) {
                    $total += $matrizEsquerda[$i][$multiplicador] * $matrizDireita[$multiplicador][$j];
                }
                $saida[$i][$j] = $total;
            }
        }
        return $saida;
    }

    public function porEscalar(array $matriz, float $multiplicador): array
    {
        [$linhas, $colunas] = $this->tamanho($matriz);
        $saida = [];
        for ($i = 0; $i < $linhas; $i++) {
            for ($j = 0; $j < $colunas; $j++) {
                $saida[$i][$j] = $matriz[$i][$j] * $multiplicador;
            }
        }
        return $saida;
    }

    public function transpor(array $matriz): array
    {
        [$linhas, $colunas] = $this->tamanho($matriz);
        $saida = [];
        for ($j = 0; $j < $colunas; $j++) {
            for ($i = 0; $i < $linhas; $i++) {
                $saida[$j][$i] = $matriz[$i][$j];
            }
        }
        return $saida;
    }

    public function identidade(int $ordem): array
    {
        if ($ordem < 1) {
            throw new InvalidArgumentException('A ordem da identidade começa em 1.');
        }
        $saida = [];
        for ($i = 0; $i < $ordem; $i++) {
            for ($j = 0; $j < $ordem; $j++) {
                $saida[$i][$j] = $i === $j ? 1 : 0;
            }
        }
        return $saida;
    }

    public function zerada(int $linhas, int $colunas): array
    {
        if ($linhas < 1 || $colunas < 1) {
            throw new InvalidArgumentException('Informe linhas e colunas maiores que zero.');
        }
        $saida = [];
        for ($i = 0; $i < $linhas; $i++) {
            for ($j = 0; $j < $colunas; $j++) {
                $saida[$i][$j] = 0;
            }
        }
        return $saida;
    }

    public function quadrada(array $matriz): int
    {
        [$linhas, $colunas] = $this->tamanho($matriz);
        if ($linhas !== $colunas) {
            throw new InvalidArgumentException('Esta conta só vale para matriz quadrada.');
        }
        return $linhas;
    }

    public function determinante(array $matriz): float
    {
        $ordem = $this->quadrada($matriz);
        if ($ordem === 1) {
            return (float) $matriz[0][0];
        }
        if ($ordem === 2) {
            return (float) ($matriz[0][0] * $matriz[1][1] - $matriz[0][1] * $matriz[1][0]);
        }
        $determinante = 0.0;
        foreach ($matriz[0] as $j => $valor) {
            $sinal = $j % 2 === 0 ? 1 : -1;
            $determinante += $sinal * $valor * $this->determinante($this->menor($matriz, 0, $j));
        }
        return $determinante;
    }

    public function inversa(array $matriz): array
    {
        $ordem = $this->quadrada($matriz);
        $determinante = $this->determinante($matriz);
        if (abs($determinante) < 1e-12) {
            throw new InvalidArgumentException('Sem inversa: determinante igual a zero (matriz singular).');
        }
        if ($ordem === 1) {
            return [[1 / $matriz[0][0]]];
        }
        $adjunta = [];
        for ($i = 0; $i < $ordem; $i++) {
            for ($j = 0; $j < $ordem; $j++) {
                $sinal = ($i + $j) % 2 === 0 ? 1 : -1;
                $adjunta[$j][$i] = $sinal * $this->determinante($this->menor($matriz, $i, $j));
            }
        }
        $saida = [];
        for ($i = 0; $i < $ordem; $i++) {
            for ($j = 0; $j < $ordem; $j++) {
                $saida[$i][$j] = $adjunta[$i][$j] / $determinante;
            }
        }
        return $saida;
    }

    private function menor(array $matriz, int $foraLinha, int $foraColuna): array
    {
        $saida = [];
        foreach ($matriz as $i => $linha) {
            if ($i === $foraLinha) {
                continue;
            }
            $linhaMenor = [];
            foreach ($linha as $j => $valor) {
                if ($j === $foraColuna) {
                    continue;
                }
                $linhaMenor[] = $valor;
            }
            $saida[] = $linhaMenor;
        }
        return $saida;
    }
}
