# Vue storefront

This is a Vue 3 + Vite storefront served by Laravel at `http://localhost:8000/`.
The original Laravel Mix/Vue 2 assets remain unchanged.

## Run locally

1. From this directory, run `npm install` once.
2. From the repository root, run `npm run dev` to build the storefront into
   `public/frontend`.
3. Run `php artisan serve --port=8000` and open `http://localhost:8000/`.

The API client defaults to `/api/v2`, matching the product and category routes
in `routes/api.php`. Cart endpoints require a Sanctum bearer token and customer
ID in browser local storage as `ecom_access_token` and `ecom_user_id`.
`VITE_COST_MATRIX` is forwarded to the existing cart-add endpoint, which
requires that value in this backend. Use `npm run frontend:dev` to run the
standalone Vite development server instead.

Run `npm run frontend:build` to rebuild the Laravel-served bundle.

## Store features

- Browse categories, products, brands, deals, shops, and product variants.
- Register, verify email, sign in, reset a password, update a profile, and
  manage shipping addresses.
- Use the wishlist, product reviews, chat with a seller, and view purchase
  history, order details, refund requests, wallet activity, and club points.
- Check out with the payment methods enabled by the Laravel business settings.
  Manual methods support transfer reference and optional proof image upload.
- Delivery staff can sign in through the account screen and use the delivery
  dashboard to update assigned orders, collections, and earnings.

`src/services/endpoints.js` contains the grouped client for every route in
`routes/api.php`: authentication, catalog, commerce, customer account, chat,
delivery, and payment gateway/callback endpoints. Provider credentials and
enabled methods remain configured by Laravel. Payment return/callback routes
are called by the configured provider after the browser leaves checkout.
