<?php
declare(strict_types=1);

/**
 * South Laser — Onboarding de clientes
 * Uso: onboarding.php?tipo=landing-page
 * O valor de "tipo" é a chave do planos.json.
 */

session_start();
date_default_timezone_set('America/Sao_Paulo');

const PLANOS_JSON  = __DIR__ . '/planos.json';
const CONTRATOS_DIR = __DIR__ . '/contratos';

/* ---------- helpers ---------- */

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function brl(float $v): string
{
    return 'R$ ' . number_format($v, 2, ',', '.');
}

function soDigitos(string $s): string
{
    return preg_replace('/\D+/', '', $s) ?? '';
}

function cpfValido(string $cpf): bool
{
    if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }
    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) {
            $soma += (int) $cpf[$i] * (($t + 1) - $i);
        }
        $dv = ((10 * $soma) % 11) % 10;
        if ((int) $cpf[$t] !== $dv) {
            return false;
        }
    }
    return true;
}

function fmtCpf(string $c): string
{
    return substr($c, 0, 3) . '.' . substr($c, 3, 3) . '.' . substr($c, 6, 3) . '-' . substr($c, 9, 2);
}

function fmtTel(string $t): string
{
    return strlen($t) === 11
        ? sprintf('(%s) %s-%s', substr($t, 0, 2), substr($t, 2, 5), substr($t, 7))
        : sprintf('(%s) %s-%s', substr($t, 0, 2), substr($t, 2, 4), substr($t, 6));
}

/** Remove quebras de linha e caracteres de controle de valores digitados pelo cliente. */
function limpaLinha(string $s): string
{
    return trim(preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $s) ?? '');
}

function textoCobranca(string $c): string
{
    return $c === 'mensal' ? 'por mês' : 'pagamento único';
}

/* ---------- carrega o JSON e valida o tipo ---------- */

$dados = [];
try {
    $dados = json_decode((string) file_get_contents(PLANOS_JSON), true, 512, JSON_THROW_ON_ERROR);
} catch (Throwable $e) {
    http_response_code(500);
    exit('Não foi possível carregar os planos. Tente novamente mais tarde.');
}

$tipo = (string) ($_POST['tipo'] ?? $_GET['tipo'] ?? '');
$plano = null;
if (preg_match('/^[a-z0-9][a-z0-9_-]{0,49}$/i', $tipo) && isset($dados[$tipo]) && is_array($dados[$tipo])) {
    $plano = $dados[$tipo];
}
$termos = is_array($dados['_termos'] ?? null) ? $dados['_termos'] : [];

if ($plano === null) {
    http_response_code(404);
}

/* ---------- CSRF ---------- */

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

/* ---------- processa o envio ---------- */

$erros = [];
$sucesso = false;
$codigo = '';
$old = ['nome' => '', 'telefone' => '', 'cpf' => ''];

