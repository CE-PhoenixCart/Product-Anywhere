<div class="<?= PI_EMBED_CONTENT_WIDTH ?> pi-embed">
  <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#embedModal">
    <?= PI_EMBED_BUTTON_TEXT ?>
  </button>
</div>


<?php
// <!-- push modal to footer -->
$embed_modal_title = PI_EMBED_MODAL_TITLE;
$embed_modal_close = PI_EMBED_MODAL_CLOSE;
// embed
$embed_modal_preview_title = PI_EMBED_MODAL_PREVIEW_TITLE;
$embed_modal_preview_subtitle = PI_EMBED_MODAL_PREVIEW_SUBTITLE;
// code
$pi_embed_modal_code_title = PI_EMBED_MODAL_CODE_TITLE;
$pi_embed_modal_code_subtitle = PI_EMBED_MODAL_CODE_SUBTITLE;
$pi_embed_modal_code_steps = PI_EMBED_MODAL_CODE_STEPS;
$pi_embed_modal_code_box_title = PI_EMBED_MODAL_CODE_BOX_TITLE;
$pi_embed_modal_button_copy = PI_EMBED_MODAL_BUTTON_COPY;
$pi_embed_modal_copy_success = PI_EMBED_MODAL_COPY_SUCCESS;
$pi_embed_modal_help_box_title = PI_EMBED_MODAL_HELP_BOX_TITLE;
$pi_embed_modal_help_box_text = PI_EMBED_MODAL_HELP_BOX_TEXT;
$pi_embed_modal_help_box_supported = PI_EMBED_MODAL_HELP_BOX_SUPPORTED;

$embed_modal = <<<EOM
<div class="modal fade" id="embedModal" tabindex="-1" aria-labelledby="embedModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5 ps-2" id="embedModalLabel">$embed_modal_title</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="$embed_modal_close"></button>
      </div>
      <div class="modal-body">
      
        <div class="row g-0">
          <div class="col-lg-4 p-4 embed-preview-side">
            <div class="embed-preview-title">
              {$embed_modal_preview_title}
            </div>
            <div class="embed-preview-subtitle">
              {$embed_modal_preview_subtitle}
            </div>
            <div class="embed-preview-box">
              <div class="product-preview" data-id="{$product_id}"></div>
              <script src="{$script_url}" defer></script>
            </div>
          </div>
          <div class="col-lg-8 p-4 embed-code-side">
            <div class="embed-code-title">
              {$pi_embed_modal_code_title}
            </div>
            <div class="embed-code-subtitle">
              {$pi_embed_modal_code_subtitle}
            </div>
            {$pi_embed_modal_code_steps}
            <div class="embed-code-box">
              <div class="embed-code-bar">
                {$pi_embed_modal_code_box_title}
              </div>
              <code id="embed-code">&lt;div class="product-preview" data-id="{$product_id}"&gt;&lt;/div&gt;
&lt;script src="{$script_url}" defer&gt;&lt;/script&gt;</code>
            </div>
              
            <div class="embed-copy-row">
              <button id="copy-btn" class="btn btn-success rounded-4">
                {$pi_embed_modal_button_copy}
              </button>
              <div id="copy-msg" class="d-none">
                {$pi_embed_modal_copy_success}
              </div>
            </div>

            <div class="embed-help-box">
              <div class="embed-code-title">
                {$pi_embed_modal_help_box_title}
              </div>
              {$pi_embed_modal_help_box_text}
              <div class="embed-supported">
                {$pi_embed_modal_help_box_supported}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

EOM;

$GLOBALS['Template']->add_block($embed_modal, 'footer_scripts');

// <!-- push css to footer -->
$embed_css = <<<EOCSS
<style>
/* MODAL BASE */
#embedModal .modal-content {
  border: 0;
  border-radius: 1.25rem;
  overflow: hidden;
  box-shadow: 0 25px 70px rgba(0, 0, 0, 0.18);
}

#embedModal .modal-header {
  border-bottom: 1px solid #f1f3f5;
  background: #fff;
}

#embedModal .modal-title {
  font-weight: 700;
  color: #212529;
}

#embedModal .modal-body {
  padding: 0;
}

/* LAYOUT */
.embed-preview-side {
  background: radial-gradient(circle at top left, #fff7ed 0%, #fff 50%);
  min-height: 100%;
  border-right: 1px solid #f1f3f5;
}

.embed-code-side {
  background: linear-gradient(180deg, #fff 0%, #fafbff 100%);
}

/* TYPOGRAPHY */
.embed-preview-title,
.embed-code-title {
  font-weight: 500;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #8b5cf6;
  margin-bottom: 0.5rem;
}

.embed-preview-subtitle,
.embed-code-subtitle {
  color: #6c757d;
  line-height: 1.2;
  margin-bottom: 1rem;
}

/* PREVIEW */
.embed-preview-box {
  display: flex;
  justify-content: center;
  align-items: flex-start;
  margin-top: 0.5rem;
}

/* CODE BOX */
.embed-code-box {
  position: relative;
  background: linear-gradient(135deg, #f8f9ff 0%, #eef4ff 100%);
  border-radius: 1rem;
  overflow: hidden;
  border: 1px solid #dbe4ff;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
}

.embed-code-bar {
  padding: 0.9rem 1.2rem;
  color: #6366f1;
  border-bottom: 1px solid #dbe4ff;
  background: rgba(255, 255, 255, 0.5);
}

#embed-code {
  font-size: 0.84rem;
  background: transparent;
  white-space: pre-wrap;
  display: block;
  padding: 1rem 1.2rem;
  margin: 0;
}

/* STEPS */
.embed-steps {
  padding-left: 1.2rem;
  margin-bottom: 1.5rem;
  color: #6c757d;
  font-size: 0.92rem;
}

.embed-steps li {
  margin-bottom: 0.5rem;
}

/* COPY BUTTON & MESSAGE */
.embed-copy-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-top: 1rem;
  flex-wrap: wrap;
}

#copy-msg {
  margin: 0;
  padding: 0.2rem 1rem;
  border-radius: 999px;
  background: #ecfdf5;
  color: #047857;
  border: 1px solid #a7f3d0;
}

/* HELP BOX */
.embed-help-box {
  background: #fff;
  border: 1px solid #ececff;
  border-radius: 1rem;
  padding: 1rem 1.1rem;
  margin-top: 1.5rem;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
}

.embed-supported {
  font-size: 0.9rem;
  margin-top: 1rem;
  color: #6c757d;
}

.embed-supported i {
  color: #8b5cf6;
  margin-right: 0.35rem;
}

/* RESPONSIVE */
@media (max-width: 767px) {
  .embed-preview-side {
    border-right: 0;
    border-bottom: 1px solid #f1f3f5;
  }
}
</style>

EOCSS;

$GLOBALS['Template']->add_block($embed_css, 'footer_scripts');

// <!-- push copy JS to footer -->
$embed_js = <<<EOJ
<script>
document.getElementById('copy-btn').addEventListener('click', async function() {
  const codeElement = document.getElementById('embed-code');
  const rawText = codeElement.innerText; 
  
  try {
    await navigator.clipboard.writeText(rawText);
    const msg = document.getElementById('copy-msg');
    msg.classList.remove('d-none');
    setTimeout(() => msg.classList.add('d-none'), 15000);
  } catch (err) {
    alert('Failed to copy');
  }
});
</script>

EOJ;

$GLOBALS['Template']->add_block($embed_js, 'footer_scripts');
?>

<?php
/*
  Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2026 Phoenix Cart

  Released under the GNU General Public License
*/
?>
