// Language switch (Telugu / English). Both languages are already in the page; this only flips which one shows.
document.querySelectorAll('[data-lang-toggle]').forEach(function (b) {
  b.addEventListener('click', function () {
    var h = document.documentElement, next = h.dataset.lang === 'te' ? 'en' : 'te';
    h.dataset.lang = next; h.lang = next;
    try { localStorage.setItem('ck-lang', next); } catch (e) {}
  });
});
