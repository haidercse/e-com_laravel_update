<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessSettingsTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('business_settings')->truncate();
        DB::table('business_settings')->insert([
  0 => 
  [
    'id' => 1,
    'type' => 'home_default_currency',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2018-10-16 03:35:52',
    'updated_at' => '2019-01-28 02:26:53',
  ],
  1 => 
  [
    'id' => 2,
    'type' => 'system_default_currency',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2018-10-16 03:36:58',
    'updated_at' => '2020-01-26 05:22:13',
  ],
  2 => 
  [
    'id' => 3,
    'type' => 'currency_format',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2018-10-17 05:01:59',
    'updated_at' => '2018-10-17 05:01:59',
  ],
  3 => 
  [
    'id' => 4,
    'type' => 'symbol_format',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2018-10-17 05:01:59',
    'updated_at' => '2019-01-20 03:10:55',
  ],
  4 => 
  [
    'id' => 5,
    'type' => 'no_of_decimals',
    'value' => '2',
    'lang' => NULL,
    'created_at' => '2018-10-17 05:01:59',
    'updated_at' => '2020-03-04 01:57:16',
  ],
  5 => 
  [
    'id' => 6,
    'type' => 'product_activation',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2018-10-28 02:38:37',
    'updated_at' => '2019-02-04 02:11:41',
  ],
  6 => 
  [
    'id' => 7,
    'type' => 'vendor_system_activation',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2018-10-28 08:44:16',
    'updated_at' => '2019-02-04 02:11:38',
  ],
  7 => 
  [
    'id' => 8,
    'type' => 'show_vendors',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2018-10-28 08:44:47',
    'updated_at' => '2019-02-04 02:11:13',
  ],
  8 => 
  [
    'id' => 9,
    'type' => 'paypal_payment',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2018-10-28 08:45:16',
    'updated_at' => '2019-01-31 06:09:10',
  ],
  9 => 
  [
    'id' => 10,
    'type' => 'stripe_payment',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2018-10-28 08:45:47',
    'updated_at' => '2018-11-14 02:51:51',
  ],
  10 => 
  [
    'id' => 11,
    'type' => 'cash_payment',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2018-10-28 08:46:05',
    'updated_at' => '2019-01-24 04:40:18',
  ],
  11 => 
  [
    'id' => 12,
    'type' => 'payumoney_payment',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2018-10-28 08:46:27',
    'updated_at' => '2019-03-05 06:41:36',
  ],
  12 => 
  [
    'id' => 13,
    'type' => 'best_selling',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2018-12-24 09:13:44',
    'updated_at' => '2019-02-14 06:29:13',
  ],
  13 => 
  [
    'id' => 14,
    'type' => 'paypal_sandbox',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2019-01-16 13:44:18',
    'updated_at' => '2019-01-16 13:44:18',
  ],
  14 => 
  [
    'id' => 15,
    'type' => 'sslcommerz_sandbox',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2019-01-16 13:44:18',
    'updated_at' => '2019-03-14 01:07:26',
  ],
  15 => 
  [
    'id' => 16,
    'type' => 'sslcommerz_payment',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2019-01-24 10:39:07',
    'updated_at' => '2019-01-29 07:13:46',
  ],
  16 => 
  [
    'id' => 17,
    'type' => 'vendor_commission',
    'value' => '20',
    'lang' => NULL,
    'created_at' => '2019-01-31 07:18:04',
    'updated_at' => '2019-04-13 08:49:26',
  ],
  17 => 
  [
    'id' => 18,
    'type' => 'verification_form',
    'value' => '[{"type":"text","label":"Your name"},{"type":"text","label":"Shop name"},{"type":"text","label":"Email"},{"type":"text","label":"License No"},{"type":"text","label":"Full Address"},{"type":"text","label":"Phone Number"},{"type":"file","label":"Tax Papers"}]',
    'lang' => NULL,
    'created_at' => '2019-02-03 12:36:58',
    'updated_at' => '2019-02-16 07:14:42',
  ],
  18 => 
  [
    'id' => 19,
    'type' => 'google_analytics',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2019-02-06 13:22:35',
    'updated_at' => '2019-02-06 13:22:35',
  ],
  19 => 
  [
    'id' => 20,
    'type' => 'facebook_login',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2019-02-07 13:51:59',
    'updated_at' => '2019-02-08 20:41:15',
  ],
  20 => 
  [
    'id' => 21,
    'type' => 'google_login',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2019-02-07 13:52:10',
    'updated_at' => '2019-02-08 20:41:14',
  ],
  21 => 
  [
    'id' => 22,
    'type' => 'twitter_login',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2019-02-07 13:52:20',
    'updated_at' => '2019-02-08 03:32:56',
  ],
  22 => 
  [
    'id' => 23,
    'type' => 'payumoney_payment',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2019-03-05 12:38:17',
    'updated_at' => '2019-03-05 12:38:17',
  ],
  23 => 
  [
    'id' => 24,
    'type' => 'payumoney_sandbox',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2019-03-05 12:38:17',
    'updated_at' => '2019-03-05 06:39:18',
  ],
  24 => 
  [
    'id' => 36,
    'type' => 'facebook_chat',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2019-04-15 13:45:04',
    'updated_at' => '2019-04-15 13:45:04',
  ],
  25 => 
  [
    'id' => 37,
    'type' => 'email_verification',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2019-04-30 09:30:07',
    'updated_at' => '2019-04-30 09:30:07',
  ],
  26 => 
  [
    'id' => 38,
    'type' => 'wallet_system',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2019-05-19 10:05:44',
    'updated_at' => '2019-05-19 04:11:57',
  ],
  27 => 
  [
    'id' => 39,
    'type' => 'coupon_system',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2019-06-11 11:46:18',
    'updated_at' => '2019-06-11 11:46:18',
  ],
  28 => 
  [
    'id' => 40,
    'type' => 'current_version',
    'value' => '5.5.4',
    'lang' => NULL,
    'created_at' => '2019-06-11 11:46:18',
    'updated_at' => '2019-06-11 11:46:18',
  ],
  29 => 
  [
    'id' => 41,
    'type' => 'instamojo_payment',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2019-07-06 11:58:03',
    'updated_at' => '2019-07-06 11:58:03',
  ],
  30 => 
  [
    'id' => 42,
    'type' => 'instamojo_sandbox',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2019-07-06 11:58:43',
    'updated_at' => '2019-07-06 11:58:43',
  ],
  31 => 
  [
    'id' => 43,
    'type' => 'razorpay',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2019-07-06 11:58:43',
    'updated_at' => '2019-07-06 11:58:43',
  ],
  32 => 
  [
    'id' => 44,
    'type' => 'paystack',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2019-07-21 15:00:38',
    'updated_at' => '2019-07-21 15:00:38',
  ],
  33 => 
  [
    'id' => 45,
    'type' => 'pickup_point',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2019-10-17 13:50:39',
    'updated_at' => '2019-10-17 13:50:39',
  ],
  34 => 
  [
    'id' => 46,
    'type' => 'maintenance_mode',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2019-10-17 13:51:04',
    'updated_at' => '2019-10-17 13:51:04',
  ],
  35 => 
  [
    'id' => 47,
    'type' => 'voguepay',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2019-10-17 13:51:24',
    'updated_at' => '2019-10-17 13:51:24',
  ],
  36 => 
  [
    'id' => 48,
    'type' => 'voguepay_sandbox',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2019-10-17 13:51:38',
    'updated_at' => '2019-10-17 13:51:38',
  ],
  37 => 
  [
    'id' => 50,
    'type' => 'category_wise_commission',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2020-01-21 08:22:47',
    'updated_at' => '2020-01-21 08:22:47',
  ],
  38 => 
  [
    'id' => 51,
    'type' => 'conversation_system',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2020-01-21 08:23:21',
    'updated_at' => '2020-01-21 08:23:21',
  ],
  39 => 
  [
    'id' => 52,
    'type' => 'guest_checkout_active',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2020-01-22 08:36:38',
    'updated_at' => '2020-01-22 08:36:38',
  ],
  40 => 
  [
    'id' => 53,
    'type' => 'facebook_pixel',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2020-01-22 12:43:58',
    'updated_at' => '2020-01-22 12:43:58',
  ],
  41 => 
  [
    'id' => 55,
    'type' => 'classified_product',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2020-05-13 15:01:05',
    'updated_at' => '2020-05-13 15:01:05',
  ],
  42 => 
  [
    'id' => 56,
    'type' => 'pos_activation_for_seller',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2020-06-11 11:45:02',
    'updated_at' => '2020-06-11 11:45:02',
  ],
  43 => 
  [
    'id' => 57,
    'type' => 'shipping_type',
    'value' => 'product_wise_shipping',
    'lang' => NULL,
    'created_at' => '2020-07-01 15:49:56',
    'updated_at' => '2020-07-01 15:49:56',
  ],
  44 => 
  [
    'id' => 58,
    'type' => 'flat_rate_shipping_cost',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2020-07-01 15:49:56',
    'updated_at' => '2020-07-01 15:49:56',
  ],
  45 => 
  [
    'id' => 59,
    'type' => 'shipping_cost_admin',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2020-07-01 15:49:56',
    'updated_at' => '2020-07-01 15:49:56',
  ],
  46 => 
  [
    'id' => 60,
    'type' => 'payhere_sandbox',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2020-07-30 20:23:53',
    'updated_at' => '2020-07-30 20:23:53',
  ],
  47 => 
  [
    'id' => 61,
    'type' => 'payhere',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2020-07-30 20:23:53',
    'updated_at' => '2020-07-30 20:23:53',
  ],
  48 => 
  [
    'id' => 62,
    'type' => 'google_recaptcha',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2020-08-17 09:13:37',
    'updated_at' => '2020-08-17 09:13:37',
  ],
  49 => 
  [
    'id' => 63,
    'type' => 'ngenius',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2020-09-22 12:58:21',
    'updated_at' => '2020-09-22 12:58:21',
  ],
  50 => 
  [
    'id' => 64,
    'type' => 'header_logo',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  51 => 
  [
    'id' => 65,
    'type' => 'show_language_switcher',
    'value' => 'on',
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  52 => 
  [
    'id' => 66,
    'type' => 'show_currency_switcher',
    'value' => 'on',
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  53 => 
  [
    'id' => 67,
    'type' => 'header_stikcy',
    'value' => 'on',
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  54 => 
  [
    'id' => 68,
    'type' => 'footer_logo',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  55 => 
  [
    'id' => 69,
    'type' => 'about_us_description',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  56 => 
  [
    'id' => 70,
    'type' => 'contact_address',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  57 => 
  [
    'id' => 71,
    'type' => 'contact_phone',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  58 => 
  [
    'id' => 72,
    'type' => 'contact_email',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  59 => 
  [
    'id' => 73,
    'type' => 'widget_one_labels',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  60 => 
  [
    'id' => 74,
    'type' => 'widget_one_links',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  61 => 
  [
    'id' => 75,
    'type' => 'widget_one',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  62 => 
  [
    'id' => 76,
    'type' => 'frontend_copyright_text',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  63 => 
  [
    'id' => 77,
    'type' => 'show_social_links',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  64 => 
  [
    'id' => 78,
    'type' => 'facebook_link',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  65 => 
  [
    'id' => 79,
    'type' => 'twitter_link',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  66 => 
  [
    'id' => 80,
    'type' => 'instagram_link',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  67 => 
  [
    'id' => 81,
    'type' => 'youtube_link',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  68 => 
  [
    'id' => 82,
    'type' => 'linkedin_link',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  69 => 
  [
    'id' => 83,
    'type' => 'payment_method_images',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  70 => 
  [
    'id' => 84,
    'type' => 'home_slider_images',
    'value' => '[]',
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  71 => 
  [
    'id' => 85,
    'type' => 'home_slider_links',
    'value' => '[]',
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  72 => 
  [
    'id' => 86,
    'type' => 'home_banner1_images',
    'value' => '[]',
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  73 => 
  [
    'id' => 87,
    'type' => 'home_banner1_links',
    'value' => '[]',
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  74 => 
  [
    'id' => 88,
    'type' => 'home_banner2_images',
    'value' => '[]',
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  75 => 
  [
    'id' => 89,
    'type' => 'home_banner2_links',
    'value' => '[]',
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  76 => 
  [
    'id' => 90,
    'type' => 'home_categories',
    'value' => '[1,2,3,4,5,6]',
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  77 => 
  [
    'id' => 91,
    'type' => 'top10_categories',
    'value' => '[]',
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  78 => 
  [
    'id' => 92,
    'type' => 'top10_brands',
    'value' => '[]',
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  79 => 
  [
    'id' => 93,
    'type' => 'website_name',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  80 => 
  [
    'id' => 94,
    'type' => 'site_motto',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  81 => 
  [
    'id' => 95,
    'type' => 'site_icon',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  82 => 
  [
    'id' => 96,
    'type' => 'base_color',
    'value' => '#e62e04',
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  83 => 
  [
    'id' => 97,
    'type' => 'base_hov_color',
    'value' => '#e62e04',
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  84 => 
  [
    'id' => 98,
    'type' => 'meta_title',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  85 => 
  [
    'id' => 99,
    'type' => 'meta_description',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  86 => 
  [
    'id' => 100,
    'type' => 'meta_keywords',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  87 => 
  [
    'id' => 101,
    'type' => 'meta_image',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  88 => 
  [
    'id' => 102,
    'type' => 'site_name',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  89 => 
  [
    'id' => 103,
    'type' => 'system_logo_white',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  90 => 
  [
    'id' => 104,
    'type' => 'system_logo_black',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  91 => 
  [
    'id' => 105,
    'type' => 'timezone',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  92 => 
  [
    'id' => 106,
    'type' => 'admin_login_background',
    'value' => NULL,
    'lang' => NULL,
    'created_at' => '2020-11-16 08:26:36',
    'updated_at' => '2020-11-16 08:26:36',
  ],
  93 => 
  [
    'id' => 107,
    'type' => 'iyzico_sandbox',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2020-12-30 17:45:56',
    'updated_at' => '2020-12-30 17:45:56',
  ],
  94 => 
  [
    'id' => 108,
    'type' => 'iyzico',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2020-12-30 17:45:56',
    'updated_at' => '2020-12-30 17:45:56',
  ],
  95 => 
  [
    'id' => 109,
    'type' => 'decimal_separator',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2020-12-30 17:45:56',
    'updated_at' => '2020-12-30 17:45:56',
  ],
  96 => 
  [
    'id' => 110,
    'type' => 'nagad',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2021-01-22 11:30:03',
    'updated_at' => '2021-01-22 11:30:03',
  ],
  97 => 
  [
    'id' => 111,
    'type' => 'bkash',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2021-01-22 11:30:03',
    'updated_at' => '2021-01-22 11:30:03',
  ],
  98 => 
  [
    'id' => 112,
    'type' => 'bkash_sandbox',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2021-01-22 11:30:03',
    'updated_at' => '2021-01-22 11:30:03',
  ],
  99 => 
  [
    'id' => 113,
    'type' => 'header_menu_labels',
    'value' => '["Home","Flash Sale","Blogs","All Brands","All Categories"]',
    'lang' => NULL,
    'created_at' => '2021-02-16 03:43:11',
    'updated_at' => '2021-02-16 03:52:18',
  ],
  100 => 
  [
    'id' => 114,
    'type' => 'header_menu_links',
    'value' => '["http:\\/\\/domain.com","http:\\/\\/domain.com\\/flash-deals","http:\\/\\/domain.com\\/blog","http:\\/\\/domain.com\\/brands","http:\\/\\/domain.com\\/categories"]',
    'lang' => NULL,
    'created_at' => '2021-02-16 03:43:11',
    'updated_at' => '2021-02-18 02:20:04',
  ],
  101 => 
  [
    'id' => 115,
    'type' => 'proxypay',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2021-06-20 13:25:42',
    'updated_at' => '2021-06-20 13:25:42',
  ],
  102 => 
  [
    'id' => 116,
    'type' => 'proxypay_sandbox',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2021-06-20 13:25:42',
    'updated_at' => '2021-06-20 13:25:42',
  ],
  103 => 
  [
    'id' => 117,
    'type' => 'google_map',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2021-07-27 17:49:39',
    'updated_at' => '2021-07-27 17:49:39',
  ],
  104 => 
  [
    'id' => 118,
    'type' => 'google_firebase',
    'value' => '0',
    'lang' => NULL,
    'created_at' => '2021-07-27 17:49:39',
    'updated_at' => '2021-07-27 17:49:39',
  ],
  105 => 
  [
    'id' => 119,
    'type' => 'authorizenet_sandbox',
    'value' => '1',
    'lang' => NULL,
    'created_at' => '2021-02-16 03:43:11',
    'updated_at' => '2021-06-14 07:00:23',
  ],
]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
