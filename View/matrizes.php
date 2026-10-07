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
    <input
        class="form-control"
        id="<?= $id ?>"
        name="<?= $nome ?>"
        type="number"
        step="any"
        required
        value="<?= $escapar($pagina['valores'][$chave] ?? '') ?>"
        <?= $erro ? 'aria-invalid="true" aria-describedby="' . $id . '-erro"' : '' ?>
    />
    <?php if ($erro): ?>
    <small class="form-text erro-campo text-danger" id="<?= $id ?>-erro"><?= $escapar($erro) ?></small>
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

<h2 class="fs-4 fw-bold mb-3">Operações com matrizes</h2>
<p class="introducao mb-4">
    Escolha a operação e prepare a grade. Depois, preencha os valores para calcular.
</p>
<form method="get" class="configuracao d-flex align-items-end gap-3 border-bottom pb-4 mb-4">
    <input type="hidden" name="atividade" value="matrizes" />
    <div class="campo mb-3">
        <label class="form-label" for="operacao">Operação</label>
        <select class="form-select" id="operacao" name="operacao">
            <?php foreach ($pagina['operacoes'] as $chave => $rotulo): ?>
            <option value="<?= $chave ?>" <?= $pagina['operacao'] === $chave ? 'selected' : '' ?>><?= $rotulo ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="campo mb-3">
        <label class="form-label" for="ordem">Tamanho</label>
        <select class="form-select" id="ordem" name="ordem">
            <option value="2" <?= $pagina['ordem'] === 2 ? 'selected' : '' ?>>2 × 2</option>
            <option value="3" <?= $pagina['ordem'] === 3 ? 'selected' : '' ?>>3 × 3</option>
        </select>
    </div>
    <button class="secundario btn btn-dark" type="submit">Preparar grade</button>
</form>
<form method="post" action="?atividade=matrizes&amp;ordem=<?= $pagina['ordem'] ?>&amp;operacao=<?= $pagina['operacao'] ?>">
    <h3 class="fs-5 fw-bold mb-3"><?= $escapar($pagina['operacoes'][$pagina['operacao']]) ?> · <?= $pagina[ 'ordem' ] ?> × <?= $pagina['ordem'] ?></h3>
    <div class="operandos d-flex gap-4">
        <?php foreach (['matrizEsquerda' => 'Matriz A', 'matrizDireita' => 'Matriz B'] as $nome => $rotulo):
        if (
            ($nome === 'matrizEsquerda' && !$pagina['usaEsquerda']) ||
            ($nome === 'matrizDireita' && !$pagina['usaDireita'])
        ) {
            continue;
        } ?>
        <fieldset class="mb-4 flex-fill">
            <legend class="fs-5 fw-bold"><?= $rotulo ?></legend>
            <div class="matriz">
                <?php for ($linha = 0; $linha < $pagina['ordem']; $linha++): ?>
                <div class="linha-matriz d-flex gap-2 mb-2">
                    <?php for ($coluna = 0; $coluna < $pagina['ordem']; $coluna++) {
                        $campoNumero(
                            [$nome, $linha, $coluna],
                            $rotulo . ', linha ' . ($linha + 1) . ', coluna ' . ($coluna + 1),
                        );
                    } ?>
                </div>
                <?php endfor; ?>
            </div>
        </fieldset>
        <?php
        endforeach; ?>
    </div>
    <?php if ($pagina['operacao'] === 'escalar'): ?>
    <div class="escalar">
        <label class="form-label" for="escalar">Escalar k</label>
        <?php $campoNumero(
            ['escalar'],
            'Escalar k',
        ); ?>
    </div>
    <?php endif; ?>
    <?php if (!$pagina['usaEsquerda']): ?>
    <p>A matriz será gerada no tamanho selecionado; não é necessário preencher valores.</p>
    <?php endif; ?>
    <button class="btn btn-dark" type="submit"><?= $pagina['usaEsquerda'] ? 'Calcular resultado' : 'Gerar matriz' ?></button>
</form>
<?php if ($pagina['erros']): ?>
<p class="aviso alert alert-danger" role="alert"><?= $escapar($pagina['erros']['calculo'] ?? 'Confira os valores indicados na grade. Seus dados foram mantidos.') ?></p>
<?php endif; ?>
<?php if ($pagina['resultado']): ?>
<section class="resultado border-top pt-4 mt-4">
    <h2 class="fs-4 fw-bold mb-3">Resposta · <?= $escapar($pagina['operacoes'][$pagina['operacao']]) ?></h2>
    <?php if ($pagina['resultado']['tipo'] === 'numero'): ?>
    <p class="valor-resposta">det(A) = <?= $numero($pagina['resultado']['dados']) ?></p>
    <?php else:$desenharMatriz($pagina['resultado']['dados']);endif; ?>
</section>
<?php endif; ?>
<details class="referencia border-top pt-3 mt-4">
    <summary>Como os cálculos são feitos?</summary>
    <p>
        O determinante usa cálculo direto para ordem 1 e 2 e expansão por cofatores nas demais. A inversa é a
        adjunta dividida pelo determinante; matrizes singulares não possuem inversa.
    </p>
</details>
