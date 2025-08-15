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

        $this->load->model(['M_user_access', 'M_perusahaans', 'M_subscription']);

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

    public function premium_confirmation()
    {


        $nip = $this->session->userdata('nip');

        $data['title'] = 'Cabang';
        $data['utility'] = $this->db->get('utility')->row_array();
        $data['pages_script'] = 'script/subscription/s_premium_confirmation';
        $data['user'] = $this->db->get_where('users', ['nip' => $nip])->row_array();
        $data['pages'] = 'pages/subscription/v_premium_confirmation';
        $data['menus'] = $this->M_menu->get_accessible_menus($nip);

        $this->load->view('index', $data);
    }

    public function ajax_list()
    {
        $list = $this->M_subscription->get_datatables();
        $data = array();
        $crs = "";
        $no = $_POST['start'];
        // Date formatting
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

        foreach ($list as $cat) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $cat->nama_perusahaan;
            $row[] = $cat->paket;
            $row[] = $cat->total_bulan;

            $start_date_obj = new DateTime($cat->tanggal_mulai);
            $end_date_obj = new DateTime($cat->tanggal_selesai);

            $formatted_start_date = strtr($start_date_obj->format('d F Y'), $indonesian_months);
            $formatted_end_date = strtr($end_date_obj->format('d F Y'), $indonesian_months);

            // Nominal formatting
            $formatted_nominal = 'Rp. ' . number_format($cat->nominal, 0, ',', '.');

            // Updated data for the row
            $row[] = $formatted_start_date;
            $row[] = $formatted_end_date;
            $row[] = $formatted_nominal;
            if ($cat->status_bayar == 0) {
                $button_konfirmasi = '
                      <button class="btn btn-primary text-white" data-toggle="modal" data-id="' . $cat->id . '" data-target="#approval_modal" type="button" style="color: white;">Approval</button>';

                $status = "Belum Dikonfirmasi";
            } else {
                $button_konfirmasi = '';

                if ($cat->status_bayar == 1) {
                    $status = "Disetujui";
                } else {
                    $status = "Tidak Disetujui";
                }
            }
            $row[] = $status;
            $row[] = $button_konfirmasi;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->M_subscription->count_all(),
            "recordsFiltered" => $this->M_subscription->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function update_confirmation_premium()
    {
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

        $id = $this->input->post('id_approval');
        $status_bayar = $this->input->post('confirmation');
        $edit_data = [
            "status_bayar" => $status_bayar,
        ];
        $this->db->where('id', $id);
        if ($this->db->update('premium_confirmation', $edit_data)) {

            if ($status_bayar == 1) {
                $confirmation_detail = $this->db->from('premium_confirmation')->where('Id', $id)->get()->row();
                // $perusahaan_detail = $this->db->from('premium_confirmation')->where('Id', $id)->get()->row();
                if ($confirmation_detail->paket == "Bangsawan Muda") {
                    $kuota_invoice = 5000;
                    $kuota_memo = 5000;
                    $kuota_pengajuan_biaya = 5000;
                    $kuota_user = 15;
                    $kuota_cabang = 3;
                } else if ($confirmation_detail->paket == "Kesatria Sejati") {
                    $kuota_invoice = 10000;
                    $kuota_memo = 10000;
                    $kuota_pengajuan_biaya = 10000;
                    $kuota_user = 30;
                    $kuota_cabang = 5;
                } else if ($confirmation_detail->paket == "Raja Sultan") {
                    $kuota_invoice = 25000;
                    $kuota_memo = 25000;
                    $kuota_pengajuan_biaya = 25000;
                    $kuota_user = 50;
                    $kuota_cabang = 10;
                }
                $edit_data_perusahaan = [
                    "kuota_invoice" => $kuota_invoice,
                    "kuota_memo" => $kuota_memo,
                    "kuota_pengajuan_biaya" => $kuota_pengajuan_biaya,
                    "kuota_user" => $kuota_user,
                    "kuota_cabang" => $kuota_cabang,
                    "is_premium" => 1,
                    "expired_day" => $confirmation_detail->tanggal_selesai,
                ];
                $this->db->where('Id', $confirmation_detail->id_perusahaan);
                // $this->db->update('utility', $edit_data_perusahaan);

                if ($this->db->update('utility', $edit_data_perusahaan)) {

                    $this->db->select('users.*'); // Select all from users, and specific columns from t_cabang
                    $this->db->from('users');
                    $this->db->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
                    $this->db->where('t_cabang.id_perusahaan', $confirmation_detail->id_perusahaan);
                    $this->db->where('nama_jabatan', 'Super Admin');
                    $detail_user = $this->db->get()->row();
                    // Send a success response


                    $start_date_obj = new DateTime($confirmation_detail->tanggal_selesai);

                    $formatted_start_date = strtr($start_date_obj->format('d F Y'), $indonesian_months);


                    $msg = "Terima kasih, pembayaran Anda telah berhasil kami konfirmasi. Akun Anda telah *di-upgrade* ke *premium*, berlaku hingga tanggal *{$formatted_start_date}*. Kami harap Anda puas dengan layanan kami.";

                    // $this->api_whatsapp->wa_notif($msg, "085157563305");
                    if ($this->api_whatsapp->wa_notif($msg, $detail_user->phone)) {
                        echo json_encode(array("status" => 'success', "message" => "Berhasil Mengkonfirmasi"));
                    } else {
                        echo json_encode(array("status" => 'error', "message" => "Gagal Kirim WA"));
                    }
                } else {
                    echo json_encode(array("status" => 'error', "message" => "Gagal Update Perusahaan"));
                }
            } else {
                echo json_encode(array("status" => 'success', "message" => "Berhasil Mengkonfirmasi"));
            }
        } else {
            echo json_encode(array("status" => 'error', "message" => "Gagal Mengkonfirmasi"));
        }
    }
}
