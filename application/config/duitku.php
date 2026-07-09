<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Duitku Configuration
|--------------------------------------------------------------------------
| Daftar di https://dashboard.duitku.com untuk mendapatkan API Key
|
| is_production: false = Sandbox (testing), true = Production
*/

$config['duitku_merchant_code'] = 'DS30540'; // Ganti dengan Merchant Code kamu
$config['duitku_api_key']       = 'efcf29b7d5836c3ca7c573c6539ed2b5';       // Ganti dengan API Key kamu
$config['duitku_is_production'] = false;                 // Ubah ke true saat live
$config['duitku_base_url'] = $config['duitku_is_production']
    ? 'https://passport.duitku.com/webapi/api/'
    : 'https://api-sandbox.duitku.com/api/';  // <-- BEDA dari sebelumnya
// $config['duitku_callback_url']  = base_url('Subscription/duitku_notification');
// $config['duitku_return_url']    = base_url('Subscription/duitku_return');
// Untuk testing localhost, gunakan dummy URL dulu
$config['duitku_callback_url'] = 'https://example.com/callback';
$config['duitku_return_url']   = 'https://example.com/return';
