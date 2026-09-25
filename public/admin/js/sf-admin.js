(function () {
  'use strict';

  var body = document.body;
  var backdrop = document.getElementById('sf-aside-backdrop');
  var menuBtn = document.getElementById('sf-aside-toggle');
  var TOAST_MS = 3000;

  function setAsideOpen(open) {
    body.classList.toggle('sf-aside-open', !!open);
    if (backdrop) {
      backdrop.hidden = !open;
    }
    if (menuBtn) {
      menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
  }

  if (menuBtn) {
    menuBtn.addEventListener('click', function () {
      setAsideOpen(!body.classList.contains('sf-aside-open'));
    });
  }

  if (backdrop) {
    backdrop.addEventListener('click', function () {
      setAsideOpen(false);
    });
  }

  window.addEventListener('resize', function () {
    if (window.innerWidth > 991) {
      setAsideOpen(false);
    }
  });

  function toast(message, isError) {
    var el = document.getElementById('ajax-toast');
    if (!el || !message) return;

    var msg = el.querySelector('.ajax-toast__msg');
    var icon = el.querySelector('.ajax-toast__icon');
    if (msg) {
      msg.textContent = message;
    } else {
      el.textContent = message;
    }
    if (icon) {
      icon.innerHTML = isError
        ? '<i class="fas fa-times"></i>'
        : '<i class="fas fa-check"></i>';
    }

    el.classList.toggle('is-error', !!isError);
    el.classList.toggle('is-success', !isError);
    el.hidden = false;
    el.classList.remove('is-in', 'is-out');
    void el.offsetWidth;
    el.classList.add('is-in');

    clearTimeout(toast._timer);
    clearTimeout(toast._hide);
    toast._timer = setTimeout(function () {
      el.classList.remove('is-in');
      el.classList.add('is-out');
      toast._hide = setTimeout(function () {
        el.hidden = true;
        el.classList.remove('is-out', 'is-success', 'is-error');
      }, 280);
    }, TOAST_MS);
  }

  window.sfAdminToast = toast;

  function askConfirm(options) {
    options = options || {};
    var root = document.getElementById('sf-confirm');
    if (!root) {
      return Promise.resolve(window.confirm(options.text || '¿Continuar?'));
    }

    var titleEl = document.getElementById('sf-confirm-title');
    var textEl = document.getElementById('sf-confirm-text');
    var okBtn = document.getElementById('sf-confirm-ok');
    var iconEl = root.querySelector('.sf-confirm__icon i');
    var cancelBtn = root.querySelector('[data-sf-confirm-dismiss].sf-confirm__btn');

    if (titleEl) titleEl.textContent = options.title || '¿Continuar?';
    if (textEl) textEl.textContent = options.text || '';
    if (okBtn) okBtn.textContent = options.confirmLabel || 'Sí, confirmar';
    if (cancelBtn && options.cancelLabel) cancelBtn.textContent = options.cancelLabel;
    if (iconEl) {
      iconEl.className = options.icon || 'fas fa-exclamation';
    }
    root.classList.toggle('is-danger', options.danger !== false);
    if (okBtn) {
      okBtn.classList.toggle('sf-confirm__btn--danger', options.danger !== false);
      okBtn.classList.toggle('sf-confirm__btn--primary', options.danger === false);
    }

    return new Promise(function (resolve) {
      function cleanup(result) {
        root.classList.remove('is-open');
        root.classList.add('is-closing');
        document.body.classList.remove('sf-confirm-open');
        document.removeEventListener('keydown', onKey);
        setTimeout(function () {
          root.hidden = true;
          root.classList.remove('is-closing');
          root._resolve = null;
          resolve(result);
        }, 220);
      }

      function onKey(event) {
        if (event.key === 'Escape') cleanup(false);
        if (event.key === 'Enter') cleanup(true);
      }

      root._resolve = cleanup;
      root.hidden = false;
      document.body.classList.add('sf-confirm-open');
      requestAnimationFrame(function () {
        root.classList.add('is-open');
        if (okBtn) okBtn.focus();
      });
      document.addEventListener('keydown', onKey);
    });
  }

  document.addEventListener('click', function (event) {
    var root = document.getElementById('sf-confirm');
    if (!root || root.hidden || typeof root._resolve !== 'function') return;

    if (event.target.closest('[data-sf-confirm-dismiss]')) {
      event.preventDefault();
      root._resolve(false);
      return;
    }
    if (event.target.closest('#sf-confirm-ok')) {
      event.preventDefault();
      root._resolve(true);
    }
  });

  document.addEventListener('submit', function (event) {
    var form = event.target;
    if (!(form instanceof HTMLFormElement)) return;

    if (form.classList.contains('js-confirm-delete') || form.classList.contains('delete-form') || form.classList.contains('confirm-form')) {
      event.preventDefault();

      if (form.getAttribute('data-has-products') === 'true') {
        toast('No se puede eliminar: la categoría tiene productos asociados.', true);
        return;
      }

      askConfirm({
        title: form.getAttribute('data-title') || '¿Eliminar?',
        text: form.getAttribute('data-message') || 'Esta acción no se puede deshacer.',
        confirmLabel: form.getAttribute('data-confirm-text') || 'Sí, eliminar',
        icon: 'fas fa-trash-alt',
        danger: true
      }).then(function (ok) {
        if (ok) form.submit();
      });
      return;
    }

    if (form.classList.contains('status-form') || form.classList.contains('js-confirm')) {
      var needsConfirm = form.getAttribute('data-confirm');
      if (needsConfirm === '0') return;
      if (needsConfirm === '1' || form.classList.contains('js-confirm')) {
        event.preventDefault();
        askConfirm({
          title: form.getAttribute('data-title') || 'Confirmación',
          text: form.getAttribute('data-message') || '¿Confirmas esta acción?',
          confirmLabel: form.getAttribute('data-confirm-text') || 'Sí, confirmar',
          icon: form.getAttribute('data-icon') || 'fas fa-exclamation',
          danger: form.getAttribute('data-danger') === '1'
        }).then(function (ok) {
          if (ok) form.submit();
        });
      }
    }
  });

  var flashEl = document.getElementById('sf-flash-data');
  if (flashEl) {
    var flashMsg = flashEl.getAttribute('data-msg');
    var flashError = flashEl.getAttribute('data-error');
    if (flashMsg) toast(flashMsg, false);
    if (flashError) toast(flashError, true);
  }
})();
