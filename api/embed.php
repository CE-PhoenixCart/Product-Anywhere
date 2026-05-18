<?php
/*
  Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2026 Phoenix Cart

  Released under the GNU General Public License
*/

chdir('../');
require 'includes/application_top.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Cache-Control: public, max-age=300');
header('Vary: Origin');

$site_url = $GLOBALS['Linker']->build('index.php', [], false);

$products_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($products_id < 1) {
  http_response_code(400);
  echo json_encode([
    'error' => 'Missing or invalid product ID',
    'site_url' => $site_url
  ], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);
  exit;
}

$product = product_by_id::build($products_id);

if (!isset($product) || !($product instanceof Product) || !$product->get('status')) {
  http_response_code(404);
  echo json_encode([
    'error' => 'Product not found',
    'site_url' => $site_url
  ], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);
  exit;
}

$clean_description = clean_output($product->get('description'));
$words = preg_split('/\s+/', $clean_description);
if (count($words) > 21) {
  $clean_description = implode(' ', array_slice($words, 0, 21)) . '...';
}

$sku = $product->get('model') ?: $product->get('id');
$products_image = $product->get('image');
$stock = max(0, (int)$product->get('in_stock'));

$data = [
  'version' => 1,
  'id' => (int)$product->get('id'),
  'sku' => (string)$sku,
  'name' => clean_output($product->get('name')),
  'image' => !empty($products_image)
    ? $GLOBALS['Linker']->build("images/$products_image", [], false)
    : null,
  'url' => $GLOBALS['Linker']->build('product_info.php', ['products_id' => (int)$product->get('id')], false),
  'description' => $clean_description,
  'price' => $GLOBALS['currencies']->format($product->get('price')),
  'special' => $GLOBALS['currencies']->format($product->get('base_price')),
  'availability' => ($stock > 0) ? 'In Stock' : 'Out of Stock',
];

// optional fields, may or may not exist
$model = $product->get('model');
if (!Text::is_empty($model ?? '')) {
  $data['mpn'] = (string)$model;
}

$reviews = $product->get('reviews');
if (count($reviews) > 0) {
  $data['review_rating'] = round((float)$product->get('review_rating'), 2);
  $data['review_count'] = (int)count($reviews);
}

function clean_output($text) {
  if (empty($text)) {
    return '';
  }

  $text = strip_tags($text);
  $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  $text = preg_replace('/\s+/', ' ', $text);

  return trim($text);
}

// output
echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_THROW_ON_ERROR);
exit;