if ($plano !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], (string) ($_POST['csrf'] ?? ''))) {
        $erros[] = 'Sessão expirada. Recarregue a página e tente novamente.';
    }

    $nome = limpaLinha((string) ($_POST['nome'] ?? ''));
    $tel  = soDigitos((string) ($_POST['telefone'] ?? ''));
    $cpf  = soDigitos((string) ($_POST['cpf'] ?? ''));
    $old  = ['nome' => $nome, 'telefone' => (string) ($_POST['telefone'] ?? ''), 'cpf' => (string) ($_POST['cpf'] ?? '')];

    if (mb_strlen($nome) < 5 || mb_strlen($nome) > 120 || !str_contains($nome, ' ')) {
        $erros[] = 'Informe seu nome completo.';
    }
    if (!in_array(strlen($tel), [10, 11], true)) {
        $erros[] = 'Informe um telefone válido com DDD.';
    }
    if (!cpfValido($cpf)) {
        $erros[] = 'O CPF informado não é válido.';
    }
    if (empty($_POST['aceite'])) {
        $erros[] = 'Para continuar, aceite os termos e condições.';
    }

    if (!$erros) {
        $agora = new DateTimeImmutable('now');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';

        $md  = "# Contrato de Prestação de Serviços — South Laser\n\n";
        $md .= "**Aceite registrado em:** " . $agora->format('d/m/Y \à\s H:i:s') . " (America/Sao_Paulo)  \n";
        $md .= "**IP do aceite:** {$ip}\n\n";

        $md .= "## Contratante\n\n";
        $md .= "- **Nome:** {$nome}\n";
        $md .= "- **CPF:** " . fmtCpf($cpf) . "\n";
        $md .= "- **Telefone:** " . fmtTel($tel) . "\n\n";

        $md .= "## Serviço contratado\n\n";
        $md .= "**" . limpaLinha((string) ($plano['nome'] ?? $tipo)) . "** (`{$tipo}`)\n\n";
        $md .= (string) ($plano['descricao'] ?? '') . "\n\n";
        $md .= "- **Valor:** " . brl((float) ($plano['preco'] ?? 0)) . " — " . textoCobranca((string) ($plano['cobranca'] ?? 'unico')) . "\n";
        if (!empty($plano['prazo'])) {
            $md .= "- **Prazo:** " . $plano['prazo'] . "\n";
        }
        $md .= "\n### O que será entregue\n\n";
        foreach ((array) ($plano['entregaveis'] ?? []) as $item) {
            $md .= "- {$item}\n";
        }
        if (!empty($plano['nao_inclui'])) {
            $md .= "\n### O que não está incluído\n\n";
            foreach ((array) $plano['nao_inclui'] as $item) {
                $md .= "- {$item}\n";
            }
        }

        $md .= "\n## Termos e condições aceitos\n\n";
        $textoTermos = '';
        foreach ($termos as $t) {
            $bloco = "### " . ($t['titulo'] ?? '') . "\n\n" . ($t['texto'] ?? '') . "\n\n";
            $md .= $bloco;
            $textoTermos .= $bloco;
        }

        $md .= "## Registro de aceite\n\n";
        $md .= "O contratante declarou ter lido e aceitado integralmente os termos acima, o serviço e o valor descritos.\n\n";
        $md .= "- **Impressão digital dos termos (SHA-256):** `" . hash('sha256', $textoTermos) . "`\n";

        /* pasta protegida */
        if (!is_dir(CONTRATOS_DIR) && !mkdir(CONTRATOS_DIR, 0750, true) && !is_dir(CONTRATOS_DIR)) {
            $erros[] = 'Não foi possível registrar o contrato. Fale com a South Laser.';
        } else {
            $ht = CONTRATOS_DIR . '/.htaccess';
            if (!file_exists($ht)) {
                file_put_contents(
                    $ht,
                    "<IfModule mod_authz_core.c>\n  Require all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n  Deny from all\n</IfModule>\n"
                );
            }

            /* nome = CPF.md; se já existir, não sobrescreve */
            $arquivo = CONTRATOS_DIR . "/{$cpf}.md";
            $h = @fopen($arquivo, 'x');
            if ($h === false) {
                $arquivo = CONTRATOS_DIR . "/{$cpf}_" . $agora->format('Ymd-His') . '.md';
                $h = @fopen($arquivo, 'x');
            }
            if ($h === false) {
                $erros[] = 'Não foi possível registrar o contrato. Fale com a South Laser.';
            } else {
                fwrite($h, $md);
                fclose($h);
                @chmod($arquivo, 0640);
                $sucesso = true;
                $codigo = strtoupper(substr(hash('sha256', basename($arquivo) . $agora->format('c')), 0, 10));
                $_SESSION['csrf'] = bin2hex(random_bytes(32));
            }
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Onboarding — South Laser</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;800&family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
<style>
/* Identidade South Laser: ajuste as cores e fontes aqui */
:root{
  --bg:#060914; --card:#0d1328; --card2:#121a35; --ink:#eef2ff; --muted:#9aa6c8;
  --blue:#2f7bff; --red:#ff2e57; --line:#232d52; --ok:#2ee6a6;
  --grad:linear-gradient(90deg,var(--blue),var(--red));
  --f-display:"Orbitron","Space Grotesk",system-ui,sans-serif;
  --f-body:"Space Grotesk",system-ui,sans-serif;
}
*{box-sizing:border-box}
html{scroll-behavior:smooth}
body{margin:0;background:var(--bg);color:var(--ink);font:400 16px/1.65 var(--f-body);
  background-image:radial-gradient(900px 420px at 12% -8%,rgba(47,123,255,.22),transparent 60%),radial-gradient(800px 380px at 100% 0,rgba(255,46,87,.16),transparent 60%);
  background-repeat:no-repeat}
h1,h2,h3,label,button{font-family:var(--f-body)}
.wrap{max-width:720px;margin:0 auto;padding:28px 20px 72px}
.brand{display:flex;align-items:center;gap:12px;margin-bottom:30px}
.brand svg{width:44px;height:auto;filter:drop-shadow(0 0 8px rgba(47,123,255,.7))}
.brand b{font:800 17px var(--f-display);letter-spacing:.14em;background:var(--grad);-webkit-background-clip:text;background-clip:text;color:transparent}
h1{font:800 clamp(24px,5vw,34px)/1.2 var(--f-display);margin:0 0 12px;letter-spacing:.01em}
h2{font-size:18px;font-weight:700;margin:0 0 14px}
.lead{color:var(--muted);margin:0 0 24px;max-width:60ch}
section{background:var(--card);border:1px solid var(--line);border-radius:14px;padding:24px;margin-bottom:18px;position:relative;overflow:hidden}
section::before{content:"";position:absolute;left:0;top:0;right:0;height:2px;background:var(--grad);opacity:.85}
.price{display:flex;align-items:baseline;gap:10px;flex-wrap:wrap;padding:16px 0;border-top:1px solid var(--line);border-bottom:1px solid var(--line);margin:18px 0}
.price strong{font:800 32px var(--f-display);color:#fff;text-shadow:0 0 18px rgba(47,123,255,.8)}
.price span{color:var(--muted)}
ul.ck,ul.no{list-style:none;margin:0;padding:0}
ul.ck li,ul.no li{padding:6px 0 6px 28px;position:relative}
ul.ck li::before{content:"";position:absolute;left:4px;top:13px;width:12px;height:6px;border-left:2.5px solid var(--ok);border-bottom:2.5px solid var(--ok);transform:rotate(-45deg)}
ul.no li{color:var(--muted)}
ul.no li::before{content:"×";position:absolute;left:5px;top:2px;color:var(--red);font:700 19px var(--f-body)}
h3{font-size:14px;font-weight:700;margin:20px 0 4px}
.field{margin-bottom:16px}
label{display:block;font-weight:500;font-size:14px;margin-bottom:6px;color:var(--muted)}
input[type=text],input[type=tel]{width:100%;padding:12px 14px;border:1.5px solid var(--line);border-radius:10px;font:400 16px var(--f-body);background:var(--card2);color:var(--ink)}
input[type=text]::placeholder,input[type=tel]::placeholder{color:#5f6b91}
input[type=text]:focus,input[type=tel]:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(47,123,255,.3);outline:none}
button:focus-visible,summary:focus-visible,.terms:focus-visible,.aceite input:focus-visible{outline:3px solid var(--blue);outline-offset:2px}
.terms{max-height:260px;overflow:auto;border:1px solid var(--line);border-radius:10px;padding:14px 16px;background:var(--card2);font-size:15px;color:#d5dcf3}
.terms h3{margin-top:14px;color:var(--ink)}
.terms h3:first-child{margin-top:0}
.terms p{margin:4px 0}
.aceite{display:flex;gap:12px;align-items:flex-start;margin:16px 0 0;font:400 15px/1.5 var(--f-body);color:var(--ink)}
.aceite input{width:20px;height:20px;margin-top:3px;accent-color:var(--red);flex:none}
button.go{width:100%;margin-top:20px;padding:15px;border:0;border-radius:10px;background:var(--grad);color:#fff;font-weight:700;font-size:16px;letter-spacing:.02em;cursor:pointer;box-shadow:0 0 22px rgba(47,123,255,.35);transition:box-shadow .15s,filter .15s}
button.go:hover{filter:brightness(1.1);box-shadow:0 0 30px rgba(255,46,87,.45)}
.err{border:1.5px solid var(--red);background:rgba(255,46,87,.1);border-radius:10px;padding:12px 16px;margin-bottom:18px}
.err ul{margin:0;padding-left:18px}
.done{text-align:center;padding:40px 24px}
.done .code{display:inline-block;margin-top:14px;padding:8px 16px;border-radius:8px;border:1px solid var(--line);background:var(--card2);font:600 14px var(--f-display);letter-spacing:.12em}
@media (prefers-reduced-motion:reduce){*{transition:none!important;scroll-behavior:auto!important}}
</style>
</head>
<body>
<div class="wrap">
  <div class="brand">
    <svg viewBox="0 0 64 28" aria-hidden="true"><defs><linearGradient id="g" x1="0" x2="1"><stop offset="0" stop-color="#2f7bff"/><stop offset="1" stop-color="#ff2e57"/></linearGradient></defs><path d="M3 8c0-3 2-5 5-5h48c3 0 5 2 5 5v4c0 8-6 13-14 13-6 0-9-3-11-6h-8c-2 3-5 6-11 6C9 25 3 20 3 12z" fill="url(#g)"/><path d="M10 9h44" stroke="#fff" stroke-opacity=".55" stroke-width="2" stroke-linecap="round"/></svg>
    <b>SOUTH LASER</b>
  </div>

<?php if ($plano === null): ?>
  <section>
    <h1>Link inválido</h1>
    <p class="lead">Não encontramos o serviço deste link. Peça um novo link à South Laser.</p>
  </section>

<?php elseif ($sucesso): ?>
  <section class="done">
    <h1>Tudo certo, <?= e(explode(' ', $old['nome'])[0]) ?>.</h1>
    <p class="lead" style="margin-inline:auto">Recebemos seu aceite para <strong><?= e((string) $plano['nome']) ?></strong>. A South Laser vai entrar em contato pelo telefone informado para os próximos passos.</p>
    <div class="code">Protocolo <?= e($codigo) ?></div>
  </section>

<?php else: ?>
  <h1><?= e((string) ($plano['nome'] ?? $tipo)) ?></h1>
  <p class="lead"><?= e((string) ($plano['descricao'] ?? '')) ?></p>

  <section>
    <h2>O que você está contratando</h2>
    <div class="price">
      <strong><?= e(brl((float) ($plano['preco'] ?? 0))) ?></strong>
      <span><?= e(textoCobranca((string) ($plano['cobranca'] ?? 'unico'))) ?></span>
    </div>
    <?php if (!empty($plano['prazo'])): ?><p style="margin-top:0"><?= e((string) $plano['prazo']) ?>.</p><?php endif; ?>

    <h3>O que será entregue</h3>
    <ul class="ck">
      <?php foreach ((array) ($plano['entregaveis'] ?? []) as $i): ?><li><?= e((string) $i) ?></li><?php endforeach; ?>
    </ul>

    <?php if (!empty($plano['nao_inclui'])): ?>
      <h3>O que não está incluído</h3>
      <ul class="no">
        <?php foreach ((array) $plano['nao_inclui'] as $i): ?><li><?= e((string) $i) ?></li><?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

  <form method="post" action="?tipo=<?= e(urlencode($tipo)) ?>" novalidate>
    <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
    <input type="hidden" name="tipo" value="<?= e($tipo) ?>">

    <?php if ($erros): ?>
      <div class="err" role="alert"><ul><?php foreach ($erros as $m): ?><li><?= e($m) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <section>
      <h2>Seus dados</h2>
      <div class="field">
        <label for="nome">Nome completo</label>
        <input type="text" id="nome" name="nome" autocomplete="name" required value="<?= e($old['nome']) ?>">
      </div>
      <div class="field">
        <label for="telefone">Telefone com DDD</label>
        <input type="tel" id="telefone" name="telefone" inputmode="numeric" autocomplete="tel-national" placeholder="(51) 99999-9999" required value="<?= e($old['telefone']) ?>">
      </div>
      <div class="field">
        <label for="cpf">CPF</label>
        <input type="text" id="cpf" name="cpf" inputmode="numeric" placeholder="000.000.000-00" required value="<?= e($old['cpf']) ?>">
      </div>
    </section>

    <section>
      <h2>Termos e condições</h2>
      <div class="terms" tabindex="0" aria-label="Termos e condições">
        <?php foreach ($termos as $t): ?>
          <h3><?= e((string) ($t['titulo'] ?? '')) ?></h3>
          <p><?= e((string) ($t['texto'] ?? '')) ?></p>
        <?php endforeach; ?>
      </div>
      <label class="aceite">
        <input type="checkbox" name="aceite" value="1" required>
        <span>Li e aceito os termos e condições, o serviço e o valor descritos nesta página.</span>
      </label>
      <button class="go" type="submit">Aceitar e contratar</button>
    </section>
  </form>

<script>
(function(){
  var cpf=document.getElementById('cpf'), tel=document.getElementById('telefone');
  function mask(el,fn){el&&el.addEventListener('input',function(){el.value=fn(el.value.replace(/\D/g,''))});}
  mask(cpf,function(v){v=v.slice(0,11);
    return v.replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d{1,2})$/,'$1-$2');});
  mask(tel,function(v){v=v.slice(0,11);
    if(v.length<=2)return v;
    if(v.length<=6)return '('+v.slice(0,2)+') '+v.slice(2);
    if(v.length<=10)return '('+v.slice(0,2)+') '+v.slice(2,6)+'-'+v.slice(6);
    return '('+v.slice(0,2)+') '+v.slice(2,7)+'-'+v.slice(7);});
})();
</script>
<?php endif; ?>
</div>
</body>
</html>
