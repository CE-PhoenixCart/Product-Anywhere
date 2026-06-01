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
CSS,

  'template' => <<<HTML
<div class="pc-widget">
  <img class="pc-image" src="{{image}}" alt="{{name}}" loading="lazy">

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