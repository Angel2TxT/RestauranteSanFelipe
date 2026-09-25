(function () {
  'use strict';

  var root = document.getElementById('sf-cashier');
  if (!root) return;

  var feedUrl = root.getAttribute('data-feed-url');
  var csrf = root.getAttribute('data-csrf') || '';
  var readyEl = document.getElementById('cashier-ready');
  var paidEl = document.getElementById('cashier-paid');
  var countReady = document.getElementById('cashier-count-ready');
  var countPaid = document.getElementById('cashier-count-paid');
  var badgeReady = document.getElementById('cashier-badge-ready');
  var badgePaid = document.getElementById('cashier-badge-paid');
  var liveLabel = document.getElementById('cashier-live-label');

  var payModal = document.getElementById('sf-pay-modal');
  var payTitle = document.getElementById('sf-pay-title');
  var payDue = document.getElementById('sf-pay-due');
  var payReceived = document.getElementById('sf-pay-received');
  var payChange = document.getElementById('sf-pay-change');
  var payChangeLabel = document.getElementById('sf-pay-change-label');
  var payChangeBox = document.getElementById('sf-pay-change-box');
  var payConfirm = document.getElementById('sf-pay-confirm');

  var lastReadyId = 0;
  var busy = {};
  var POLL_MS = 5000;
  var payState = null;
  var previewTimer = null;
  var previewSeq = 0;

  function escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function money(n) {
    return '$' + Number(n || 0).toFixed(2);
  }

  function ageLabel(minutes) {
    if (minutes <= 0) return 'Ahora';
    if (minutes === 1) return '1 min';
    return minutes + ' min';
  }

  function renderItems(items) {
    if (!items || !items.length) {
      return '<li>Sin productos</li>';
    }
    return items.map(function (item) {
      return '<li><span class="sf-k-card__qty">×' + escapeHtml(item.qty) + '</span> ' +
        escapeHtml(item.name) +
        ' <em>$' + escapeHtml(item.price) + '</em></li>';
    }).join('');
  }

  function renderCard(order, mode) {
    var actionLabel = mode === 'ready' ? 'Cobrar' : 'Completar';
    var actionClass = mode === 'ready' ? 'sf-k-card__btn--charge' : 'sf-k-card__btn--done';
    var notes = order.notes
      ? '<div class="sf-k-card__notes"><strong>Nota:</strong> ' + escapeHtml(order.notes) + '</div>'
      : '';
    var phone = order.phone
      ? '<div class="sf-cashier__phone"><i class="fas fa-phone"></i> ' + escapeHtml(order.phone) + '</div>'
      : '';
    var paymentInfo = '';
    if (mode === 'paid' && order.payment) {
      paymentInfo = '<div class="sf-cashier__payment-info">' +
        'Recibido $' + escapeHtml(order.payment.amount_received) +
        ' · Cambio $' + escapeHtml(order.payment.change_given) +
        '</div>';
    }

    return (
      '<article class="sf-k-card sf-cashier-card" data-id="' + order.id + '" data-total="' + escapeHtml(order.total_raw) + '">' +
        '<div class="sf-k-card__top">' +
          '<div class="sf-k-card__id">#' + order.id + '</div>' +
          '<div class="sf-k-card__age">' + escapeHtml(ageLabel(order.age_minutes)) + '</div>' +
        '</div>' +
        '<div class="sf-k-card__meta">' +
          escapeHtml(order.type) +
          (order.created_at ? ' · ' + escapeHtml(order.created_at) : '') +
        '</div>' +
        '<div class="sf-k-card__customer">' + escapeHtml(order.customer) + '</div>' +
        phone +
        '<ul class="sf-k-card__items">' + renderItems(order.items) + '</ul>' +
        notes +
        paymentInfo +
        '<div class="sf-k-card__foot sf-cashier-card__foot">' +
          '<strong class="sf-cashier__total">$' + escapeHtml(order.total) + '</strong>' +
          '<div class="sf-cashier-card__actions">' +
            '<a class="sf-cashier__ticket" href="' + escapeHtml(order.ticket_url) + '" target="_blank" rel="noopener" title="Ticket">' +
              '<i class="fas fa-print"></i>' +
            '</a>' +
            '<button type="button" class="sf-k-card__btn ' + actionClass + '" data-mode="' + mode + '" data-url="' + escapeHtml(order.status_url) + '" data-charge-url="' + escapeHtml(order.charge_url || '') + '" data-preview-url="' + escapeHtml(order.preview_url || '') + '">' +
              actionLabel +
            '</button>' +
          '</div>' +
        '</div>' +
      '</article>'
    );
  }

  function renderList(el, orders, mode) {
    if (!orders || !orders.length) {
      el.innerHTML = '<div class="sf-kitchen__empty">' +
        (mode === 'ready' ? 'Sin órdenes por cobrar' : 'Sin órdenes pagadas') +
        '</div>';
      return;
    }
    el.innerHTML = orders.map(function (order) {
      return renderCard(order, mode);
    }).join('');
  }

  function setCounts(counts) {
    var r = counts.ready || 0;
    var p = counts.paid || 0;
    if (countReady) countReady.textContent = String(r);
    if (countPaid) countPaid.textContent = String(p);
    if (badgeReady) badgeReady.textContent = String(r);
    if (badgePaid) badgePaid.textContent = String(p);
  }

  function beep() {
    try {
      var Ctx = window.AudioContext || window.webkitAudioContext;
      if (!Ctx) return;
      var ctx = new Ctx();
      var osc = ctx.createOscillator();
      var gain = ctx.createGain();
      osc.type = 'triangle';
      osc.frequency.value = 660;
      gain.gain.value = 0.05;
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.start();
      setTimeout(function () {
        osc.stop();
        ctx.close();
      }, 160);
    } catch (e) { /* ignore */ }
  }

  function toast(message, isError) {
    if (typeof window.sfAdminToast === 'function') {
      window.sfAdminToast(message, !!isError);
    }
  }

  function applyPreview(data) {
    if (!payState || !data) return;

    if (typeof data.amount_due === 'number') {
      payState.total = data.amount_due;
      payDue.textContent = '$' + (data.amount_due_formatted || Number(data.amount_due).toFixed(2));
    }

    payChangeBox.classList.remove('is-ok', 'is-short', 'is-exact');
    payChangeLabel.textContent = data.label || 'Cambio';
    payChange.textContent = '$' + (data.display_amount || '0.00');

    if (data.state === 'ok') payChangeBox.classList.add('is-ok');
    if (data.state === 'short') payChangeBox.classList.add('is-short');
    if (data.state === 'exact') payChangeBox.classList.add('is-exact');

    payConfirm.disabled = !data.can_charge;
  }

  function updateChange() {
    if (!payState || !payState.previewUrl) return;

    if (previewTimer) {
      clearTimeout(previewTimer);
    }

    previewTimer = setTimeout(function () {
      var seq = ++previewSeq;
      var received = parseFloat(payReceived.value);
      if (isNaN(received)) received = 0;

      fetch(payState.previewUrl, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ amount_received: received })
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
          if (seq !== previewSeq || !payState) return;
          applyPreview(data);
        })
        .catch(function () {
          if (seq !== previewSeq) return;
          payConfirm.disabled = true;
        });
    }, 180);
  }

  function openPayModal(orderId, total, chargeUrl, previewUrl) {
    payState = {
      id: orderId,
      total: Number(total) || 0,
      url: chargeUrl,
      previewUrl: previewUrl
    };
    payTitle.textContent = 'Orden #' + orderId;
    payDue.textContent = money(payState.total);
    payReceived.value = '';
    payConfirm.disabled = true;
    payChangeLabel.textContent = 'Cambio';
    payChange.textContent = '$0.00';
    payChangeBox.classList.remove('is-ok', 'is-short', 'is-exact');
    payModal.hidden = false;
    document.body.classList.add('sf-pay-open');
    requestAnimationFrame(function () {
      payModal.classList.add('is-open');
      payReceived.focus();
    });
  }

  function closePayModal() {
    if (previewTimer) {
      clearTimeout(previewTimer);
      previewTimer = null;
    }
    previewSeq += 1;
    payModal.classList.remove('is-open');
    document.body.classList.remove('sf-pay-open');
    setTimeout(function () {
      payModal.hidden = true;
      payState = null;
    }, 180);
  }

  function advanceOrder(url, id, successMessage, payload) {
    if (!url || !id || busy[id]) return Promise.resolve();
    busy[id] = true;

    var options = {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      credentials: 'same-origin'
    };

    if (payload) {
      options.headers['Content-Type'] = 'application/json';
      options.body = JSON.stringify(payload);
    }

    return fetch(url, options)
      .then(function (response) {
        return response.json().then(function (data) {
          if (!response.ok || data.ok === false) {
            throw new Error(data.message || 'error');
          }
          return data;
        });
      })
      .then(function (data) {
        toast(data.message || successMessage || 'Orden actualizada');
        return refresh();
      })
      .finally(function () {
        delete busy[id];
      });
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
        renderList(readyEl, data.ready || [], 'ready');
        renderList(paidEl, data.paid || [], 'paid');

        var latest = data.latest_ready_id || 0;
        if (lastReadyId && latest > lastReadyId) {
          beep();
          toast('Nueva orden lista para cobrar');
        }
        lastReadyId = latest;
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
    var mode = btn.getAttribute('data-mode');
    var id = card ? card.getAttribute('data-id') : null;
    if (!url || !id || busy[id]) return;

    if (mode === 'ready') {
      var total = card.getAttribute('data-total');
      var chargeUrl = btn.getAttribute('data-charge-url') || url;
      var previewUrl = btn.getAttribute('data-preview-url') || '';
      openPayModal(id, total, chargeUrl, previewUrl);
      return;
    }

    btn.disabled = true;
    var oldText = btn.textContent;
    btn.textContent = '…';
    advanceOrder(url, id, 'Orden completada')
      .catch(function (err) {
        toast((err && err.message) || 'No se pudo actualizar', true);
        btn.disabled = false;
        btn.textContent = oldText;
      });
  });

  if (payReceived) {
    payReceived.addEventListener('input', updateChange);
    payReceived.addEventListener('keydown', function (event) {
      if (event.key === 'Enter' && !payConfirm.disabled) {
        event.preventDefault();
        payConfirm.click();
      }
    });
  }

  document.getElementById('sf-pay-quick').addEventListener('click', function (event) {
    var btn = event.target.closest('[data-amount]');
    if (!btn || !payState) return;
    var amount = btn.getAttribute('data-amount');
    if (amount === 'exact') {
      payReceived.value = payState.total.toFixed(2);
    } else {
      payReceived.value = Number(amount).toFixed(2);
    }
    updateChange();
    payReceived.focus();
  });

  payModal.addEventListener('click', function (event) {
    if (event.target.closest('[data-pay-close]')) {
      event.preventDefault();
      closePayModal();
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && payModal && !payModal.hidden) {
      closePayModal();
    }
  });

  payConfirm.addEventListener('click', function () {
    if (!payState || payConfirm.disabled) return;
    var received = parseFloat(payReceived.value) || 0;
    var id = payState.id;
    var url = payState.url;

    payConfirm.disabled = true;
    payConfirm.textContent = 'Cobrando…';

    advanceOrder(url, id, null, {
      amount_received: received,
      method: 'cash'
    })
      .then(function () {
        closePayModal();
      })
      .catch(function (err) {
        toast((err && err.message) || 'No se pudo cobrar', true);
        payConfirm.disabled = false;
        updateChange();
      })
      .finally(function () {
        payConfirm.textContent = 'Confirmar cobro';
      });
  });

  refresh();
  window.setInterval(refresh, POLL_MS);
})();
