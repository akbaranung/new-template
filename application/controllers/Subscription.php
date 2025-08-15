<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Subscription extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('isLogin') == FALSE) {
            $this->session->set_flashdata('error', 'Your session has expired');
            redirect('auth');
        } else if (!$this->session->userdata('nama_perusahaan')) {
            redirect('auth');
        }

        $this->load->model(['M_user_access', 'M_perusahaans']);

        $this->cb = $this->load->database('corebank', TRUE);
    }

    public function upgrade()
    {
        $nip = $this->session->userdata('nip');
        $data['title'] = 'Perusahaan';
        $data['utility'] = $this->db->get('utility')->row_array();
        $data['user'] = $this->db->get_where('users', ['nip' => $nip])->row_array();
        $data['pages'] = 'pages/subscription/v_upgrade';
        $data['pages_script'] = 'script/subscription/s_upgrade';
        $data['menus'] = $this->M_menu->get_accessible_menus($nip);

        $this->load->view('index', $data);
    }

    public function proses_bayar()
    {
        // Set the response header to JSON
        $this->output->set_content_type('application/json');

        // Check if the request method is POST
        if ($this->input->method() !== 'post') {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Invalid request method.'
            ]);
            return;
        }

        // Get the raw POST data from the request body
        $json_data = $this->input->raw_input_stream;

        // Decode the JSON data into a PHP associative array
        $data = json_decode($json_data, true);

        // Validate that the data was received and decoded correctly
        if ($data === null) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Invalid JSON data received.'
            ]);
            return;
        }

        // Prepare the data for insertion, mapping the front-end keys to your database columns
        $add = [
            "id_perusahaan" => $data['id_perusahaan'],
            "paket" => $data['planName'],
            "total_bulan" => $data['months'],
            "tanggal_mulai" => $data['startDate'],
            "tanggal_selesai" => $data['endDate'],
            "nominal" => $data['confirmationPrice'],
            "status_bayar" => 0, // Assuming 0 is the default for 'pending'
        ];
        $nominal = $data['confirmationPrice'];
        $formatted_nominal = number_format($nominal, 0, ',', '.');


        // --- Date Formatting Logic ---
        // Create a mapping of English month names to Indonesian month names
        $indonesian_months = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember'
        ];

        // Create DateTime objects from the YYYY-MM-DD strings
        $start_date_obj = new DateTime($data['startDate']);
        $end_date_obj = new DateTime($data['endDate']);

        // Format the dates to "DD Month YYYY" and replace the month name
        $tanggal_mulai_formatted = strtr($start_date_obj->format('d F Y'), $indonesian_months);
        $tanggal_selesai_formatted = strtr($end_date_obj->format('d F Y'), $indonesian_months);
        // --- End Date Formatting Logic ---


        $detail_perusahaan = $this->db->from('utility')->where('Id', $data['id_perusahaan'])->get()->row();

        // Attempt to insert the data into the database
        if ($this->db->insert('premium_confirmation', $add)) {
            // Send a success response
            $msg = "Pembayaran Premium telah masuk.
Rincian:
- Perusahaan: *$detail_perusahaan->nama_perusahaan* (ID: *$detail_perusahaan->Id*)
- Nama Paket: *" . $data['planName'] . "*
- Total Bulan: *" . $data['months'] . "*
- Tanggal Mulai: *" . $tanggal_mulai_formatted . "*
- Tanggal Selesai: *" . $tanggal_selesai_formatted . "*
- Nominal: *Rp. " . $formatted_nominal . "*

Mohon untuk memproses pembayaran segera.";

            // $this->api_whatsapp->wa_notif($msg, "085157563305");
            $this->api_whatsapp->wa_notif($msg, "08127070700");

            echo json_encode([
                'status'  => 'success',
                'message' => 'Pembayaran berhasil disimpan. Silahkan menunggu konfirmasi.'
            ]);
        } else {
            // Send a detailed error response if the insertion fails
            echo json_encode([
                'status'  => 'error',
                'message' => 'Gagal menyimpan pembayaran ke database.'
            ]);
        }
    }
}
