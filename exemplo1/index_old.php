<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Corrêa Prado Advocacia | Direito Previdenciário</title>
<meta name="description" content="Escritório dedicado ao Direito Previdenciário em Porto Alegre/RS. Orientação em aposentadorias, benefícios por incapacidade, revisões, BPC/LOAS e recursos junto ao INSS.">

<!-- Tailwind CSS (utilitários de layout/espaçamento) -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Tipografia -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap">

<!-- Estilos próprios (paleta, tema, componentes) -->
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ============ NAV ============ -->
<header class="sticky top-0 z-50 backdrop-blur border-b border-soft" style="background:color-mix(in srgb, var(--bg) 88%, transparent);">
  <div class="max-w-6xl mx-auto px-5 sm:px-8 flex items-center justify-between h-20">
    <a href="#topo" class="flex items-center gap-3">
      <span class="flex items-center justify-center w-11 h-11 rounded-sm border border-soft" style="color:var(--accent);">
        <span class="serif text-lg font-semibold">CP</span>
      </span>
      <span class="leading-tight">
        <span class="block serif text-base sm:text-lg font-semibold tracking-tight">Corrêa Prado Advocacia</span>
        <span class="block text-[11px] tracking-[0.16em] uppercase text-muted">Direito Previdenciário</span>
      </span>
    </a>

    <nav class="hidden md:flex items-center gap-9 text-sm font-medium">
      <a href="#sobre" class="nav-link">Sobre</a>
      <a href="#areas" class="nav-link">Áreas de Atuação</a>
      <a href="#porque" class="nav-link">Por que nos escolher</a>
      <a href="#faq" class="nav-link">Dúvidas Frequentes</a>
    </nav>

    <div class="flex items-center gap-3">
      <a href="https://wa.me/5551999999999?text=Ol%C3%A1%2C%20gostaria%20de%20falar%20sobre%20o%20meu%20benef%C3%ADcio%20do%20INSS."
         target="_blank" rel="noopener"
         class="hidden sm:inline-flex btn-whatsapp items-center gap-2 rounded-full px-5 py-2.5 text-sm font-semibold transition-colors">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.37 2 11.77c0 2.06.64 3.98 1.75 5.57L2 22l4.86-1.67A10.2 10.2 0 0 0 12 21.53c5.52 0 10-4.36 10-9.76S17.52 2 12 2Zm0 17.7c-1.7 0-3.29-.47-4.64-1.29l-.33-.2-2.88.99.96-2.78-.22-.34a7.8 7.8 0 0 1-1.27-4.31c0-4.33 3.65-7.85 8.38-7.85 4.62 0 8.38 3.52 8.38 7.85 0 4.34-3.76 7.86-8.38 7.86Zm4.53-5.77c-.25-.12-1.47-.71-1.7-.79-.23-.08-.39-.12-.56.12-.17.25-.64.79-.78.95-.15.17-.29.19-.54.06-.25-.12-1.04-.37-1.98-1.19-.73-.63-1.22-1.4-1.37-1.64-.14-.25-.02-.38.11-.5.11-.11.25-.29.37-.43.12-.15.16-.25.25-.42.08-.17.04-.31-.02-.43-.06-.12-.56-1.32-.76-1.8-.2-.47-.41-.4-.56-.41h-.48c-.17 0-.43.06-.66.31-.23.25-.86.83-.86 2.03 0 1.2.88 2.36 1 2.52.12.17 1.73 2.6 4.19 3.64.59.25 1.04.4 1.4.51.59.18 1.12.15 1.55.09.47-.07 1.47-.6 1.68-1.17.2-.58.2-1.07.14-1.17-.06-.11-.22-.17-.47-.29Z"/></svg>
        Fale no WhatsApp
      </a>
      <button id="menu-btn" class="md:hidden p-2" aria-label="Abrir menu" aria-expanded="false" aria-controls="mobile-menu">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
      </button>
    </div>
  </div>
  <div id="mobile-menu" class="md:hidden border-t border-soft" inert>
    <nav class="flex flex-col px-5 py-4 gap-4 text-sm font-medium">
      <a href="#sobre">Sobre</a>
      <a href="#areas">Áreas de Atuação</a>
      <a href="#porque">Por que nos escolher</a>
      <a href="#faq">Dúvidas Frequentes</a>
      <a href="https://wa.me/5551999999999" target="_blank" rel="noopener" class="btn-whatsapp text-center rounded-full px-5 py-2.5 font-semibold">Fale no WhatsApp</a>
    </nav>
  </div>
</header>

