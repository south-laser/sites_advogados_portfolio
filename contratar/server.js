'use strict';

/**
 * South Laser — Contratação de clientes (Node.js, sem dependências, Node 18+)
 *
 * Uso:   node server.js
 * Link:  http://localhost:3000/?tipo=landing-page
 *
 * Estrutura:
 *   server.js        lógica (este arquivo)
 *   planos.json      planos e termos
 *   views/*.html     páginas (edite aqui o texto e o layout)
 *   views/style.css  visual
 *   views/mascaras.js máscaras de CPF e telefone
 *   contratos/       gerada automaticamente com os aceites (.md)
 *
 * Variáveis de ambiente (opcionais):
 *   PORT=3000   HOST=127.0.0.1   TRUST_PROXY=1 (atrás de Nginx/Apache)
 *
 * E-mail: usa o módulo ./email (pasta email/ ao lado do server.js), que exporta
 *   enviarEmail({ to, subject, html }).
 */

const http = require('node:http');
const fsp = require('node:fs/promises');
const path = require('node:path');
const crypto = require('node:crypto');

const PORT = Number(process.env.PORT) || 3000;
const HOST = process.env.HOST || '127.0.0.1';
const TRUST_PROXY = process.env.TRUST_PROXY === '1';

const PLANOS_JSON = path.join(__dirname, 'planos.json');
const CONTRATOS_DIR = path.join(__dirname, 'contratos');
const VIEWS_DIR = path.join(__dirname, 'views');

/** Únicos arquivos servidos diretamente (nada mais da pasta é exposto). */
const ESTATICOS = {
  '/style.css': ['style.css', 'text/css; charset=utf-8'],
  '/mascaras.js': ['mascaras.js', 'text/javascript; charset=utf-8'],
};

/* ---------- helpers ---------- */

const ESC = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };
const e = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ESC[c]);

