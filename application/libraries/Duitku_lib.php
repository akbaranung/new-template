<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Duitku QRIS Library for CodeIgniter 3
 * Handles QRIS transaction creation & status check
 *
 * Docs: https://docs.duitku.com
 */
class Duitku_lib
{

    private $CI;
    private $merchant_code;
    private $api_key;
    private $base_url;
    private $callback_url;
    private $return_url;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->config->load('duitku');

        $this->merchant_code = $this->CI->config->item('duitku_merchant_code');
        $this->api_key       = $this->CI->config->item('duitku_api_key');
        $this->base_url      = $this->CI->config->item('duitku_base_url');
        $this->callback_url  = $this->CI->config->item('duitku_callback_url');
        $this->return_url    = $this->CI->config->item('duitku_return_url');
    }

    /**
     * Buat transaksi QRIS
     *
     * @param  string  $order_id    ID unik pesanan
     * @param  int     $amount      Nominal (rupiah, tanpa desimal)
     * @param  array   $customer    ['name', 'email', 'phone']
     * @param  string  $item_name   Nama produk / layanan
     * @param  int     $expiry_min  Menit kedaluwarsa (default 60 menit)
     * @return array   Response dari Duitku
     */
    public function create_qris($order_id, $amount, $customer, $item_name = 'Pembayaran', $expiry_min = 60)
    {
        $expiry_period = $expiry_min;
        // $signature     = md5($this->merchant_code . $order_id . $amount . $this->api_key);
        // $signature = md5($this->merchant_code . $order_id . $amount . $this->api_key);
        $signature = md5($this->merchant_code . $order_id . (int)$amount . $this->api_key);

        $payload = [
            'merchantCode'     => $this->merchant_code,
            'paymentAmount'    => (int) $amount,
            'paymentMethod'    => 'QR',
            'merchantOrderId'  => $order_id,
            'productDetails'   => $item_name,
            'additionalParam'  => '',
            'merchantUserInfo' => $customer['email'] ?? '',
            'customerVaName'   => $customer['name'],
            'email'            => !empty($customer['email']) ? $customer['email'] : 'noreply@bariskode.com',
            'phoneNumber'      => $customer['phone'] ?? '',
            'itemDetails'      => [[
                'name'     => $item_name,
                'price'    => (int) $amount,
                'quantity' => 1,
            ]],
            'customerDetail'   => [
                'firstName'   => $customer['name'],
                'email'       => !empty($customer['email']) ? $customer['email'] : 'noreply@bariskode.com',
                'phoneNumber' => $customer['phone'] ?? '',
            ],
            'callbackUrl'      => $this->callback_url,
            'returnUrl'        => $this->return_url,
            // HAPUS 'signature' dari sini
            'expiryPeriod'     => $expiry_min,
        ];

        return $this->_request('POST', 'merchant/createinvoice', $payload);
    }

    /**
     * Cek status transaksi
     *
     * @param  string $order_id   merchantOrderId yang digunakan saat create
     * @param  int    $amount     Nominal transaksi (harus sama dengan saat create)
     * @return array
     */

    public function check_status($order_id, $amount)
    {
        // transactionStatus pakai MD5 di body, BUKAN SHA256 di header
        $signature = md5($this->merchant_code . $order_id . $this->api_key);

        $payload = [
            'merchantCode'    => $this->merchant_code,
            'merchantOrderId' => $order_id,
            'signature'       => $signature,
        ];

        // $url = 'https://sandbox.duitku.com/webapi/api/merchant/transactionStatus';
        $url = 'https://passport.duitku.com/webapi/api/merchant/transactionStatus';

        $ch  = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($payload)),
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) return ['error' => $error];

        return json_decode($response, true);
    }

    /**
     * Verifikasi notifikasi callback dari Duitku
     *
     * @param  array  $notification  Data POST dari Duitku
     * @return bool
     */
    public function verify_notification($notification)
    {
        $expected = md5(
            $this->merchant_code .
                $notification['amount'] .
                $notification['merchantOrderId'] .
                $this->api_key
        );
        return $expected === $notification['signature'];
    }

    // ---------------------------------------------------------------
    // Private: HTTP request ke Duitku API
    // ---------------------------------------------------------------
    private function _request($method, $endpoint, $payload = null)
    {
        $url       = $this->base_url . $endpoint;
        $timestamp = round(microtime(true) * 1000); // milliseconds
        $signature = hash('sha256', $this->merchant_code . $timestamp . $this->api_key);

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'x-duitku-signature: '   . $signature,
            'x-duitku-timestamp: '   . $timestamp,
            'x-duitku-merchantcode: ' . $this->merchant_code,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) return ['error' => $error];

        return json_decode($response, true);
    }
}
