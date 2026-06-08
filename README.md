# Product Anywhere

**Product Anywhere** is a Phoenix Cart feature that lets you surface and sell products dynamically across any part of your store or even external pages using a simple identifier rather than fixed category placement. 

Instead of manually duplicating product blocks or being constrained to category/product-page templates, you can inject product data into CMS pages, blog content, landing pages, or custom modules on demand, with Phoenix Cart resolving the product context automatically and rendering it consistently wherever it appears. 

The intent is to decouple merchandising from rigid catalogue structure, so products become portable content components that can be embedded, reused, and targeted contextually without rewriting core templates or duplicating data.

## Use

```
<product-preview data-id="123"></product-preview>
<script src="https://yourshop.com/shop/embed.js" defer></script>
```

- Replace 123 with a valid Product ID  
- Replace https://yourshop.com/shop/ with your shop url
