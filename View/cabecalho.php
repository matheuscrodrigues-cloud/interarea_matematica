<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Laboratório de Álgebra — Derick e Matheus</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" />
    <link rel="stylesheet"
        href="templates/css/style.css?v=<?= substr(hash_file('sha256', __DIR__ . '/../templates/css/style.css'), 0, 12) ?>" />
</head>

<body>
    <a class="pular visually-hidden-focusable" href="#atividade">Ir para a atividade</a>
    <div class="folha mx-auto my-4 p-5 bg-white">
        <header class="border-bottom pb-3 mb-3">
            <h1 class="fs-2 fw-bold">Laboratório de Álgebra</h1>
            <p>Matrizes e sistemas lineares · folha de exercícios</p>
            <p class="autoria small text-secondary">Derick e Matheus · SENAI · Trabalho interárea</p>
        </header>
        <nav class="nav gap-3 border-bottom mb-4" aria-label="Atividades">
            <a href="?atividade=matrizes" <?= $pagina['atividade'] === 'matrizes' ? 'aria-current="page"' : '' ?>>Matrizes</a>
            <a href="?atividade=sistemas" <?= $pagina['atividade'] === 'sistemas' ? 'aria-current="page"' : '' ?>>Sistemas</a>
        </nav>
        <main id="atividade">