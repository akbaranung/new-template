<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * WhatsApp Gateway Library untuk CodeIgniter 3
 *
 * Simpan file ini di: application/libraries/Whatsapp.php
 *
 * Cara load di controller:
 *   $this->load->library('whatsapp');
 *
 * Atau autoload di application/config/autoload.php:
 *   $autoload['libraries'] = array('whatsapp');
 *
 * Cara pakai:
 *   $this->whatsapp->send('08123456789', 'Halo!');
 *   $this->whatsapp->send_group('120363xxx@g.us', 'Halo group!');
 *   $this->whatsapp->broadcast(['08111', '08222'], 'Pesan broadcast');
 *   $this->whatsapp->check_number('08123456789');
 *   $this->whatsapp->groups();
 *   $this->whatsapp->status();
 *   $this->whatsapp->is_connected();
 */
class Whatsapp
{
    private $CI;
    private $base_url;
    private $api_key;

    public function __construct()
    {
        $this->CI = &get_instance();

        // Load config dari application/config/whatsapp.php
        $this->CI->config->load('whatsapp', true, true);

        $this->base_url = $this->CI->config->item('wa_base_url', 'whatsapp') ?: 'http://103.27.206.233:3000';
        $this->api_key  = $this->CI->config->item('wa_api_key', 'whatsapp')  ?: 'bariskodeindonesia123456!@@';

        // Pastikan ekstensi cURL tersedia
        if (!function_exists('curl_init')) {
            show_error('Ekstensi cURL tidak aktif. Aktifkan extension=curl di php.ini');
        }
    }

    // ============================================================
    // PUBLIC METHODS
    // ============================================================

    /**
     * Kirim pesan teks ke nomor pribadi
     *
     * @param  string $number  nomor tujuan (format bebas: 08xx / 628xx / +628xx)
     * @param  string $message isi pesan
     * @return array  ['success' => bool, 'message' => string, 'id' => string, 'to' => string]
     */
    public function send($number, $message)
    {
        return $this->request('POST', '/send-message', array(
            'number'  => $this->format_number($number),
            'message' => $message,
        ));
    }

    /**
     * Kirim pesan teks ke group WhatsApp
     *
     * @param  string $group_id ID group dari hasil groups(), contoh: 120363xxx@g.us
     * @param  string $message  isi pesan
     * @return array  ['success' => bool, 'message' => string, 'id' => string]
     */
    public function send_group($group_id, $message)
    {
        return $this->request('POST', '/send-message', array(
            'group_id' => $group_id,
            'message'  => $message,
        ));
    }

    /**
     * Kirim pesan ke banyak nomor sekaligus (broadcast)
     *
     * @param  array  $numbers list nomor tujuan
     * @param  string $message isi pesan
     * @param  int    $delay   jeda antar pesan dalam detik (default 1)
     * @return array  hasil per nomor ['628xxx' => ['success' => bool, ...]]
     */
    public function broadcast(array $numbers, $message, $delay = 1)
    {
        $results = array();

        foreach ($numbers as $number) {
            $formatted           = $this->format_number($number);
            $results[$formatted] = $this->send($formatted, $message);
            if ($delay > 0) sleep($delay);
        }

        return $results;
    }

    /**
     * Cek apakah nomor terdaftar di WhatsApp
     *
     * @param  string $number nomor yang ingin dicek
     * @return array  ['success' => bool, 'number' => string, 'registered' => bool]
     */
    public function check_number($number)
    {
        return $this->request('POST', '/check-number', array(
            'number' => $this->format_number($number),
        ));
    }

    /**
     * Ambil daftar semua group
     *
     * @return array ['success' => bool, 'total' => int, 'groups' => [...]]
     */
    public function groups()
    {
        return $this->request('GET', '/groups');
    }

    /**
     * Cek status koneksi gateway
     *
     * @return array ['success' => bool, 'status' => string, 'hasQR' => bool]
     */
    public function status()
    {
        return $this->request('GET', '/status');
    }

    /**
     * Cek apakah gateway sedang terhubung
     *
     * @return bool
     */
    public function is_connected()
    {
        $result = $this->status();
        return isset($result['status']) && $result['status'] === 'connected';
    }

    /**
     * Format nomor ke format internasional (62xxx)
     * - 08xxx   -> 628xxx
     * - +628xxx -> 628xxx
     * - 628xxx  -> 628xxx (tidak berubah)
     *
     * @param  string $number
     * @return string
     */
    public function format_number($number)
    {
        // hapus semua karakter selain angka
        $number = preg_replace('/\D/', '', $number);

        // ganti awalan 0 dengan 62
        if (substr($number, 0, 1) === '0') {
            $number = '62' . substr($number, 1);
        }

        return $number;
    }

    /**
     * Raw GET request (untuk AJAX dari controller dashboard)
     */
    public function send_raw_get($endpoint)
    {
        return $this->request('GET', $endpoint);
    }

    /**
     * Raw POST request (untuk AJAX dari controller dashboard)
     */
    public function send_raw_post($endpoint, $payload = array())
    {
        return $this->request('POST', $endpoint, $payload);
    }

    // ============================================================
    // PRIVATE METHODS
    // ============================================================

    /**
     * Eksekusi HTTP request ke WA Gateway menggunakan cURL
     *
     * @param  string     $method   GET | POST
     * @param  string     $endpoint contoh: /send-message
     * @param  array|null $payload  data body untuk POST
     * @return array
     */
    private function request($method, $endpoint, $payload = null)
    {
        $url = rtrim($this->base_url, '/') . $endpoint;

        // inisialisasi cURL
        $ch = curl_init();

        // set headers
        $headers = array(
            'x-api-key: ' . $this->api_key,
            'Content-Type: application/json',
            'Accept: application/json',
        );

        // opsi dasar
        curl_setopt($ch, CURLOPT_URL,            $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   // return response sebagai string
        curl_setopt($ch, CURLOPT_HTTPHEADER,     $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT,        30);     // batas waktu tunggu response
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);     // batas waktu konek ke server
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);   // ikuti redirect otomatis
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // nonaktifkan verifikasi SSL (aktifkan di produksi)
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // nonaktifkan verifikasi host SSL

        // jika POST, set body
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST,       true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        // eksekusi request
        $response   = curl_exec($ch);
        $http_code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        $curl_errno = curl_errno($ch);
        curl_close($ch);

        // tangani error koneksi
        if ($curl_errno) {
            return array(
                'success'    => false,
                'message'    => 'Gagal menghubungi WA Gateway: ' . $curl_error,
                'error_code' => $curl_errno,
            );
        }

        // tangani response kosong
        if (empty($response)) {
            return array(
                'success'   => false,
                'message'   => 'Response kosong dari gateway',
                'http_code' => $http_code,
            );
        }

        // decode JSON response
        $decoded = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return array(
                'success'    => false,
                'message'    => 'Response bukan format JSON yang valid',
                'http_code'  => $http_code,
                'raw'        => $response,
            );
        }

        // tangani HTTP error (4xx, 5xx)
        if ($http_code >= 400) {
            return array(
                'success'   => false,
                'message'   => isset($decoded['message']) ? $decoded['message'] : 'HTTP Error ' . $http_code,
                'http_code' => $http_code,
            );
        }

        return $decoded;
    }
}
