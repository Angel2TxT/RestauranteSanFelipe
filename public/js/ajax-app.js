(function () {
  'use strict';

  var csrfToken = document.querySelector('meta[name="csrf-token"]');
  csrfToken = csrfToken ? csrfToken.getAttribute('content') : '';

  function toast(message, isError) {
    var el = document.getElementById('ajax-toast');
    if (!el) return;
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
    }, 3000);
  }

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

    if (titleEl) titleEl.textContent = options.title || '¿Continuar?';
    if (textEl) textEl.textContent = options.text || '';
    if (okBtn) okBtn.textContent = options.confirmLabel || 'Sí, confirmar';
    if (iconEl) {
      iconEl.className = options.icon || 'fas fa-exclamation';
    }
    root.classList.toggle('is-danger', options.danger !== false);

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

  function updateCartUI(data) {
    var count = document.getElementById('cart-count');
    if (count && typeof data.count !== 'undefined') {
      count.textContent = data.count;
      count.classList.toggle('is-empty', !data.count);
    }

    var panel = document.getElementById('cart-panel');
    if (panel && data.cart_html) {
      panel.innerHTML = data.cart_html;
    }
  }

  function requestJson(url, options) {
    options = options || {};
    var headers = options.headers || {};
    headers['X-Requested-With'] = 'XMLHttpRequest';
    headers['Accept'] = 'application/json';
    if (options.method && options.method.toUpperCase() !== 'GET') {
      headers['X-CSRF-TOKEN'] = csrfToken;
    }

    return fetch(url, {
      method: options.method || 'GET',
      headers: headers,
      body: options.body || null,
      credentials: 'same-origin'
    }).then(function (response) {
      if (!response.ok) {
        return response.json().catch(function () {
          return {};
        }).then(function (payload) {
          var err = new Error(payload.message || 'No se pudo completar la acción');
          err.payload = payload;
          throw err;
        });
      }
      return response.json();
    });
  }

  // Agregar al carrito
  document.addEventListener('click', function (event) {
    var link = event.target.closest('.js-add-to-cart');
    if (!link) return;

    event.preventDefault();
    event.stopPropagation();

    var url = link.getAttribute('data-url') || link.getAttribute('href');
    if (!url) return;

    link.classList.add('is-loading');
    requestJson(url)
      .then(function (data) {
        updateCartUI(data);
        toast(data.message || 'Producto agregado');
      })
      .catch(function () {
        toast('No se pudo agregar al carrito', true);
      })
      .finally(function () {
        link.classList.remove('is-loading');
      });
  });

  // Actualizar cantidad
  function updateQty(input) {
    var url = input.getAttribute('data-url');
    var qty = parseInt(input.value, 10);
    if (!url || !qty || qty < 1) return;

    var body = new URLSearchParams();
    body.append('qty', String(qty));
    body.append('_method', 'PATCH');
    body.append('_token', csrfToken);

    requestJson(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: body
    })
      .then(function (data) {
        updateCartUI(data);
        toast(data.message || 'Cantidad actualizada');
      })
      .catch(function () {
        toast('No se pudo actualizar la cantidad', true);
      });
  }

  document.addEventListener('change', function (event) {
    var input = event.target.closest('.js-cart-qty');
    if (!input) return;
    updateQty(input);
  });

  document.addEventListener('click', function (event) {
    var btn = event.target.closest('.js-cart-qty-step');
    if (!btn) return;

    event.preventDefault();
    var wrap = btn.closest('.cart-qty');
    if (!wrap) return;
    var input = wrap.querySelector('.js-cart-qty');
    if (!input) return;

    var step = parseInt(btn.getAttribute('data-step'), 10) || 0;
    var min = parseInt(input.getAttribute('min'), 10) || 1;
    var max = parseInt(input.getAttribute('max'), 10) || 50;
    var next = Math.min(max, Math.max(min, (parseInt(input.value, 10) || 1) + step));
    input.value = String(next);
    updateQty(input);
  });

  // Quitar del carrito
  document.addEventListener('click', function (event) {
    var link = event.target.closest('.js-cart-remove');
    if (!link) return;

    event.preventDefault();
    var url = link.getAttribute('data-url') || link.getAttribute('href');
    if (!url) return;

    requestJson(url)
      .then(function (data) {
        updateCartUI(data);
        toast(data.message || 'Producto eliminado');
      })
      .catch(function () {
        toast('No se pudo eliminar el producto', true);
      });
  });

  function updateOrderQty(input) {
    var url = input.getAttribute('data-url');
    if (!url) return;
    var qty = parseInt(input.value, 10) || 1;
    var body = new URLSearchParams();
    body.set('qty', String(qty));
    body.set('_method', 'PATCH');

    fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      credentials: 'same-origin',
      body: body
    })
      .then(function (response) {
        return response.json().then(function (data) {
          if (!response.ok || data.ok === false) {
            throw new Error(data.message || 'error');
          }
          return data;
        });
      })
      .then(function (data) {
        toast(data.message || 'Cantidad actualizada');
        window.location.href = data.redirect || window.location.href;
      })
      .catch(function (err) {
        toast((err && err.message) || 'No se pudo actualizar', true);
      });
  }

  document.addEventListener('change', function (event) {
    var input = event.target.closest('.js-order-qty');
    if (!input) return;
    updateOrderQty(input);
  });

  document.addEventListener('click', function (event) {
    var btn = event.target.closest('.js-order-qty-step');
    if (!btn) return;
    event.preventDefault();
    var wrap = btn.closest('.order-qty');
    if (!wrap) return;
    var input = wrap.querySelector('.js-order-qty');
    if (!input) return;
    var step = parseInt(btn.getAttribute('data-step'), 10) || 0;
    var min = parseInt(input.getAttribute('min'), 10) || 1;
    var max = parseInt(input.getAttribute('max'), 10) || 50;
    var next = Math.min(max, Math.max(min, (parseInt(input.value, 10) || 1) + step));
    input.value = String(next);
    updateOrderQty(input);
  });

  document.addEventListener('click', function (event) {
    var btn = event.target.closest('.js-order-item-remove');
    if (!btn) return;
    event.preventDefault();
    var url = btn.getAttribute('data-url');
    if (!url) return;

    askConfirm({
      title: 'Quitar producto',
      text: 'Este platillo se eliminará de tu orden pendiente.',
      confirmLabel: 'Sí, quitar',
      icon: 'fas fa-trash-alt',
      danger: true
    }).then(function (ok) {
      if (!ok) return;

      fetch(url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: new URLSearchParams({ _method: 'DELETE' })
      })
        .then(function (response) {
          return response.json().then(function (data) {
            if (!response.ok || data.ok === false) {
              throw new Error(data.message || 'error');
            }
            return data;
          });
        })
        .then(function (data) {
          toast(data.message || 'Producto eliminado');
          window.location.href = data.redirect || window.location.href;
        })
        .catch(function (err) {
          toast((err && err.message) || 'No se pudo eliminar', true);
        });
    });
  });

  document.addEventListener('submit', function (event) {
    var form = event.target.closest('.js-order-cancel-form, .js-order-merge-form');
    if (!form) return;
    event.preventDefault();

    var proceed = Promise.resolve(true);
    if (form.classList.contains('js-order-cancel-form')) {
      proceed = askConfirm({
        title: 'Cancelar pedido',
        text: 'Se cancelará tu orden pendiente y no se podrá recuperar.',
        confirmLabel: 'Sí, cancelar',
        icon: 'fas fa-ban',
        danger: true
      });
    }

    proceed.then(function (ok) {
      if (!ok) return;

      fetch(form.getAttribute('action'), {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        credentials: 'same-origin',
        body: new FormData(form)
      })
        .then(function (response) {
          return response.json().then(function (data) {
            if (!response.ok || data.ok === false) {
              throw new Error(data.message || 'error');
            }
            return data;
          });
        })
        .then(function (data) {
          toast(data.message || 'Listo');
          if (data.redirect) {
            window.location.href = data.redirect;
          } else {
            window.location.reload();
          }
        })
        .catch(function (err) {
          toast((err && err.message) || 'No se pudo completar la acción', true);
        });
    });
  });

  // Giro de cards (delegado, funciona tras AJAX)
  document.addEventListener('click', function (event) {
    var card = event.target.closest('.product-flip');
    if (!card) {
      document.querySelectorAll('.product-flip.is-flipped').forEach(function (el) {
        el.classList.remove('is-flipped');
      });
      return;
    }

    if (event.target.closest('a, button, input, select, textarea')) return;
    card.classList.toggle('is-flipped');
  });

  // Filtros de tienda sin recargar
  var shopForm = document.getElementById('shop-form');
  var shopResults = document.getElementById('shop-results');
  var searchTimer = null;

  function loadShop(pushState) {
    if (!shopForm || !shopResults) return;

    var action = shopForm.getAttribute('action') || window.location.pathname;
    var params = new URLSearchParams(new FormData(shopForm));
    var url = action + (params.toString() ? '?' + params.toString() : '');

    shopResults.classList.add('is-loading');

    fetch(url, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'text/html'
      },
      credentials: 'same-origin'
    })
      .then(function (response) {
        if (!response.ok) throw new Error('shop');
        return response.text();
      })
      .then(function (html) {
        shopResults.innerHTML = html;
        shopResults.classList.remove('is-loading');
        if (pushState !== false) {
          history.pushState({ shop: true }, '', url);
        }
      })
      .catch(function () {
        shopResults.classList.remove('is-loading');
        toast('No se pudieron filtrar los productos', true);
      });
  }

  if (shopForm && shopResults) {
    shopForm.addEventListener('submit', function (event) {
      event.preventDefault();
      loadShop(true);
    });

    shopForm.querySelectorAll('select').forEach(function (select) {
      select.addEventListener('change', function () {
        loadShop(true);
      });
    });

    var searchInput = document.getElementById('shop-search');
    if (searchInput) {
      searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
          loadShop(true);
        }, 350);
      });
    }

    var clearBtn = document.getElementById('shop-clear');
    if (clearBtn) {
      clearBtn.addEventListener('click', function (event) {
        event.preventDefault();
        shopForm.reset();
        var selects = shopForm.querySelectorAll('select');
        selects.forEach(function (select) {
          select.value = '';
        });
        if (searchInput) searchInput.value = '';
        loadShop(true);
      });
    }

    window.addEventListener('popstate', function () {
      // Al usar atrás/adelante, recargar contenido según URL
      var url = window.location.href;
      shopResults.classList.add('is-loading');
      fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'text/html'
        },
        credentials: 'same-origin'
      })
        .then(function (response) {
          return response.text();
        })
        .then(function (html) {
          shopResults.innerHTML = html;
          shopResults.classList.remove('is-loading');
        })
        .catch(function () {
          shopResults.classList.remove('is-loading');
        });
    });
  }
  // Navbar + carrito propios
  (function initSfHeader() {
    var navToggle = document.getElementById('sf-nav-toggle');
    var menu = document.getElementById('sf-menu');
    var cart = document.getElementById('sf-cart');
    var cartOpen = document.getElementById('sf-cart-open');
    var cartClose = document.getElementById('sf-cart-close');
    var cartBackdrop = document.getElementById('sf-cart-backdrop');
    var userToggle = document.getElementById('sf-user-toggle');
    var userMenu = document.getElementById('sf-user-menu');

    function closeMenu() {
      document.body.classList.remove('sf-menu-open');
      if (navToggle) navToggle.setAttribute('aria-expanded', 'false');
    }

    function openCart() {
      document.body.classList.add('sf-cart-open');
      if (cart) cart.setAttribute('aria-hidden', 'false');
      if (cartBackdrop) cartBackdrop.hidden = false;
      closeMenu();
    }

    function closeCart() {
      document.body.classList.remove('sf-cart-open');
      if (cart) cart.setAttribute('aria-hidden', 'true');
      if (cartBackdrop) cartBackdrop.hidden = true;
    }

    if (navToggle && menu) {
      navToggle.addEventListener('click', function () {
        var open = document.body.classList.toggle('sf-menu-open');
        navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
    }

    if (cartOpen) cartOpen.addEventListener('click', openCart);
    if (cartClose) cartClose.addEventListener('click', closeCart);
    if (cartBackdrop) cartBackdrop.addEventListener('click', closeCart);

    if (userToggle && userMenu) {
      userToggle.addEventListener('click', function (event) {
        event.stopPropagation();
        var open = userMenu.classList.toggle('is-open');
        userToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
    }

    document.addEventListener('click', function (event) {
      if (userMenu && userToggle && !event.target.closest('.sf-menu__user')) {
        userMenu.classList.remove('is-open');
        userToggle.setAttribute('aria-expanded', 'false');
      }
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeCart();
        closeMenu();
      }
    });

    // Nav flotante al hacer scroll (histéresis + lock para evitar parpadeo)
    var header = document.querySelector('.sf-header');
    if (header) {
      var floating = false;
      var ticking = false;
      var lockUntil = 0;
      var ENTER_Y = 64;
      var EXIT_Y = 10;
      var LOCK_MS = 520;

      var applyFloating = function () {
        ticking = false;
        var now = Date.now();
        if (now < lockUntil) return;

        var y = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
        var shouldFloat = floating ? (y > EXIT_Y) : (y > ENTER_Y);
        if (shouldFloat === floating) return;

        floating = shouldFloat;
        header.classList.toggle('is-floating', floating);
        header.classList.add('is-nav-animating');
        lockUntil = now + LOCK_MS;
        window.setTimeout(function () {
          header.classList.remove('is-nav-animating');
          lockUntil = 0;
          applyFloating();
        }, LOCK_MS);
      };

      var onScroll = function () {
        if (!ticking) {
          ticking = true;
          window.requestAnimationFrame(applyFloating);
        }
      };

      applyFloating();
      window.addEventListener('scroll', onScroll, { passive: true });
      document.addEventListener('scroll', onScroll, { passive: true, capture: true });
    }
  })();
})();
