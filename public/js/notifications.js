(function () {
  'use strict';

  var root = document.getElementById('sf-notify');
  if (!root) return;

  var toggle = document.getElementById('sf-notify-toggle');
  var panel = document.getElementById('sf-notify-panel');
  var list = document.getElementById('sf-notify-list');
  var badge = document.getElementById('sf-notify-badge');
  var readAllBtn = document.getElementById('sf-notify-readall');
  var csrf = document.querySelector('meta[name="csrf-token"]');
  csrf = csrf ? csrf.getAttribute('content') : '';

  var POLL_MS = 20000;
  var open = false;
  var lastUnread = 0;

  function endpoints() {
    return {
      index: '/notifications',
      readAll: '/notifications/read-all',
      read: function (id) {
        return '/notifications/' + encodeURIComponent(id) + '/read';
      }
    };
  }

  function request(url, options) {
    options = options || {};
    var headers = {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    };
    if (options.method && options.method.toUpperCase() !== 'GET') {
      headers['X-CSRF-TOKEN'] = csrf;
    }
    return fetch(url, {
      method: options.method || 'GET',
      headers: headers,
      credentials: 'same-origin',
      body: options.body || null
    }).then(function (response) {
      if (!response.ok) {
        throw new Error('notify_http_' + response.status);
      }
      return response.json();
    });
  }

  function setBadge(count) {
    lastUnread = count || 0;
    if (!badge) return;
    if (lastUnread > 0) {
      badge.hidden = false;
      badge.classList.remove('is-empty');
      badge.textContent = lastUnread > 99 ? '99+' : String(lastUnread);
    } else {
      badge.hidden = true;
      badge.classList.add('is-empty');
      badge.textContent = '0';
    }
  }

  function escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function renderList(notifications) {
    if (!list) return;

    if (!notifications || !notifications.length) {
      list.innerHTML = '<p class="sf-notify-empty">Sin notificaciones</p>';
      return;
    }

    list.innerHTML = notifications.map(function (item) {
      var unreadClass = item.read_at ? '' : ' is-unread';
      return (
        '<a class="sf-notify-item' + unreadClass + '" href="' + escapeHtml(item.url) + '" data-id="' + escapeHtml(item.id) + '">' +
          '<span class="sf-notify-item__title">' + escapeHtml(item.title) + '</span>' +
          '<span class="sf-notify-item__body">' + escapeHtml(item.body) + '</span>' +
          '<span class="sf-notify-item__time">' + escapeHtml(item.created_at_human || '') + '</span>' +
        '</a>'
      );
    }).join('');
  }

  function refresh() {
    return request(endpoints().index + '?limit=15')
      .then(function (data) {
        setBadge(data.unread_count || 0);
        renderList(data.notifications || []);
        return data;
      })
      .catch(function () {
        /* silencioso en polling */
      });
  }

  function setOpen(next) {
    open = !!next;
    if (panel) {
      if (panel.classList.contains('dropdown-menu')) {
        panel.style.display = open ? 'block' : 'none';
        panel.classList.toggle('show', open);
      } else {
        panel.hidden = !open;
      }
    }
    if (toggle) {
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    root.classList.toggle('is-open', open);
    if (open) {
      refresh();
    }
  }

  if (toggle) {
    toggle.addEventListener('click', function (event) {
      event.preventDefault();
      event.stopPropagation();
      setOpen(!open);
    });
  }

  if (list) {
    list.addEventListener('click', function (event) {
      var item = event.target.closest('.sf-notify-item');
      if (!item) return;
      var id = item.getAttribute('data-id');
      if (!id) return;

      request(endpoints().read(id), { method: 'POST' })
        .then(function (data) {
          setBadge(data.unread_count || 0);
          item.classList.remove('is-unread');
        })
        .catch(function () { /* ignore */ });
    });
  }

  if (readAllBtn) {
    readAllBtn.addEventListener('click', function (event) {
      event.preventDefault();
      event.stopPropagation();
      request(endpoints().readAll, { method: 'POST' })
        .then(function () {
          setBadge(0);
          refresh();
        })
        .catch(function () { /* ignore */ });
    });
  }

  document.addEventListener('click', function (event) {
    if (!open) return;
    if (!event.target.closest('#sf-notify')) {
      setOpen(false);
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && open) {
      setOpen(false);
    }
  });

  refresh();
  window.setInterval(refresh, POLL_MS);
})();
