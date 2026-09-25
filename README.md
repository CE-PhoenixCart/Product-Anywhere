# Product Anywhere

**Product Anywhere** takes your product to where potential customers already are - instead of waiting for people to find your store, put your products on the sites they're already browsing.

A product you manage once in Phoenix Cart can appear on affiliate sites, a supplier's site, a blog post, a landing page, or anywhere else that can load a web page - **each one linking back to its product page so customers land on your site to buy**. You don't upload it separately or keep it up to date by hand - change it once in Phoenix Cart and every embed updates.

It's a lightweight web component (a custom HTML element) built for Phoenix Cart - no plugin or separate app needed on the other website.

The other website doesn't need to run Phoenix Cart, share your hosting, or belong to your business. It just needs a small snippet of code, which you generate with one click - no developer required.

<img src="https://raw.githubusercontent.com/CE-PhoenixCart/Product-Anywhere/master/.github/Network.png" alt="Product Anywhere Network Diagram">

## Live Demo

**See Product Anywhere in Action**

Experience a live embedded card directly on the official Phoenix Cart homepage:

<div align="center">
<a href="https://phoenixcart.org/index.php">
  <img src="https://raw.githubusercontent.com/CE-PhoenixCart/Product-Anywhere/master/.github/demo.png" alt="Product Anywhere Card Demo">
</a>

*Click the preview above or visit [phoenixcart.org](https://phoenixcart.org/index.php) to see the live integration.*

</div>

## Table of Contents

* [Getting Your Embed Code](#getting-your-embed-code)
* [Where You Can Use It](#where-you-can-use-it)
* [Why Product Anywhere?](#why-product-anywhere)
* [Turning It On](#turning-it-on)
* [Is My Data Safe?](#is-my-data-safe)
* [Getting Help](#getting-help)
* [For Developers](#for-developers)

## Getting Your Embed Code

From any product's page in your Phoenix Cart, generate the embed snippet for that product - no need to write any code or look up product IDs yourself. Copy it, paste it into the other website, and the product appears there automatically, linking back to that product's page on your store.

## Where You Can Use It

- Affiliate and partner websites
- Manufacturer and supplier websites
- Review and community websites
- Blogs and landing pages
- Marketing campaigns
- Any website you or a partner can add a bit of code to

## Why Product Anywhere?

Customers don't only find products on your store. They find them on the sites they're already reading - a blog, a supplier's page, an affiliate's review. Normally, getting your product in front of them there means duplicating the listing by hand and hoping it stays accurate.

Product Anywhere puts your product directly on those sites instead, with the listing always pulled live from your Phoenix Cart - same price, same photo, same stock level, wherever it appears. You manage it once; it shows up everywhere it's useful, and every visitor who clicks it lands back on your store to buy.

## Turning It On

1. Download the latest release.
2. Upload the files to your Phoenix Cart installation.
3. Turn on the Product Info Layout Module at Your Admin > Modules > Layout

Once that's done, you're ready to start generating embed codes for your products.

## Is My Data Safe?

- Product Anywhere only shows information that's already public on your store - nothing private is exposed.
- No customer data or pricing calculations are shared.
- It uses the same product permissions your store already has, so anything you've hidden or restricted stays that way.

## Getting Help

- [Phoenix Forum](https://phoenixcart.org/forum/index.php)
- [GitHub Discussions](https://github.com/CE-PhoenixCart/Product-Anywhere/discussions)

## For Developers

If you want to embed a product by hand instead of using the generator, or you're integrating this into a build:

```html
<product-preview data-id="123"></product-preview>
<script src="https://yourshop.com/shop/embed.js" defer></script>
```

Replace `123` with a valid Product ID and `https://yourshop.com/shop/` with your Phoenix Cart store URL.

**Under the hood:** Shadow DOM encapsulation keeps embedded markup and styles isolated from the host page. Lazy-loading via IntersectionObserver means embeds only fetch data once in view. A concurrency-limited fetch queue prevents pages with many embeds from overloading your store's API. Referrer-based UTM attribution and server-side click tracking show you where embedded-product traffic is coming from.

Pull requests are welcome — for major changes, please open a discussion first.
