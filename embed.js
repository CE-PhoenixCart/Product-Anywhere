/*
  Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2026 Phoenix Cart

  Released under the GNU General Public License
*/

(function () {
  'use strict';

  const CACHE_TIME = 300000;
  const MAX_CACHE_ENTRIES = 100;
  const FETCH_TIMEOUT = 5000;
  const MAX_CONCURRENT = 3;

  const HTML_ESCAPES = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
  };

  const ERROR_HTML = `<p style="color:#dc3545;font-size:0.875rem;">Failed to load</p>`;
  const INVALID_PRODUCT_HTML = `<p style="color:#dc3545;font-size:0.9rem;">Invalid product ID</p>`;
  const LOADING_HTML = `<p style="font-size:0.9rem;color:#6c757d;">Loading…</p>`;
  // Get the current script being executed
  const current_script = document.currentScript || (() => {
    const scripts = document.querySelectorAll('script[src*="embed.js"]');
    return scripts[scripts.length - 1];
  })();

  const LANGUAGE = (() => {
    if (!current_script) {
      return 'en';
    }

    const url = new URL(current_script.src);

    return (
      url.searchParams.get('lang') ||
      'en'
    ).toLowerCase().split('-')[0];
  })();

  const STYLES = `
  .pc-widget {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    background: #fff;
    width: 100%;
    max-width: 18rem;
    font-family: system-ui, sans-serif;
    overflow: hidden;
  }

  .pc-widget *, .pc-widget *::before, .pc-widget *::after {
    box-sizing: border-box;
  }

  .pc-image { width: 100%; display: block; }

  .pc-body { padding: 1rem; }

  .pc-title {
    margin: 0 0 0.5rem;
    font-size: 1.25rem;
    font-weight: 700;
    color: #212529;
  }

  .pc-price {
    margin: 0 0 0.5rem;
    font-size: 0.95rem;
    font-weight: 600;
    color: #212529;
  }

  .pc-price .special {
    color: #dc3545;
  }

  .pc-description {
    margin: 0 0 1rem;
    font-size: 0.9rem;
    color: #495057;
  }

  .pc-link {
    color: #0d6efd;
    text-decoration: none;
    font-weight: 500;
  }
`;

  if (!document.getElementById('pc-widget-styles')) {
    const style = document.createElement('style');
    style.id = 'pc-widget-styles';
    style.textContent = STYLES;
    document.head.appendChild(style);
  }

  const TEMPLATE = `
  <div class="pc-widget">
    <img class="pc-image" src="{{image}}" alt="{{name}}" loading="lazy">

    <div class="pc-body">
      <h5 class="pc-title">{{name}}</h5>
      
      <div class="pc-price">{{price}} &middot; {{availability}}</div>

      <p class="pc-description">{{description}}</p>

      <a class="pc-link" href="{{url}}" target="_blank" rel="noopener noreferrer nofollow">
        {{view_product}} ->
      </a>
    </div>
  </div>
`;

  function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, s => HTML_ESCAPES[s]);
  }

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
  
  const API_BASE_URL = (() => {
    if (!current_script) {
      throw new Error('Script not found');
    }

    const url = new URL(current_script.src);

    url.pathname = url.pathname.replace(/\/embed\.js$/, '/api/embed.php');

    url.search = '';

    return url.toString();
  })();

  async function loadCard(container) {
    if (container.dataset.loaded === '1') return;
    container.dataset.loaded = '1';  
    
    const productId = container.dataset.id;
    
    if (!productId) {
      container.innerHTML = INVALID_PRODUCT_HTML;
      return;
    }
    
    const apiUrl = `${API_BASE_URL}?id=${productId}&lang=${encodeURIComponent(LANGUAGE)}`;

    container.innerHTML = LOADING_HTML;
  
    try {
      let data;
      
      const cached = cache.get(apiUrl);
      
      if (cached && (Date.now() - cached.timestamp < CACHE_TIME)) {
        data = cached.data;
      } else {
        const response = await queueFetch(apiUrl);

        if (!response.ok) {
          throw new Error(`HTTP ${response.status}`);
        }

        const contentType = response.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
          throw new Error('Invalid content type');
        }

        const json = await response.json();

        data = json;
        cache.set(apiUrl, { data, timestamp: Date.now() });
        trimCache(cache);
      }
      
      const html = renderTemplate(TEMPLATE, data);
      const tpl = document.createElement('template');
      tpl.innerHTML = html;
      container.replaceChildren(tpl.content.cloneNode(true));
    } catch {
      container.innerHTML = ERROR_HTML;
    }
  }

  function renderTemplate(template, data) {
    const finalUrl = new URL(data.url);
    finalUrl.searchParams.forEach(() => {}); 
    UTM_QUERY.split('&').forEach(p => {
      const [k, v] = p.split('=');
      if (k) finalUrl.searchParams.set(k, v || '');
    });

    return template.replace(/{{(\w+)}}/g, (_, key) => {
      if (key === 'url') return escapeHtml(finalUrl.toString());
      
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