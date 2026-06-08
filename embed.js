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
    const scripts = document.querySelectorAll('script');
    for (let i = scripts.length - 1; i >= 0; i--) {
      if (scripts[i].src && scripts[i].src.includes('embed.js')) {
        return scripts[i];
      }
    }
    throw new Error('Unable to determine widget script URL');
  };

  const getBaseUrl = (replacement) => {
    const url = new URL(getCurrentScript().src);
    url.pathname = url.pathname.replace(/\/embed\.js$/, replacement);
    url.search = '';
    return url.toString();
  };

  const API_BASE_URL = getBaseUrl('/api/embed.php');
  const UI_BASE_URL = getBaseUrl('/api/embed-ui.php');

  let uiPromise = null;
  function getUI() {
    if (!uiPromise) {
      uiPromise = queueFetch(UI_BASE_URL).then(r => {
        if (!r.ok) throw new Error(`UI HTTP ${r.status}`);
        return r.json();
      });
    }
    return uiPromise;
  }

  function renderTemplate(template, data) {
    return template.replace(/{{(\w+)}}/g, (_, key) => {
      if (key === 'url') {
        try {
          const url = new URL(data.url || '');
          UTM_QUERY.split('&').forEach(p => {
            const [k, v] = p.split('=');
            if (k) url.searchParams.set(k, v || '');
          });
          return escapeHtml(url.toString());
        } catch (e) {
          return '#';
        }
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

  const sharedObserver = new IntersectionObserver(async (entries) => {
    for (const entry of entries) {
      if (!entry.isIntersecting) continue;
      
      sharedObserver.unobserve(entry.target);
      
      const el = entry.target;
      if (el.dataset.loaded === '1') continue;
      el.dataset.loaded = '1';

      const productId = el.dataset.id;
      if (!productId) {
        el.shadowRoot.innerHTML = INVALID_PRODUCT_HTML;
        return;
      }

      const dataUrl = `${API_BASE_URL}?id=${productId}`;
      el.shadowRoot.innerHTML = LOADING_HTML;

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
        const html = renderTemplate(ui.template, data);
        
        el.shadowRoot.innerHTML = `<style>${ui.styles || ''}</style>${html}`;

      } catch (err) {
        console.error('ProductPreview load error:', err);
        el.shadowRoot.innerHTML = ERROR_HTML;
      }
    }
  }, { rootMargin: '200px' });

  class ProductPreview extends HTMLElement {
    constructor() {
      super();
      this.attachShadow({ mode: 'open' });
    }

    connectedCallback() {
      sharedObserver.observe(this);
    }

    disconnectedCallback() {
      sharedObserver.unobserve(this);
    }
  }

  if (!customElements.get('product-preview')) {
    customElements.define('product-preview', ProductPreview);
  }
})();