const brl = (v) =>
  'R$ ' + Number(v || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

const soDigitos = (s) => String(s ?? '').replace(/\D+/g, '');

function cpfValido(c) {
  if (c.length !== 11 || /^(\d)\1{10}$/.test(c)) return false;
  for (let t = 9; t < 11; t++) {
    let soma = 0;
    for (let i = 0; i < t; i++) soma += Number(c[i]) * (t + 1 - i);
    if (Number(c[t]) !== ((10 * soma) % 11) % 10) return false;
  }
  return true;
}

const fmtCpf = (c) => `${c.slice(0, 3)}.${c.slice(3, 6)}.${c.slice(6, 9)}-${c.slice(9)}`;
const fmtTel = (t) =>
  t.length === 11
    ? `(${t.slice(0, 2)}) ${t.slice(2, 7)}-${t.slice(7)}`
    : `(${t.slice(0, 2)}) ${t.slice(2, 6)}-${t.slice(6)}`;

/** Remove quebras de linha e caracteres de controle de valores digitados pelo cliente. */
const limpaLinha = (s) => String(s ?? '').replace(/[\x00-\x1F\x7F]+/g, ' ').trim();

const textoCobranca = (c) => (c === 'mensal' ? 'por mês' : 'pagamento único');

function agoraSP() {
  const p = Object.fromEntries(
    new Intl.DateTimeFormat('pt-BR', {
      timeZone: 'America/Sao_Paulo',
      year: 'numeric', month: '2-digit', day: '2-digit',
      hour: '2-digit', minute: '2-digit', second: '2-digit', hourCycle: 'h23',
    }).formatToParts(new Date()).map((x) => [x.type, x.value])
  );
  return {
    data: `${p.day}/${p.month}/${p.year}`,
    hora: `${p.hour}:${p.minute}:${p.second}`,
    stamp: `${p.year}${p.month}${p.day}-${p.hour}${p.minute}${p.second}`,
  };
}

function parseCookies(header) {
  const out = {};
  for (const par of String(header || '').split(';')) {
    const i = par.indexOf('=');
    if (i > 0) out[par.slice(0, i).trim()] = par.slice(i + 1).trim();
  }
  return out;
}

function lerCorpo(req, limite = 20_000) {
  return new Promise((resolve, reject) => {
    let tam = 0;
    const partes = [];
    req.on('data', (c) => {
      tam += c.length;
      if (tam > limite) {
        reject(Object.assign(new Error('Corpo grande demais'), { status: 413 }));
        req.destroy();
        return;
      }
      partes.push(c);
    });
    req.on('end', () => resolve(Buffer.concat(partes).toString('utf8')));
    req.on('error', reject);
  });
}

function csrfIgual(a, b) {
  const x = Buffer.from(String(a || ''));
  const y = Buffer.from(String(b || ''));
  return x.length === y.length && x.length > 0 && crypto.timingSafeEqual(x, y);
}

/* ---------- templates (arquivos em /views) ---------- */

const lerView = (nome) => fsp.readFile(path.join(VIEWS_DIR, nome), 'utf8');

/** {{campo}} = texto escapado · {{{campo}}} = HTML já pronto */
function preencher(tpl, vars) {
  return tpl.replace(/\{\{\{(\w+)\}\}\}|\{\{(\w+)\}\}/g, (_, cru, esc) =>
    cru ? String(vars[cru] ?? '') : e(vars[esc])
  );
}

async function pagina(view, vars, comScript = false) {
  const [layout, conteudo] = await Promise.all([lerView('layout.html'), lerView(view)]);
  return preencher(layout, {
    conteudo: preencher(conteudo, vars),
    script: comScript ? '<script src="/mascaras.js"></script>' : '',
  });
}

const lista = (itens, cls) =>
  Array.isArray(itens) && itens.length
    ? `<ul class="${cls}">${itens.map((i) => `<li>${e(i)}</li>`).join('')}</ul>`
    : '';

function varsFormulario({ plano, tipo, termos, csrf, erros, old }) {
  return {
    titulo: plano.nome || tipo,
    descricao: plano.descricao,
    preco: brl(plano.preco),
    cobranca: textoCobranca(plano.cobranca),
    prazo_html: plano.prazo ? `<p class="nomt">${e(plano.prazo)}.</p>` : '',
    entregaveis_html: lista(plano.entregaveis, 'ck'),
    nao_inclui_html: plano.nao_inclui && plano.nao_inclui.length
      ? `<h3>O que não está incluído</h3>${lista(plano.nao_inclui, 'no')}`
      : '',
    tipo,
    tipo_url: encodeURIComponent(tipo),
    csrf,
    erros_html: erros.length
      ? `<div class="err" role="alert"><ul>${erros.map((m) => `<li>${e(m)}</li>`).join('')}</ul></div>`
      : '',
    nome: old.nome,
    email: old.email,
    telefone: old.telefone,
    cpf: old.cpf,
    termos_html: termos.map((t) => `<h3>${e(t.titulo)}</h3><p>${e(t.texto)}</p>`).join('\n        '),
  };
}

/* ---------- contrato ---------- */

function montaContrato({ tipo, plano, termos, nome, email, cpf, tel, ip, agora }) {
  let md = '# Contrato de Prestação de Serviços — South Laser\n\n';
  md += `**Aceite registrado em:** ${agora.data} às ${agora.hora} (America/Sao_Paulo)  \n`;
  md += `**IP do aceite:** ${ip}\n\n`;

  md += '## Contratante\n\n';
  md += `- **Nome:** ${nome}\n- **CPF:** ${fmtCpf(cpf)}\n- **Telefone:** ${fmtTel(tel)}\n- **E-mail:** ${email}\n\n`;

  md += '## Serviço contratado\n\n';
  md += `**${limpaLinha(plano.nome || tipo)}** (\`${tipo}\`)\n\n${plano.descricao || ''}\n\n`;
  md += `- **Valor:** ${brl(plano.preco)} — ${textoCobranca(plano.cobranca)}\n`;
  if (plano.prazo) md += `- **Prazo:** ${plano.prazo}\n`;

  md += '\n### O que será entregue\n\n';
  for (const i of plano.entregaveis || []) md += `- ${i}\n`;
  if (plano.nao_inclui && plano.nao_inclui.length) {
    md += '\n### O que não está incluído\n\n';
    for (const i of plano.nao_inclui) md += `- ${i}\n`;
  }

  md += '\n## Termos e condições aceitos\n\n';
  let textoTermos = '';
  for (const t of termos) {
    const bloco = `### ${t.titulo || ''}\n\n${t.texto || ''}\n\n`;
    md += bloco;
    textoTermos += bloco;
  }

  md += '## Registro de aceite\n\n';
  md += 'O contratante declarou ter lido e aceitado integralmente os termos acima, o serviço e o valor descritos.\n\n';
  md += `- **Impressão digital dos termos (SHA-256):** \`${crypto.createHash('sha256').update(textoTermos).digest('hex')}\`\n`;
  return md;
}

/** Grava contratos/CPF.md; se já existir, não sobrescreve e usa CPF_data-hora.md */
async function gravaContrato(cpf, md, stamp) {
  await fsp.mkdir(CONTRATOS_DIR, { recursive: true, mode: 0o750 });
  let arquivo = path.join(CONTRATOS_DIR, `${cpf}.md`);
  try {
    await fsp.writeFile(arquivo, md, { flag: 'wx', mode: 0o640 });
    return arquivo;
  } catch (err) {
    if (err.code !== 'EEXIST') throw err;
  }
  arquivo = path.join(CONTRATOS_DIR, `${cpf}_${stamp}.md`);
  await fsp.writeFile(arquivo, md, { flag: 'wx', mode: 0o640 });
  return arquivo;
}

/* ---------- e-mail de boas-vindas ---------- */

/** Carrega o módulo ./email sem derrubar o servidor se ele estiver com problema. */
let enviarEmail = null;
try {
  const mod = require('./email/email');
  enviarEmail = typeof mod === 'function' ? mod : mod.enviarEmail;
  if (typeof enviarEmail !== 'function') throw new Error('./email não exporta uma função de envio');
} catch (err) {
  enviarEmail = null;
  console.error('Módulo ./email indisponível, e-mails de boas-vindas desativados:', err.message);
}

async function enviarBoasVindas({ nome, email, plano, tipo, codigo }) {
  if (!enviarEmail) throw new Error('módulo ./email indisponível');

  const primeiroNome = nome.split(' ')[0];

  const prazoLinha = plano.prazo
    ? `<tr><td style="padding:10px 0;border-top:1px solid #3a2f5f;color:#cc84ff;font-weight:700;">Prazo</td>` +
      `<td style="padding:10px 0;border-top:1px solid #3a2f5f;color:#ffffff;">${e(plano.prazo)}</td></tr>`
    : '';

  const html = preencher(await lerView('email-boas-vindas.html'), {
    primeiro_nome: primeiroNome,
    plano_nome: plano.nome || tipo,
    preco: brl(plano.preco),
    cobranca: textoCobranca(plano.cobranca),
    prazo_linha: prazoLinha,
    codigo,
  });

  const resultado = await enviarEmail({
    to: email,
    subject: `Boas-vindas à South Laser, ${primeiroNome.slice(0, 40)}!`,
    html,
  });

  // se o módulo devolver um objeto de erro em vez de lançar exceção
  if (resultado && (resultado.success === false || resultado.sucesso === false || resultado.ok === false || resultado.error || resultado.erro)) {
    throw new Error(`o módulo de e-mail informou falha: ${JSON.stringify(resultado).slice(0, 300)}`);
  }
}

/* ---------- servidor ---------- */

const CABECALHOS = {
  'X-Content-Type-Options': 'nosniff',
  'Referrer-Policy': 'same-origin',
  'X-Frame-Options': 'DENY',
  'Content-Security-Policy':
    "default-src 'none'; style-src 'self' https://fonts.googleapis.com; " +
    "font-src https://fonts.gstatic.com; script-src 'self'; " +
    "form-action 'self'; base-uri 'none'; frame-ancestors 'none'",
};

function enviar(res, status, corpo, cookie) {
  const h = { ...CABECALHOS, 'Content-Type': 'text/html; charset=utf-8', 'Cache-Control': 'no-store' };
  if (cookie) h['Set-Cookie'] = cookie;
  res.writeHead(status, h);
  res.end(corpo);
}

function texto(res, status, msg) {
  res.writeHead(status, { ...CABECALHOS, 'Content-Type': 'text/plain; charset=utf-8' });
  res.end(msg);
}

async function tratar(req, res) {
  const url = new URL(req.url, 'http://localhost');

  if (url.pathname === '/favicon.ico') { res.writeHead(204); return res.end(); }

  if (Object.hasOwn(ESTATICOS, url.pathname) && req.method === 'GET') {
    const [arquivo, tipoMime] = ESTATICOS[url.pathname];
    const conteudo = await fsp.readFile(path.join(VIEWS_DIR, arquivo));
    res.writeHead(200, { ...CABECALHOS, 'Content-Type': tipoMime, 'Cache-Control': 'no-cache' });
    return res.end(conteudo);
  }

  if (url.pathname !== '/') return texto(res, 404, 'Não encontrado');
  if (req.method !== 'GET' && req.method !== 'POST') return texto(res, 405, 'Método não permitido');

  /* CSRF: cookie + campo oculto (double submit) */
  const seguro = TRUST_PROXY && req.headers['x-forwarded-proto'] === 'https';
  const mkCookie = (v) => `csrf=${v}; Path=/; HttpOnly; SameSite=Strict${seguro ? '; Secure' : ''}`;
  let csrf = parseCookies(req.headers.cookie).csrf;
  let cookie = null;
  if (!/^[a-f0-9]{64}$/.test(csrf || '')) {
    csrf = crypto.randomBytes(32).toString('hex');
    cookie = mkCookie(csrf);
  }

  const form = req.method === 'POST' ? new URLSearchParams(await lerCorpo(req)) : new URLSearchParams();

  /* lê o JSON a cada requisição: editar o planos.json não exige reiniciar */
  let dados;
  try {
    dados = JSON.parse(await fsp.readFile(PLANOS_JSON, 'utf8'));
  } catch (err) {
    console.error('Falha ao ler planos.json:', err.message);
    return texto(res, 500, 'Não foi possível carregar os planos. Tente novamente mais tarde.');
  }

  const tipo = String(form.get('tipo') ?? url.searchParams.get('tipo') ?? '');
  const plano =
    /^[a-z0-9][a-z0-9_-]{0,49}$/i.test(tipo) &&
    Object.hasOwn(dados, tipo) &&
    dados[tipo] && typeof dados[tipo] === 'object' && !Array.isArray(dados[tipo])
      ? dados[tipo]
      : null;
  const termos = Array.isArray(dados._termos) ? dados._termos : [];

  if (!plano) return enviar(res, 404, await pagina('invalido.html', {}), cookie);

  const erros = [];
  let old = { nome: '', email: '', telefone: '', cpf: '' };

  if (req.method === 'POST') {
    if (!csrfIgual(csrf, form.get('csrf'))) {
      erros.push('Sessão expirada. Recarregue a página e tente novamente.');
    }

    const nome = limpaLinha(form.get('nome'));
    const email = limpaLinha(form.get('email')).toLowerCase();
    const tel = soDigitos(form.get('telefone'));
    const cpf = soDigitos(form.get('cpf'));
    old = { nome, email, telefone: String(form.get('telefone') ?? ''), cpf: String(form.get('cpf') ?? '') };

    if (nome.length < 5 || nome.length > 120 || !nome.includes(' ')) erros.push('Informe seu nome completo.');
    if (email.length > 254 || !/^[^\s@<>,;:\"()[\]\\]+@[^\s@<>,;:\"()[\]\\]+\.[^\s@<>,;:\"()[\]\\]{2,}$/.test(email)) erros.push('Informe um e-mail válido.');
    if (tel.length !== 10 && tel.length !== 11) erros.push('Informe um telefone válido com DDD.');
    if (!cpfValido(cpf)) erros.push('O CPF informado não é válido.');
    if (!form.get('aceite')) erros.push('Para continuar, aceite os termos e condições.');

    if (!erros.length) {
      const agora = agoraSP();
      const ipBruto = TRUST_PROXY
        ? String(req.headers['x-forwarded-for'] || '').split(',')[0]
        : req.socket.remoteAddress;
      const ip = limpaLinha(ipBruto || req.socket.remoteAddress || 'desconhecido').slice(0, 64);

      try {
        const md = montaContrato({ tipo, plano, termos, nome, email, cpf, tel, ip, agora });
        const arquivo = await gravaContrato(cpf, md, agora.stamp);
        const codigo = crypto
          .createHash('sha256')
          .update(path.basename(arquivo) + new Date().toISOString())
          .digest('hex')
          .slice(0, 10)
          .toUpperCase();

        // e-mail em segundo plano: se falhar, o contrato continua válido e o erro vai para o log
        enviarBoasVindas({ nome, email, plano, tipo, codigo }).catch((err) =>
          console.error(`Falha ao enviar e-mail de boas-vindas (contrato ${path.basename(arquivo)}):`, err.message)
        );

        csrf = crypto.randomBytes(32).toString('hex'); // rotaciona o token
        const html = await pagina('sucesso.html', {
          primeiro_nome: nome.split(' ')[0],
          plano_nome: plano.nome || tipo,
          codigo,
        });
        return enviar(res, 200, html, mkCookie(csrf));
      } catch (err) {
        console.error('Falha ao gravar contrato:', err);
        erros.push('Não foi possível registrar o contrato. Fale com a South Laser.');
      }
    }
  }

  const html = await pagina('formulario.html', varsFormulario({ plano, tipo, termos, csrf, erros, old }), true);
  return enviar(res, erros.length ? 422 : 200, html, cookie);
}

http
  .createServer((req, res) => {
    tratar(req, res).catch((err) => {
      console.error(err);
      if (!res.headersSent) {
        texto(res, err.status || 500, err.status === 413 ? 'Requisição grande demais' : 'Erro interno. Tente novamente mais tarde.');
      }
    });
  })
  .listen(PORT, HOST, () => {
    console.log(`South Laser rodando em http://${HOST}:${PORT}/?tipo=landing-page`);
  });