<!-- ============ HERO ============ -->
<section id="topo" class="bg-alt border-b border-soft">
  <div class="max-w-6xl mx-auto px-5 sm:px-8 py-16 sm:py-24 grid md:grid-cols-2 gap-14 items-center">
    <div>
      <span class="inline-flex items-center gap-2 text-xs font-semibold tracking-[0.14em] uppercase accent">
        <span class="fade-line"></span> Direito Previdenciário
      </span>
      <h1 class="serif text-3xl sm:text-[2.6rem] leading-[1.15] font-semibold mt-5">
        Seu benefício foi negado pelo INSS?<br class="hidden sm:block"> Você não precisa enfrentar isso sozinho.
      </h1>
      <p class="text-muted text-base sm:text-lg mt-6 max-w-xl">
        Há mais de 15 anos orientamos segurados do INSS na conquista e revisão de aposentadorias, auxílios e benefícios — com análise técnica detalhada e acompanhamento próximo em cada etapa do processo.
      </p>
      <div class="mt-9 flex flex-col sm:flex-row sm:items-center gap-4">
        <a href="https://wa.me/5551999999999?text=Ol%C3%A1%2C%20gostaria%20de%20falar%20sobre%20o%20meu%20benef%C3%ADcio%20do%20INSS."
           target="_blank" rel="noopener"
           class="btn-whatsapp inline-flex items-center justify-center gap-2 rounded-full px-7 py-3.5 font-semibold text-base transition-colors">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.37 2 11.77c0 2.06.64 3.98 1.75 5.57L2 22l4.86-1.67A10.2 10.2 0 0 0 12 21.53c5.52 0 10-4.36 10-9.76S17.52 2 12 2Zm0 17.7c-1.7 0-3.29-.47-4.64-1.29l-.33-.2-2.88.99.96-2.78-.22-.34a7.8 7.8 0 0 1-1.27-4.31c0-4.33 3.65-7.85 8.38-7.85 4.62 0 8.38 3.52 8.38 7.85 0 4.34-3.76 7.86-8.38 7.86Zm4.53-5.77c-.25-.12-1.47-.71-1.7-.79-.23-.08-.39-.12-.56.12-.17.25-.64.79-.78.95-.15.17-.29.19-.54.06-.25-.12-1.04-.37-1.98-1.19-.73-.63-1.22-1.4-1.37-1.64-.14-.25-.02-.38.11-.5.11-.11.25-.29.37-.43.12-.15.16-.25.25-.42.08-.17.04-.31-.02-.43-.06-.12-.56-1.32-.76-1.8-.2-.47-.41-.4-.56-.41h-.48c-.17 0-.43.06-.66.31-.23.25-.86.83-.86 2.03 0 1.2.88 2.36 1 2.52.12.17 1.73 2.6 4.19 3.64.59.25 1.04.4 1.4.51.59.18 1.12.15 1.55.09.47-.07 1.47-.6 1.68-1.17.2-.58.2-1.07.14-1.17-.06-.11-.22-.17-.47-.29Z"/></svg>
          Fale com um especialista
        </a>
        <span class="text-sm text-muted">Atendimento inicial por telefone, vídeo ou presencial — sem compromisso.</span>
      </div>
    </div>

    <div class="relative">
      <div class="aspect-[4/5] rounded-lg border border-soft surface flex items-center justify-center overflow-hidden">
        <img src="perfil.png" alt="">
      </div>
      <p class="text-xs text-muted mt-3 text-center">Sugestão de layout: substituir por fotografia institucional dos sócios ou do escritório.</p>
    </div>
  </div>
</section>

