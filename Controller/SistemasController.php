<?php

declare(strict_types=1);

class SistemasController
{
    public function resolver(array $coeficientes, array $termosIndependentes): array
    {
        $ordem = $this->conferir($coeficientes, $termosIndependentes);

        $aumentada = [];
        for ($i = 0; $i < $ordem; $i++) {
            $aumentada[$i] = array_values($coeficientes[$i]);
            $aumentada[$i][] = (float) $termosIndependentes[$i];
        }

        $escada = $this->triangularizar($aumentada, $ordem);
        $classificacao = $this->classificar($escada, $ordem);
        if ($classificacao !== 'SPD') {
            return ['tipo' => $classificacao, 'solucao' => null];
        }
        return ['tipo' => 'SPD', 'solucao' => $this->substituir($escada, $ordem)];
    }

    public function rotulo(string $tipo): string
    {
        return match ($tipo) {
            'SPD' => 'SPD — Sistema Possível Determinado (solução única).',
            'SPI' => 'SPI — Sistema Possível Indeterminado (infinitas soluções).',
            'SI' => 'SI — Sistema Impossível (nenhuma solução).',
            default => 'Tipo desconhecido.',
        };
    }

    private function conferir(array $coeficientes, array $termosIndependentes): int
    {
        $ordem = count($coeficientes);
        if ($ordem === 0 || count($termosIndependentes) !== $ordem) {
            throw new InvalidArgumentException(
                'Informe uma matriz n×n de coeficientes e n termos independentes.',
            );
        }
        foreach ($coeficientes as $linha) {
            if (!is_array($linha) || count($linha) !== $ordem) {
                throw new InvalidArgumentException('A matriz de coeficientes precisa ser quadrada.');
            }
            foreach ($linha as $valor) {
                if (!is_numeric($valor)) {
                    throw new InvalidArgumentException('Os coeficientes devem ser números.');
                }
            }
        }
        foreach ($termosIndependentes as $valor) {
            if (!is_numeric($valor)) {
                throw new InvalidArgumentException('Os termos independentes devem ser números.');
            }
        }
        return $ordem;
    }

    private function triangularizar(array $matrizAumentada, int $ordem): array
    {
        $linhaPivo = 0;
        for ($col = 0; $col < $ordem; $col++) {
            $indiceTroca = -1;
            for ($i = $linhaPivo; $i < $ordem; $i++) {
                if (abs($matrizAumentada[$i][$col]) > 1e-12) {
                    $indiceTroca = $i;
                    break;
                }
            }
            if ($indiceTroca === -1) {
                continue;
            }
            if ($indiceTroca !== $linhaPivo) {
                $linhaTemporaria = $matrizAumentada[$linhaPivo];
                $matrizAumentada[$linhaPivo] = $matrizAumentada[$indiceTroca];
                $matrizAumentada[$indiceTroca] = $linhaTemporaria;
            }
            for ($i = $linhaPivo + 1; $i < $ordem; $i++) {
                $fator = $matrizAumentada[$i][$col] / $matrizAumentada[$linhaPivo][$col];
                for ($j = $col; $j <= $ordem; $j++) {
                    $matrizAumentada[$i][$j] -= $fator * $matrizAumentada[$linhaPivo][$j];
                }
            }
            $linhaPivo++;
        }
        return $matrizAumentada;
    }

    private function classificar(array $matrizAumentada, int $ordem): string
    {
        foreach ($matrizAumentada as $linha) {
            $vazia = true;
            for ($j = 0; $j < $ordem; $j++) {
                if (abs($linha[$j]) > 1e-9) {
                    $vazia = false;
                    break;
                }
            }
            if ($vazia && abs($linha[$ordem]) > 1e-9) {
                return 'SI';
            }
        }
        $linhasNaoNulas = 0;
        foreach ($matrizAumentada as $linha) {
            for ($j = 0; $j < $ordem; $j++) {
                if (abs($linha[$j]) > 1e-9) {
                    $linhasNaoNulas++;
                    break;
                }
            }
        }
        return $linhasNaoNulas < $ordem ? 'SPI' : 'SPD';
    }

    private function substituir(array $matrizAumentada, int $ordem): array
    {
        $solucao = array_fill(0, $ordem, 0.0);
        for ($i = $ordem - 1; $i >= 0; $i--) {
            $resto = $matrizAumentada[$i][$ordem];
            for ($j = $i + 1; $j < $ordem; $j++) {
                $resto -= $matrizAumentada[$i][$j] * $solucao[$j];
            }
            $solucao[$i] = $resto / $matrizAumentada[$i][$i];
            if ($solucao[$i] == 0) {
                $solucao[$i] = 0.0;
            }
        }
        return $solucao;
    }
}
