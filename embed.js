/*
  Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2026 Phoenix Cart

  Released under the GNU General Public License
*/

(function () {
  'use strict';

  const CACHE_TIME = 60000;
  const MAX_CACHE_ENTRIES = 100;
  const FETCH_TIMEOUT = 5000;
  const MAX_CONCURRENT = 3;

  const ERROR_HTML = `<p style="color:#dc3545;font-size:0.875rem;">Failed to load</p>`;
  const INVALID_PRODUCT_HTML = `<p style="color:#dc3545;font-size:0.9rem;">Invalid product ID</p>`;
  const LOADING_HTML = `<p style="font-size:0.9rem;color:#6c757d;">Loading…</p>`;
  
  const UTM_QUERY = (() => {
    const params = new URLSearchParams();
    params.set('utm_medium', 'card');
    params.set('utm_campaign', 'embed');

    let source = 'direct';

    if (document.referrer) {
      try {
        source = new URL(document.referrer).hostname;
      } catch {}
    }

    params.set('utm_source', source);
    return params.toString();
  })();

  const cache = new Map();

  function trimCache(map) {
    while (map.size > MAX_CACHE_ENTRIES) {
      const first = map.keys().next().value;
      map.delete(first);
    }
  }

  let activeRequests = 0;
  const fetchQueue = [];

  function queueFetch(url) {
    return new Promise((resolve, reject) => {
      fetchQueue.push({ url, resolve, reject });
      processQueue();
    });
  }

  function processQueue() {
    while (activeRequests < MAX_CONCURRENT && fetchQueue.length) {
      const item = fetchQueue.shift();
      activeRequests++;

      const controller = new AbortController();
      const timeoutId = setTimeout(() => controller.abort(), FETCH_TIMEOUT);

      fetch(item.url, {
        signal: controller.signal,
        credentials: 'omit',
      })
        .then(item.resolve)
        .catch(item.reject)
        .finally(() => {
          clearTimeout(timeoutId);
          activeRequests--;
          processQueue();
        });
    }
  }

  function escapeHtml(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  const getCurrentScript = () => {
    if (document.currentScript) return document.currentScript;
    throw new Error('Unable to determine widget script URL');
  };

  const API_BASE_URL = (() => {
    const url = new URL(getCurrentScript().src);
    url.pathname = url.pathname.replace(/\/embed\.js$/, '/api/embed.php');
    url.search = '';
    return url.toString();
  })();
  
  let uiPromise = null;

  const UI_BASE_URL = (() => {
    const url = new URL(getCurrentScript().src);
    url.pathname = url.pathname.replace(/\/embed\.js$/, '/api/embed-ui.php');
    url.search = '';
    return url.toString();
  })();

  function ensureStyles(css) {
    let style = document.getElementById('pc-widget-styles');
    if (!style) {
      style = document.createElement('style');
      style.id = 'pc-widget-styles';
      document.head.appendChild(style);
    }
    style.textContent = css || '';
  }

  function renderTemplate(template, data) {
    return template.replace(/{{(\w+)}}/g, (_, key) => {
      if (key === 'url') {
        const url = new URL(data.url);

        UTM_QUERY.split('&').forEach(p => {
          const [k, v] = p.split('=');
          if (k) url.searchParams.set(k, v || '');
        });

        return escapeHtml(url.toString());
      }

      if (key === 'price') {
        const price = data.price || '';
        const special = data.special || '';

        if (special && special !== price) {
          return `<s>${escapeHtml(price)}</s> <span class="special">${escapeHtml(special)}</span>`;
        }
        return escapeHtml(price);
      }

      return escapeHtml(data[key] ?? '');
    });
  }
  
  function getUI() {
    if (!uiPromise) {
      uiPromise = queueFetch(UI_BASE_URL).then(r => {
        if (!r.ok) throw new Error(`UI HTTP ${r.status}`);
        return r.json();
      });
    }
    return uiPromise;
  }

  async function loadCard(container) {
    if (container.dataset.loaded === '1') return;
    container.dataset.loaded = '1';

    const productId = container.dataset.id;
    if (!productId) {
      container.innerHTML = INVALID_PRODUCT_HTML;
      return;
    }

    const dataUrl = `${API_BASE_URL}?id=${productId}`;

    container.innerHTML = LOADING_HTML;

    try {
      let data;

      const cached = cache.get(dataUrl);
      if (cached && (Date.now() - cached.timestamp < CACHE_TIME)) {
        data = cached.data;
      } else {
        const response = await queueFetch(dataUrl);
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        data = await response.json();

        cache.set(dataUrl, { data, timestamp: Date.now() });
        trimCache(cache);
      }

      const ui = await getUI();
      ensureStyles(ui.styles);

      const html = renderTemplate(ui.template, data);
      container.innerHTML = html;
    } catch {
      container.innerHTML = ERROR_HTML;
    }
  }

  const widgets = document.querySelectorAll('.product-preview');
  if (!widgets.length) return;

  const observer = new IntersectionObserver(entries => {
    for (const entry of entries) {
      if (!entry.isIntersecting) continue;
      observer.unobserve(entry.target);
      loadCard(entry.target);
    }
  }, { rootMargin: '200px' });

  widgets.forEach(w => observer.observe(w));

})();