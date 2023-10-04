# Vivic

Welcome to **Vivic**, a data synchronization and e-commerce integration project built using Laravel, Vue 3, PostgreSQL, and the WooCommerce API. This project serves as a robust data hub that bridges the gap between WooCommerce and your Laravel/Vue application. It includes both a backend and a frontend component for a fully-fledged online store.

## Project Overview

**Vivic** primarily functions as a powerful data intermediary, enabling seamless integration and synchronization between your Laravel/Vue application and WooCommerce. Key features and functionalities include:

- **Data Hub**: Laravel and Vue serve as containers for data sourced from WooCommerce, including products, coupons, orders, and more.

- **Database Population**: The project continuously updates your PostgreSQL database with the latest data from WooCommerce, ensuring that your local data is always up-to-date.

- **WooCommerce Sync**: Orders placed on WooCommerce are seamlessly synchronized with your WordPress instance, providing real-time order tracking and management.

- **Payment Gateways**: Integration with popular payment gateways such as Stripe and PayPal for secure and convenient online transactions.

- **Admin Dashboard**: A comprehensive dashboard provides valuable statistics, analytics, and CRUD operations to effectively manage your e-commerce operations.


## Project Structure

The project is organized into the following directories:

- **backend**: Contains the Laravel backend of the project, which serves as the API for the store.
- **frontend/ecom-vue**: Contains the Vue 3 code for the storefront, where customers can browse and purchase products.

## Features

- **E-commerce Functionality**: The project offers all the features of a typical online store, including product listings, shopping cart, checkout, and order management.

- **WooCommerce Integration**: It integrates with the WooCommerce API to sync product data to and from WooCommerce, ensuring up-to-date product information.

- **Admin Dashboard**: A comprehensive admin dashboard is provided for store management, including CRUD operations and insightful statistics

## Prerequisites

Before getting started, ensure you have the following installed:

- Laravel: [Laravel Installation Guide](https://laravel.com/docs/installation)
- Vue CLI: [Vue CLI Installation Guide](https://cli.vuejs.org/guide/installation.html)
- PostgreSQL: [PostgreSQL Downloads](https://www.postgresql.org/download/)
- WooCommerce API Key: You'll need API keys for WooCommerce integration.
- ![Laravel](https://img.shields.io/badge/-Laravel-FF2D20?logo=laravel&logoColor=white)
- ![PHP](https://img.shields.io/badge/-PHP-777BB4?logo=php&logoColor=white)
- ![HTTP](https://img.shields.io/badge/-HTTP-005C97?logo=http&logoColor=white)

## Getting Started

### Backend

1. Navigate to the `backend` directory.

2. Install Laravel dependencies:

   ```bash
   composer install
   ```
3. Go .env file and configure your PostgreSQL database settings

4. Migrate the database:

   ```bash
   php artisan migrate
   ```
4. Run server:

   ```bash
   php artisan serve
   ```

## Product Synchronization

To populate your database with product data, follow these steps:

1. Access the admin dashboard at "http://localhost/dashboard"

2. Log in with administrator credentials.

3. In the admin dashboard, look for a "Products " or similar feature that allows you to manually trigger the products synchronization process.

4. Follow the on-screen instructions to sync products from WooCommerce. You need to provide WooCommerce API keys or other necessary details during this process.

Once the synchronization is complete, your database will be populated with product data.