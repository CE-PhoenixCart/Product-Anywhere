<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2026 Phoenix Cart

  Released under the GNU General Public License
*/

class hook_admin_catalog_productAnywhere {

  function listen_infoBox($parameters) {
    global $product;
    
    if (isset($product) && ($product instanceof Product)) {
      $product_id = (int)$product->get('id');
      $script_url = $GLOBALS['Linker']->build('embed.js', [], false);
      
      $embed_snippet = htmlspecialchars('<product-preview data-id="' . $product_id . '"></product-preview>' . "\n" . '<script src="' . $script_url . '" defer></script>');
      
      $anywhere_label = ANYWHERE_LABEL;
      $anywhere_copy = ANYWHERE_COPY;
      $anywhere_copied = ANYWHERE_COPIED;
      
      $html = <<<HTML
<div class="mb-3">
  <label class="form-label font-weight-bold text-muted small uppercase">{$anywhere_label}</label>
  <div class="position-relative">
    <textarea id="embedCodeInput_{$product_id}" class="form-control form-control-sm font-monospace text-nowrap" rows="3" readonly style="font-size: 0.75rem; background-color: #f8f9fa; resize: none;">{$embed_snippet}</textarea>
    <button type="button" id="copyEmbedBtn_{$product_id}" onclick="copyEmbedCode_{$product_id}()" class="btn btn-sm btn-outline-primary mt-1 w-100">
      {$anywhere_copy}
    </button>
  </div>
</div>

<script>
function copyEmbedCode_{$product_id}() {
  const codeArea = document.getElementById('embedCodeInput_{$product_id}');
  const btn = document.getElementById('copyEmbedBtn_{$product_id}');
  const originalText = btn.innerHTML;
  
  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard.writeText(codeArea.value).then(showSuccess).catch(fallbackCopy);
  } else {
    fallbackCopy();
  }

  function fallbackCopy() {
    codeArea.select();
    document.execCommand('copy');
    showSuccess();
  }

  function showSuccess() {
    btn.className = 'btn btn-sm btn-success mt-1 w-100';
    btn.innerHTML = '$anywhere_copied';
    setTimeout(() => {
      btn.className = 'btn btn-sm btn-outline-primary mt-1 w-100';
      btn.innerHTML = originalText;
    }, 2000);
  }
}
</script>
HTML;

      $parameters['contents'][] = ['text' => $html];
    }
  }

}
