* ============================================================
   Corrêa Prado Advocacia — Landing Page
   Comportamento: apenas o menu mobile (abrir/fechar) e o
   fechamento automático ao clicar em um link. O accordion do
   FAQ usa <details>/<summary> nativo do HTML, sem necessidade de JS.
   ============================================================ */
 
document.addEventListener('DOMContentLoaded', () => {
  const menuBtn = document.getElementById('menu-btn');
  const mobileMenu = document.getElementById('mobile-menu');
 
  if (menuBtn && mobileMenu) {
    const setMenuOpen = (open) => {
      mobileMenu.classList.toggle('open', open);
      menuBtn.setAttribute('aria-expanded', String(open));
      menuBtn.setAttribute('aria-label', open ? 'Fechar menu' : 'Abrir menu');
      // Sem isso, os links do menu "fechado" (altura 0, mas ainda no DOM)
      // continuavam focáveis por Tab e visíveis para leitores de tela.
      if (open) {
        mobileMenu.removeAttribute('inert');
      } else {
        mobileMenu.setAttribute('inert', '');
      }
    };
 
    menuBtn.addEventListener('click', () => {
      setMenuOpen(!mobileMenu.classList.contains('open'));
    });
 
    mobileMenu.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', () => setMenuOpen(false));
    });
 
    // Estado inicial explícito (garante consistência mesmo se o HTML
    // for editado depois sem o atributo inert já presente).
    setMenuOpen(false);
  }
});