<!-- ============ AUTORIDADE / SOBRE ============ -->
<section id="sobre" class="max-w-6xl mx-auto px-5 sm:px-8 py-20 sm:py-28">
  <div class="grid md:grid-cols-5 gap-14">
    <div class="md:col-span-2">
      <span class="inline-flex items-center gap-2 text-xs font-semibold tracking-[0.14em] uppercase accent">
        <span class="fade-line"></span> Quem somos
      </span>
      <h2 class="serif text-2xl sm:text-3xl font-semibold mt-4 leading-tight">
        Segurança jurídica que vem da experiência
      </h2>
    </div>
    <div class="md:col-span-3">
      <p class="text-muted text-base sm:text-lg leading-relaxed">
        Fundado em Porto Alegre, o <strong class="text-current font-semibold">Corrêa Prado Advocacia</strong> dedica-se exclusivamente ao Direito Previdenciário. Ao longo de mais de uma década, construímos nossa atuação sobre um princípio simples: cada processo representa uma história de vida, e cada segurado merece ser ouvido com atenção antes de qualquer decisão jurídica.
      </p>
      <p class="text-muted text-base sm:text-lg leading-relaxed mt-5">
        Combinamos rigor técnico na análise de cada caso com uma comunicação clara e acessível, para que você entenda exatamente as possibilidades e os próximos passos do seu processo — sem promessas, sem juridiquês, sem pressa.
      </p>

      <div class="grid sm:grid-cols-3 gap-6 mt-10 pt-10 border-t border-soft">
        <div>
          <p class="serif text-2xl font-semibold accent">Atuação exclusiva</p>
          <p class="text-sm text-muted mt-1">Dedicação integral ao Direito Previdenciário, sem dispersão entre outras áreas.</p>
        </div>
        <div>
          <p class="serif text-2xl font-semibold accent">Todo o RS</p>
          <p class="text-sm text-muted mt-1">Atendimento presencial e remoto a segurados em todo o Rio Grande do Sul.</p>
        </div>
        <div>
          <p class="serif text-2xl font-semibold accent">Caso a caso</p>
          <p class="text-sm text-muted mt-1">Estudo individual de cada processo, sem respostas padronizadas.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ ÁREAS DE ATUAÇÃO ============ -->
<section id="areas" class="bg-alt border-y border-soft">
  <div class="max-w-6xl mx-auto px-5 sm:px-8 py-20 sm:py-28">
    <div class="max-w-2xl">
      <span class="inline-flex items-center gap-2 text-xs font-semibold tracking-[0.14em] uppercase accent">
        <span class="fade-line"></span> Áreas de Atuação
      </span>
      <h2 class="serif text-2xl sm:text-3xl font-semibold mt-4 leading-tight">Como podemos orientar o seu caso</h2>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-12">
      <!-- Card 1 -->
      <div class="card rounded-lg p-7">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.6" aria-hidden="true"><path d="M4 21V9l8-6 8 6v12"/><path d="M9 21v-7h6v7"/></svg>
        <h3 class="serif text-lg font-semibold mt-5">Aposentadorias</h3>
        <p class="text-sm text-muted mt-2 leading-relaxed">Análise do tempo de contribuição e da estratégia mais adequada para requerer aposentadoria por idade, tempo de contribuição ou especial.</p>
      </div>
      <!-- Card 2 -->
      <div class="card rounded-lg p-7">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.6" aria-hidden="true"><path d="M12 21s-7-4.5-9.5-9C.7 8.2 2.6 4 6.5 4c2 0 3.4 1.1 4.2 2.4C11.5 5.1 12.9 4 14.9 4 18.8 4 20.7 8.2 18.9 12c-2.5 4.5-6.9 9-6.9 9Z"/></svg>
        <h3 class="serif text-lg font-semibold mt-5">Benefícios por Incapacidade</h3>
        <p class="text-sm text-muted mt-2 leading-relaxed">Auxílio-doença, aposentadoria por invalidez e apoio na preparação para perícias médicas do INSS.</p>
      </div>
      <!-- Card 3 -->
      <div class="card rounded-lg p-7">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.6" aria-hidden="true"><path d="M21 12a9 9 0 1 1-3-6.7"/><path d="M21 3v6h-6"/></svg>
        <h3 class="serif text-lg font-semibold mt-5">Revisão de Benefícios</h3>
        <p class="text-sm text-muted mt-2 leading-relaxed">Verificação técnica se o valor do seu benefício foi calculado corretamente pelo INSS, com base no seu histórico contributivo.</p>
      </div>
      <!-- Card 4 -->
      <div class="card rounded-lg p-7">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        <h3 class="serif text-lg font-semibold mt-5">BPC / LOAS</h3>
        <p class="text-sm text-muted mt-2 leading-relaxed">Orientação a idosos e pessoas com deficiência em situação de vulnerabilidade social na busca pelo benefício assistencial.</p>
      </div>
      <!-- Card 5 -->
      <div class="card rounded-lg p-7">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.6" aria-hidden="true"><path d="M4 4v16h16"/><path d="M8 15l4-5 3 3 5-6"/></svg>
        <h3 class="serif text-lg font-semibold mt-5">Recursos Administrativos e Judiciais</h3>
        <p class="text-sm text-muted mt-2 leading-relaxed">Atuação em recursos ao INSS e em ações judiciais contra negativas, suspensões ou cessações indevidas de benefício.</p>
      </div>
      <!-- Card 6 -->
      <div class="card rounded-lg p-7">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.6" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 9h8M8 13h8M8 17h4"/></svg>
        <h3 class="serif text-lg font-semibold mt-5">Planejamento Previdenciário</h3>
        <p class="text-sm text-muted mt-2 leading-relaxed">Simulação de cenários para identificar o momento e a modalidade mais vantajosa para a sua aposentadoria futura.</p>
      </div>
    </div>
  </div>
