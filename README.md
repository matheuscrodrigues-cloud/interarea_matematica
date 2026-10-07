# Laboratório de Álgebra — Derick e Matheus
Trabalho interárea SENAI. PHP >= 8.4, HTML5, CSS3 e Bootstrap 5.3.8, Laravel Herd e PHPUnit. Bootstrap CSS via CDN; sem JavaScript, banco de dados ou framework PHP.

## Instalação
1. Instale PHP 8.4 ou superior, Composer e Laravel Herd.
2. Na pasta desta aplicação, execute `composer install` com PHP 8.4 e confira `php -v`.
3. Para medir cobertura, habilite Xdebug compatível com a versão de PHP. Neste computador, PHP 8.4.26 e Xdebug 3.5.3 estão instalados em `%LOCALAPPDATA%\Programs\PHP84`; o caminho do executável é `%LOCALAPPDATA%\Programs\PHP84\php.exe`.

No PowerShell deste computador, para usar explicitamente a versão instalada:
```powershell
$php84 = "$env:LOCALAPPDATA\Programs\PHP84\php.exe"
& $php84 -v
& $php84 C:\php83\composer.phar install
```
O caminho do Composer acima pertence a este computador; em outra máquina, use a instalação local do Composer com PHP >= 8.4.

## Executar no Laravel Herd
No gerenciador de sites do Herd, adicione um projeto existente e selecione esta pasta, que contém index.php. Escolha PHP 8.4 ou superior para o site. Como nome sugerido, use `algebra-derick` e abra `http://algebra-derick.test` se esse nome estiver configurado. Use o domínio mostrado pelo Herd caso ele seja diferente.
O usuário confirmou que as aplicações funcionam no Herd. A validação automatizada adicional foi feita com servidor PHP local.
Alternativa para prévia:
```powershell
& "$env:LOCALAPPDATA\Programs\PHP84\php.exe" -S 127.0.0.1:8003
```
Endereço atual: http://127.0.0.1:8003/.

## Algoritmos implementados
- **Soma e subtração:** Conferem dimensões iguais e combinam os elementos na mesma posição.
- **Multiplicação:** Soma os produtos da linha da matriz esquerda pela coluna da direita; exige dimensões compatíveis.
- **Escalar e transposta:** Escalar multiplica cada elemento por k; transposta troca índices de linha e coluna.
- **Identidade e nula:** Geram diagonal principal com 1 e demais posições com 0, ou uma matriz inteiramente zero.
- **Determinante:** Usa fórmula direta em 1×1 e 2×2; nas demais ordens, expansão por cofatores da primeira linha.
- **Inversa:** Calcula cofatores, transpõe para obter a adjunta e divide pelo determinante; rejeita matriz singular.
- **Sistema linear:** Monta matriz aumentada, escalona com troca de linhas, identifica SPD/SPI/SI e aplica substituição regressiva no SPD.

## Organização e uso
Controller contém as classes de cálculo e um controlador de página. index.php delega o processamento; View contém as telas separadas por atividade e o cabeçalho/rodapé; templates/css contém o estilo próprio. Testes em tests e documentação em docs.
Escolha Matrizes ou Sistemas. Prepare operação e ordem 2×2/3×3 antes de preencher. Operações unárias não exigem matriz B. Identidade e nula dispensam entradas. Nova configuração prepara grade vazia.
Resultado abaixo da atividade, erros junto dos campos, valores mantidos após envio. Sem persistência entre atividades.

## Testes e cobertura
Execução simples: `php vendor/bin/phpunit --do-not-cache-result` (PHP >= 8.4).
Execução com cobertura e verificação automática do mínimo de 80%, neste computador:
```powershell
.\testar.ps1
```
Em outra instalação: `.\testar.ps1 -PHP "C:\caminho\php.exe"`, com Xdebug habilitado. O script ativa o modo coverage apenas nessa execução.
Comando equivalente em qualquer terminal com PHP >= 8.4 e Xdebug:
```text
php -d xdebug.mode=coverage vendor/bin/phpunit --do-not-cache-result --coverage-html docs/evidencias/cobertura --coverage-clover docs/evidencias/cobertura.xml --coverage-text=docs/evidencias/cobertura.txt
php tests/verificar-cobertura.php docs/evidencias/cobertura.xml
```
Resultado real em 06/10/2026: **OK (26 tests, 110 assertions)**, PHP 8.4.26, PHPUnit 12.5.38, Xdebug 3.5.3.
Cobertura de linhas executáveis dos algoritmos: **93.85% (168/179)**. A medição exclui controlador de página, views e dependências. Os percentuais de métodos/classes no relatório representam cobertura integral dessas unidades; o critério aqui é cobertura de linhas.

| Classe | Linhas cobertas/total | Cobertura |
|---|---:|---:|
| MatrizesController | 99/107 | 92.52% |
| SistemasController | 69/72 | 95.83% |

[Relatório HTML](docs/evidencias/cobertura/index.html) · [Resumo de cobertura](docs/evidencias/cobertura.txt) · [Clover XML](docs/evidencias/cobertura.xml) · [Execução dos testes](docs/evidencias/testes.txt).
Casos conhecidos, bordas, entradas inválidas e precisão numérica constam da suíte. A interface também foi conferida em desktop, celular e por teclado.


