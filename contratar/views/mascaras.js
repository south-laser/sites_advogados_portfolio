// Máscaras de CPF e telefone no formulário
(function () {
  var cpf = document.getElementById('cpf');
  var tel = document.getElementById('telefone');

  function mask(el, fn) {
    if (el) el.addEventListener('input', function () { el.value = fn(el.value.replace(/\D/g, '')); });
  }

  mask(cpf, function (v) {
    v = v.slice(0, 11);
    return v.replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2');
  });

  mask(tel, function (v) {
    v = v.slice(0, 11);
    if (v.length <= 2) return v;
    if (v.length <= 6) return '(' + v.slice(0, 2) + ') ' + v.slice(2);
    if (v.length <= 10) return '(' + v.slice(0, 2) + ') ' + v.slice(2, 6) + '-' + v.slice(6);
    return '(' + v.slice(0, 2) + ') ' + v.slice(2, 7) + '-' + v.slice(7);
  });
})();