</section>

<!-- ============ POR QUE NOS ESCOLHER ============ -->
<section id="porque" class="max-w-6xl mx-auto px-5 sm:px-8 py-20 sm:py-28">
  <div class="max-w-2xl">
    <span class="inline-flex items-center gap-2 text-xs font-semibold tracking-[0.14em] uppercase accent">
      <span class="fade-line"></span> Por que nos escolher
    </span>
    <h2 class="serif text-2xl sm:text-3xl font-semibold mt-4 leading-tight">Atenção humana, critério técnico</h2>
  </div>

  <div class="grid sm:grid-cols-2 gap-x-10 gap-y-10 mt-12">
    <div class="flex gap-4">
      <span class="shrink-0 w-9 h-9 rounded-full border border-soft flex items-center justify-center accent serif font-semibold">1</span>
      <div>
        <h3 class="font-semibold">Atendimento humanizado</h3>
        <p class="text-sm text-muted mt-1.5 leading-relaxed">Você conversa diretamente com quem acompanha o seu processo, sem intermediários e sem respostas automáticas.</p>
      </div>
    </div>
    <div class="flex gap-4">
      <span class="shrink-0 w-9 h-9 rounded-full border border-soft flex items-center justify-center accent serif font-semibold">2</span>
      <div>
        <h3 class="font-semibold">Agilidade no retorno</h3>
        <p class="text-sm text-muted mt-1.5 leading-relaxed">Resposta rápida às suas dúvidas e acompanhamento ativo de prazos e movimentações processuais.</p>
      </div>
    </div>
    <div class="flex gap-4">
      <span class="shrink-0 w-9 h-9 rounded-full border border-soft flex items-center justify-center accent serif font-semibold">3</span>
      <div>
        <h3 class="font-semibold">Rigor técnico</h3>
        <p class="text-sm text-muted mt-1.5 leading-relaxed">Cada caso é estudado individualmente, com base na legislação previdenciária atualizada e na jurisprudência aplicável.</p>
      </div>
    </div>
    <div class="flex gap-4">
      <span class="shrink-0 w-9 h-9 rounded-full border border-soft flex items-center justify-center accent serif font-semibold">4</span>
      <div>
        <h3 class="font-semibold">Transparência</h3>
        <p class="text-sm text-muted mt-1.5 leading-relaxed">Você entende cada etapa do processo, e os honorários são combinados com clareza antes do início de qualquer atuação.</p>
      </div>
    </div>
  </div>
</section>

<!-- ============ FAQ ============ -->
<section id="faq" class="bg-alt border-y border-soft">
  <div class="max-w-3xl mx-auto px-5 sm:px-8 py-20 sm:py-28">
    <div class="text-center">
      <span class="inline-flex items-center gap-2 text-xs font-semibold tracking-[0.14em] uppercase accent justify-center">
        <span class="fade-line"></span> Dúvidas Frequentes <span class="fade-line" style="background:linear-gradient(270deg, var(--accent), transparent);"></span>
      </span>
      <h2 class="serif text-2xl sm:text-3xl font-semibold mt-4">Perguntas que ouvimos com frequência</h2>
    </div>

    <div id="faq-list" class="mt-12 divide-y border-t border-b border-soft">
      <details class="py-5" open>
        <summary class="flex items-center justify-between gap-4 font-semibold">
          Quanto custa a consulta inicial?
          <svg class="chev shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
        </summary>
        <p class="text-sm text-muted mt-3 leading-relaxed">O primeiro contato é feito sem compromisso, para entendermos sua situação e explicarmos como podemos ajudar. Os honorários são apresentados com transparência antes do início de qualquer atuação.</p>
      </details>
      <details class="py-5">
        <summary class="flex items-center justify-between gap-4 font-semibold">
          Preciso ir até o escritório pessoalmente?
          <svg class="chev shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
        </summary>
        <p class="text-sm text-muted mt-3 leading-relaxed">Não necessariamente. Atendemos por telefone, videochamada ou presencialmente, conforme sua preferência e disponibilidade.</p>
      </details>
      <details class="py-5">
        <summary class="flex items-center justify-between gap-4 font-semibold">
          Quanto tempo demora um processo no INSS?
          <svg class="chev shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
        </summary>
        <p class="text-sm text-muted mt-3 leading-relaxed">O prazo varia conforme o tipo de benefício, a complexidade do caso e a via administrativa ou judicial escolhida. Após analisar sua documentação, explicamos as expectativas realistas para a sua situação.</p>
      </details>
      <details class="py-5">
        <summary class="flex items-center justify-between gap-4 font-semibold">
          Meu benefício foi negado. Ainda há o que fazer?
          <svg class="chev shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
        </summary>
        <p class="text-sm text-muted mt-3 leading-relaxed">Sim. Uma negativa do INSS pode ser questionada por meio de recurso administrativo ou ação judicial. É importante avaliar os motivos da negativa o quanto antes, dentro dos prazos legais.</p>
      </details>
      <details class="py-5">
        <summary class="flex items-center justify-between gap-4 font-semibold">
          Como funciona o primeiro contato?
          <svg class="chev shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
        </summary>
        <p class="text-sm text-muted mt-3 leading-relaxed">Você entra em contato pelo WhatsApp, conta brevemente sua situação e agendamos uma conversa para análise do seu caso.</p>
      </details>
    </div>
  </div>
