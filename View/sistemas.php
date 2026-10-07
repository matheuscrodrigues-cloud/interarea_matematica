<?php
$campoNumero = static function (array $caminho, string $rotulo) use ($pagina, $escapar): void {
    $chave = implode('.', $caminho);
    $id = implode('-', $caminho);
    $nome = array_shift($caminho);
    foreach ($caminho as $parte) {
        $nome .= '[' . $parte . ']';
    }
    $erro = $pagina['erros'][$chave] ?? '';
    ?>
    <div class="numero-campo flex-fill">
        <label class="sr-only visually-hidden form-label" for="<?= $id ?>"><?= $escapar($rotulo) ?></label>
        <input class="form-control" id="<?= $id ?>" name="<?= $nome ?>" type="number" step="any" required
            value="<?= $escapar($pagina['valores'][$chave] ?? '') ?>" <?= $erro ? 'aria-invalid="true" aria-describedby="' . $id . '-erro"' : '' ?> /> <?php if ($erro): ?> <small class="form-text erro-campo text-danger"
                id="<?= $id ?>-erro"><?= $escapar($erro) ?></small>
        <?php endif; ?>
    </div>
    <?php
};
$desenharMatriz = static function (array $matriz) use ($numero): void {
    ?>
    <div class="matriz resposta-matriz" role="group" aria-label="Matriz resultado">
        <?php foreach ($matriz as $linha): ?>
            <div class="linha-matriz d-flex gap-2 mb-2">
                <?php foreach ($linha as $valor): ?>
                    <span><?= $numero($valor) ?></span>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
};
?>

<h2 class="fs-4 fw-bold mb-3">Resolver sistema linear</h2>
<p class="introducao mb-4">
    Escreva os coeficientes de cada equação e o termo independente. A resposta será classificada em SPD, SPI
    ou SI.
</p>
<form method="get" class="configuracao d-flex align-items-end gap-3 border-bottom pb-4 mb-4">
    <input type="hidden" name="atividade" value="sistemas" />
    <div class="campo mb-3">
        <label class="form-label" for="ordem">Quantidade de equações</label>
        <select class="form-select" name="ordem" id="ordem">
            <option value="2" <?= $pagina['ordem'] === 2 ? 'selected' : '' ?>>2 equações</option>
            <option value="3" <?= $pagina['ordem'] === 3 ? 'selected' : '' ?>>3 equações</option>
        </select>
    </div>
    <button class="secundario btn btn-dark" type="submit">Preparar equações</button>
</form>
<form method="post" action="?atividade=sistemas&amp;ordem=<?= $pagina['ordem'] ?>">
    <fieldset class="mb-4 flex-fill">
        <legend class="fs-5 fw-bold">Equações do sistema</legend>
        <div class="sistema">
            <?php for ($linha = 0; $linha < $pagina['ordem']; $linha++): ?>
                <div class="equacao d-flex align-items-center gap-2 mb-3" role="group"
                    aria-label="Equação <?= $linha + 1 ?>">
                    <?php for ($coluna = 0; $coluna < $pagina['ordem']; $coluna++): ?>
                        <div class="termo d-flex align-items-center gap-2 flex-fill">
                            <?php $campoNumero(
                                ['coeficientes', $linha, $coluna],
                                'Equação ' . ($linha + 1) . ', coeficiente de x' . ($coluna + 1),
                            ); ?>
                            <span>
                                x
                                <sub><?= $coluna + 1 ?></sub>
                            </span>
                        </div>
                        <span><?= $coluna < $pagina['ordem'] - 1 ? '+' : '=' ?></span>
                    <?php endfor; ?>
                    <?php $campoNumero(
                        ['termosIndependentes', $linha],
                        'Termo independente da equação ' . ($linha + 1),
                    ); ?>
                </div>
            <?php endfor; ?>
        </div>
    </fieldset>
    <button class="btn btn-dark" type="submit">Resolver sistema</button>
</form>
<?php if ($pagina['erros']): ?>
    <p class="aviso alert alert-danger" role="alert">
        Confira os coeficientes indicados. Seus dados foram mantidos.
    </p>
<?php endif; ?>
<?php if ($pagina['resultado']):
    $resposta = $pagina['resultado']['dados']; ?>
    <section class="resultado border-top pt-4 mt-4">
        <h2 class="fs-4 fw-bold mb-3">Resposta do sistema</h2>
        <p class="valor-resposta"><?= $resposta['tipo'] ?></p>
        <p><?= $escapar(['SPD' => 'Sistema Possível Determinado: uma única solução.', 'SPI' => 'Sistema Possível Indeterminado: infinitas soluções.', 'SI' => 'Sistema Impossível: nenhuma solução.',][$resposta['tipo']]) ?>
        </p>
        <?php if ($resposta['solucao'] !== null): ?>
            <ul class="solucoes d-flex gap-4 list-unstyled">
                <?php foreach (
                    $resposta['solucao']
                    as $indice => $valor
                ): ?>
                    <li>
                        x
                        <sub><?= $indice + 1 ?></sub>
                        = <?= $numero($valor) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
    <?php
endif; ?>
<details class="referencia border-top pt-3 mt-4">
    <summary>Sobre o método de resolução</summary>
    <p>
        O programa monta a matriz aumentada, faz o escalonamento, identifica SPD, SPI ou SI e, quando há
        solução única, usa substituição regressiva. Classificação conforme o material do professor Gabriel
        Sena.
    </p>
</details>