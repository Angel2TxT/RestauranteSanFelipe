(function () {
  'use strict';

  var root = document.getElementById('sf-kitchen');
  if (!root) return;

  var feedUrl = root.getAttribute('data-feed-url');
  var csrf = root.getAttribute('data-csrf') || '';
  var pendingEl = document.getElementById('kitchen-pending');
  var progressEl = document.getElementById('kitchen-progress');
  var countPending = document.getElementById('kitchen-count-pending');
  var countProgress = document.getElementById('kitchen-count-progress');
  var badgePending = document.getElementById('kitchen-badge-pending');
  var badgeProgress = document.getElementById('kitchen-badge-progress');
  var liveLabel = document.getElementById('kitchen-live-label');

  var lastPendingId = 0;
  var busy = {};
  var POLL_MS = 5000;

  function escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function ageClass(minutes) {
    if (minutes >= 15) return 'is-late';
    if (minutes >= 8) return 'is-warn';
    return '';
  }

  function ageLabel(minutes) {
    if (minutes <= 0) return 'Ahora';
    if (minutes === 1) return '1 min';
    return minutes + ' min';
  }

  function renderItems(items) {
    if (!items || !items.length) {
      return '<li class="sf-k-card__empty-item">Sin productos</li>';
    }
    return items.map(function (item) {
      return '<li><span class="sf-k-card__qty">×' + escapeHtml(item.qty) + '</span> ' + escapeHtml(item.name) + '</li>';
    }).join('');
  }

  function renderCard(order, mode) {
    var actionLabel = mode === 'pending' ? 'Comenzar' : 'Marcar lista';
    var actionClass = mode === 'pending' ? 'sf-k-card__btn--start' : 'sf-k-card__btn--ready';
    var notes = order.notes
      ? '<div class="sf-k-card__notes"><strong>Nota:</strong> ' + escapeHtml(order.notes) + '</div>'
      : '';

    return (
      '<article class="sf-k-card ' + ageClass(order.age_minutes) + '" data-id="' + order.id + '">' +
        '<div class="sf-k-card__top">' +
          '<div class="sf-k-card__id">#' + order.id + '</div>' +
          '<div class="sf-k-card__age">' + escapeHtml(ageLabel(order.age_minutes)) + '</div>' +
        '</div>' +
        '<div class="sf-k-card__meta">' +
          escapeHtml(order.type) +
          (order.created_at ? ' · ' + escapeHtml(order.created_at) : '') +
        '</div>' +
        '<div class="sf-k-card__customer">' + escapeHtml(order.customer) + '</div>' +
        '<ul class="sf-k-card__items">' + renderItems(order.items) + '</ul>' +
        notes +
        '<div class="sf-k-card__foot">' +
          '<strong>$' + escapeHtml(order.total) + '</strong>' +
          '<button type="button" class="sf-k-card__btn ' + actionClass + '" data-url="' + escapeHtml(order.status_url) + '">' +
            actionLabel +
          '</button>' +
        '</div>' +
      '</article>'
    );
  }

  function renderList(el, orders, mode) {
    if (!orders || !orders.length) {
      el.innerHTML = '<div class="sf-kitchen__empty">' +
        (mode === 'pending' ? 'Sin pedidos nuevos' : 'Nada en cocina') +
        '</div>';
      return;
    }
    el.innerHTML = orders.map(function (order) {
      return renderCard(order, mode);
    }).join('');
  }

  function setCounts(counts) {
    var p = counts.pending || 0;
    var i = counts.in_progress || 0;
    if (countPending) countPending.textContent = String(p);
    if (countProgress) countProgress.textContent = String(i);
    if (badgePending) badgePending.textContent = String(p);
    if (badgeProgress) badgeProgress.textContent = String(i);
  }

  function beep() {
    try {
      var Ctx = window.AudioContext || window.webkitAudioContext;
      if (!Ctx) return;
      var ctx = new Ctx();
      var osc = ctx.createOscillator();
      var gain = ctx.createGain();
      osc.type = 'sine';
      osc.frequency.value = 880;
      gain.gain.value = 0.05;
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.start();
      setTimeout(function () {
        osc.stop();
        ctx.close();
      }, 180);
    } catch (e) { /* ignore */ }
  }

  function toast(message, isError) {
    if (typeof window.sfAdminToast === 'function') {
      window.sfAdminToast(message, !!isError);
    }
  }

  function refresh() {
    return fetch(feedUrl, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      credentials: 'same-origin'
    })
      .then(function (response) {
        if (!response.ok) throw new Error('feed');
        return response.json();
      })
      .then(function (data) {
        setCounts(data.counts || {});
        renderList(pendingEl, data.pending || [], 'pending');
        renderList(progressEl, data.in_progress || [], 'in_progress');

        var latest = data.latest_pending_id || 0;
        if (lastPendingId && latest > lastPendingId) {
          beep();
          toast('Nueva orden en cocina');
        }
        lastPendingId = latest;
        if (liveLabel) liveLabel.textContent = 'En vivo';
        root.classList.remove('is-offline');
      })
      .catch(function () {
        if (liveLabel) liveLabel.textContent = 'Reconectando…';
        root.classList.add('is-offline');
      });
  }

  root.addEventListener('click', function (event) {
    var btn = event.target.closest('.sf-k-card__btn');
    if (!btn) return;

    var card = btn.closest('.sf-k-card');
    var url = btn.getAttribute('data-url');
    var id = card ? card.getAttribute('data-id') : null;
    if (!url || !id || busy[id]) return;

    busy[id] = true;
    btn.disabled = true;
    btn.textContent = '…';

    fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      credentials: 'same-origin'
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
        toast(data.message || 'Orden actualizada');
        return refresh();
      })
      .catch(function (err) {
        toast((err && err.message) || 'No se pudo actualizar', true);
        btn.disabled = false;
      })
      .finally(function () {
        delete busy[id];
      });
  });

  refresh();
  window.setInterval(refresh, POLL_MS);
})();