</section>

<!-- ============ CTA FINAL ============ -->
<section class="max-w-6xl mx-auto px-5 sm:px-8 py-20 sm:py-24 text-center">
  <h2 class="serif text-2xl sm:text-3xl font-semibold max-w-xl mx-auto leading-tight">Ainda tem dúvidas sobre o seu caso?</h2>
  <p class="text-muted mt-4 max-w-lg mx-auto">Fale com nossa equipe e entenda, sem compromisso, quais são as possibilidades para a sua situação previdenciária.</p>
  <a href="https://wa.me/5551999999999?text=Ol%C3%A1%2C%20gostaria%20de%20falar%20sobre%20o%20meu%20benef%C3%ADcio%20do%20INSS."
     target="_blank" rel="noopener"
     class="btn-whatsapp inline-flex items-center gap-2 rounded-full px-8 py-3.5 font-semibold mt-8">
    Fale com um especialista
  </a>
</section>

<!-- ============ RODAPÉ ============ -->
<footer class="border-t border-soft bg-alt">
  <div class="max-w-6xl mx-auto px-5 sm:px-8 py-16 grid sm:grid-cols-2 md:grid-cols-4 gap-10">
    <div>
      <span class="serif text-lg font-semibold">Corrêa Prado Advocacia</span>
      <p class="text-sm text-muted mt-3 leading-relaxed">Advogado responsável: Dr. Ricardo Corrêa Prado<br>OAB/RS nº 000.000</p>
    </div>
    <div>
      <h4 class="text-xs font-semibold tracking-[0.14em] uppercase text-muted">Navegação</h4>
      <nav class="flex flex-col gap-2 mt-4 text-sm">
        <a href="#sobre">Sobre</a>
        <a href="#areas">Áreas de Atuação</a>
        <a href="#porque">Por que nos escolher</a>
        <a href="#faq">Dúvidas Frequentes</a>
      </nav>
    </div>
    <div>
      <h4 class="text-xs font-semibold tracking-[0.14em] uppercase text-muted">Contato</h4>
      <div class="flex flex-col gap-2 mt-4 text-sm text-muted">
        <span>Rua Exemplo, 123 — Porto Alegre/RS</span>
        <span>(51) 99999-9999</span>
        <span>contato@correaprado.adv.br</span>
      </div>
    </div>
    <div>
      <h4 class="text-xs font-semibold tracking-[0.14em] uppercase text-muted">Institucional</h4>
      <nav class="flex flex-col gap-2 mt-4 text-sm">
        <a href="#">Política de Privacidade</a>
        <a href="#">Termos de Uso</a>
      </nav>
    </div>
  </div>
  <div class="border-t border-soft">
    <div class="max-w-6xl mx-auto px-5 sm:px-8 py-6 flex flex-col sm:flex-row gap-3 justify-between text-xs text-muted">
      <p>&copy; 2026 Corrêa Prado Advocacia. Todos os direitos reservados.</p>
      <p class="max-w-xl">Site com caráter meramente informativo, em conformidade com o Código de Ética e Disciplina da OAB e o Provimento 205/2021. As informações aqui apresentadas não constituem consulta jurídica nem garantia de resultado.</p>
    </div>
  </div>
</footer>

<script src="script.js"></script>
</body>
</html>
