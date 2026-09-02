<?php
/*
  Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2026 Phoenix Cart

  Released under the GNU General Public License
*/

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=3600, must-revalidate');

echo json_encode([
  'styles' => <<<CSS
:host {
  display: inline-block;
}

.pc-widget {
  border: 1px solid #dee2e6;
  border-radius: 0.375rem;
  background: #fff;
  width: 100%;
  max-width: 18rem;
  font-family: system-ui, sans-serif;
  overflow: hidden;
}

:host, .pc-widget, .pc-widget *, .pc-widget *::before, .pc-widget *::after {
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
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  box-sizing: border-box;
  padding: 8px 28px;
  font-size: 15px;
  font-weight: 500;
  text-decoration: none;
  background: #6c757d;
  color: #fff;
  border-radius: 6px;
}

.pc-link:hover {
  background: #5a6268;
}
pc-signal-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 8px;
  background: rgba(0, 0, 0, 0.1); 
  border-radius: 20px;
  border: 1px solid rgba(255, 255, 255, 0.6); 
  color: #fff;
  font-family: system-ui, -apple-system, sans-serif;
  font-size: 0.7rem;
  font-weight: 600;
  letter-spacing: 0.03em;
  z-index: 10;
  line-height: 1;
}

.pc-wifi-icon {
  width: 14px;
  height: 14px;
  display: inline-block;
  vertical-align: middle;
}

.pc-wifi-icon .dot {
  fill: #fff;
}

.pc-wifi-icon .arc-inner {
  stroke: #fff;
  stroke-width: 2;
  stroke-linecap: round;
  fill: none;
  animation: wifi-pulse 1.6s ease-in-out infinite;
  animation-delay: 0.2s;
}

.pc-wifi-icon .arc-outer {
  stroke: #fff;
  stroke-width: 2;
  stroke-linecap: round;
  fill: none;
  animation: wifi-pulse 1.6s ease-in-out infinite;
  animation-delay: 0.4s;
}

@keyframes wifi-pulse {
  0%, 100% {
    opacity: 0.2;
  }
  50% {
    opacity: 1;
  }
}
CSS,

  'template' => <<<HTML
<div class="pc-widget">
  <div class="pc-image-wrap" style="position: relative;">
    <img class="pc-image" src="{{image}}" alt="{{name}}" loading="lazy">
    <div class="pc-signal-badge" title="Live store connection active">
      <svg class="pc-wifi-icon" viewBox="0 0 24 24">
        <circle class="dot" cx="12" cy="19" r="1.5" />
        <path class="arc-inner" d="M8.5 14.5A5 5 0 0 1 15.5 14.5" />
        <path class="arc-outer" d="M5 11A10 10 0 0 1 19 11" />
      </svg>
      <span>LIVE</span>
    </div>
  </div>

  <div class="pc-body">
    <h5 class="pc-title">{{name}}</h5>

    <div class="pc-price">{{price}} &middot; {{availability}}</div>

    <p class="pc-description">{{description}}</p>

    <a class="pc-link" href="{{url}}" target="_blank" rel="noopener noreferrer nofollow">
      View Product
    </a>
  </div>
</div>
HTML
], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_THROW_ON_ERROR);