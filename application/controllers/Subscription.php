<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Subscription extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(['M_coa', 'M_user_access', 'M_perusahaans', 'M_subscription']);

        $this->cb = $this->load->database('corebank', TRUE);
        date_default_timezone_set('Asia/Jakarta');
    }

    public function upgrade()
    {
        if ($this->session->userdata('isLogin') == FALSE) {
            $this->session->set_flashdata('error', 'Your session has expired');
            redirect('auth');
        } else if (!$this->session->userdata('nama_perusahaan')) {
            redirect('auth');
        }

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

        if ($this->session->userdata('isLogin') == FALSE) {
            $this->session->set_flashdata('error', 'Your session has expired');
            redirect('auth');
        } else if (!$this->session->userdata('nama_perusahaan')) {
            redirect('auth');
        }

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
        // --- NEW: Generate a complete timestamp for start and end dates ---
        $now = new DateTime();
        $start_date_with_time = $now->format('Y-m-d H:i:s');

        // Calculate the end date by adding months to the current time
        $months = $data['months'];
        $end_date_obj = clone $now;
        $end_date_obj->modify("+{$months} months");
        // $end_date_with_time = $end_date_obj->format('Y-m-d H:i:s');
        $end_date_with_time = $end_date_obj->format('Y-m-d');

        // Prepare the data for insertion, mapping the front-end keys to your database columns


        $expired_time = clone $now;
        $expired_time->modify('+24 hours');
        $expired_status_bayar = $expired_time->format('Y-m-d H:i:s');

        $add = [
            "id_perusahaan" => $data['id_perusahaan'],
            "paket" => $data['planName'],
            "total_bulan" => $data['months'],
            // "tanggal_mulai" => $data['startDate'],
            // "tanggal_selesai" => $data['endDate'],
            "tanggal_mulai" => $start_date_with_time,
            "tanggal_selesai" => $end_date_with_time,
            "nominal" => $data['confirmationPrice'],
            "status_bayar" => 0, // Assuming 0 is the default for 'pending'
            "expired_status_bayar" => $expired_status_bayar,
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
        $start_date_obj = new DateTime($start_date_with_time);
        $end_date_obj = new DateTime($end_date_with_time);

        // Format the dates to "DD Month YYYY" and replace the month name
        $tanggal_mulai_formatted = strtr($start_date_obj->format('d F Y H:i:s'), $indonesian_months);
        $tanggal_selesai_formatted = strtr($end_date_obj->format('d F Y'), $indonesian_months);
        // --- End Date Formatting Logic ---


        $detail_perusahaan = $this->db->from('utility')->where('Id', $data['id_perusahaan'])->get()->row();
        $now = (new DateTime())->format('Y-m-d H:i:s');

        // Attempt to insert the data into the database
        $confirmation_num = $this->db
            ->from('premium_confirmation')
            ->where('id_perusahaan', $data['id_perusahaan'])
            ->where('status_bayar', 0)
            ->where('expired_status_bayar >', $now) // Add this line to check for the expiration date
            ->get()
            ->num_rows(); // Note the s at the end of num_rows()

        $approval_num = $this->db
            ->from('premium_confirmation')
            ->where('id_perusahaan', $data['id_perusahaan'])
            ->where('status_bayar', 1)
            ->where('approval', 0)
            ->get()
            ->num_rows(); // Note the s at the end of num_rows()

        if ($approval_num) {

            $confirmation_detail = $this->db
                ->from('premium_confirmation')
                ->where('id_perusahaan', $data['id_perusahaan'])
                ->where('status_bayar', 1)
                ->where('approval', 0)
                ->get()
                ->row(); // Note the s at the end of num_rows()
            echo json_encode([
                'status'  => 'proses',
                'message' => 'Anda sudah melakukan Proses Pembayaran dengan Paket :' . $confirmation_detail->paket,
            ]);
        } else if ($confirmation_num) {
            $confirmation_detail = $this->db
                ->from('premium_confirmation')
                ->where('id_perusahaan', $data['id_perusahaan'])
                ->where('status_bayar', 0)
                ->where('expired_status_bayar >', $now) // Add this line to check for the expiration date
                ->get()
                ->row(); // Note the s at the end of num_rows()

            $this->db->from('users');
            $this->db->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
            $this->db->where('t_cabang.id_perusahaan', $confirmation_detail->id_perusahaan);
            $this->db->where('nama_jabatan', 'Super Admin');
            $this->db->where('level_jabatan', 99);
            $detail_user = $this->db->get()->row();

            $link_konfirmasi = base_url('Subscription/proses_bayar_konfirmasi_link/' . $confirmation_detail->id);

            $msg_user_whatsapp = "Halo, " . $detail_perusahaan->nama_perusahaan . "! ✨\n\nPemesanan paket premium Anda telah kami terima.\n\nBerikut rincian pesanan Anda:\n\n"
                . "- Paket: *" . $data['planName'] . "*\n"
                . "- Jangka Waktu: *" . $data['months'] . "* Bulan\n"
                . "- Total Tagihan: *Rp. " . $formatted_nominal . "*\n\n"
                . "Pembayaran melalui:\n\n"
                . "*Bank Syariah Indonesia (BSI)*\n"
                . "Nomor Rekening: *79 7070 7004*\n"
                . "Atas Nama: *PT. Baris Kode Indonesia*\n\n"
                . "Mohon lakukan pembayaran dalam waktu 24 jam dan konfirmasi pembelian paket dengan mengklik link di bawah ini:\n"
                . $link_konfirmasi . "\n\n"
                . "Terima kasih atas kerja sama Anda.\n\n"
                . "Hormat kami,\n"
                . "Tim Baris Kode Indonesia";

            if ($this->api_whatsapp->wa_notif($msg_user_whatsapp, $detail_user->phone)) {
                $whatsapp_send = True;
            } else {
                $whatsapp_send = false;
            }

            echo json_encode([
                'status'  => 'success',
                'message' => 'Anda sudah pernah melakukan Proses Pembayaran dengan Paket :' . $confirmation_detail->paket,
                'id_pembayaran' => $confirmation_detail->id,
                'confirmation_detail' => $confirmation_detail,
                'whatsapp_send' => $whatsapp_send,
                'whatsapp_number' => $detail_user->phone,

            ]);
        } else {
            if ($this->db->insert('premium_confirmation', $add)) {
                $last_id = $this->db->insert_id();

                $confirmation_detail = $this->db
                    ->from('premium_confirmation')
                    ->where('id', $last_id)
                    ->get()
                    ->row(); // Note the s at the end of num_rows()

                $this->db->from('users');
                $this->db->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
                $this->db->where('t_cabang.id_perusahaan', $confirmation_detail->id_perusahaan);
                $this->db->where('nama_jabatan', 'Super Admin');
                $this->db->where('level_jabatan', 99);
                $detail_user = $this->db->get()->row();

                $link_konfirmasi = base_url('Subscription/proses_bayar_konfirmasi_link/' . $last_id);

                $msg_user_whatsapp = "Halo, " . $detail_perusahaan->nama_perusahaan . "! ✨\n\nPembelian paket premium Anda telah kami terima.\n\nBerikut rincian pesanan Anda:\n\n"
                    . "- Paket: *" . $data['planName'] . "*\n"
                    . "- Jangka Waktu: *" . $data['months'] . "* Bulan\n"
                    . "- Total Tagihan: *Rp. " . $formatted_nominal . "*\n\n"
                    . "Mohon segera lakukan pembayaran melalui:\n\n"
                    . "*Bank Syariah Indonesia (BSI)*\n"
                    . "Nomor Rekening: *79 7070 7004*\n"
                    . "Atas Nama: *PT. Baris Kode Indonesia*\n\n"
                    . "Setelah melakukan pembayaran, mohon konfirmasi pembelian paket dengan mengklik link di bawah ini:\n"
                    . $link_konfirmasi . "\n\n"
                    . "Terima kasih atas kerja sama Anda.\n\n"
                    . "Hormat kami,\n"
                    . "Tim Baris Kode Indonesia";

                if ($this->api_whatsapp->wa_notif($msg_user_whatsapp, $detail_user->phone)) {
                    $whatsapp_send = True;
                } else {
                    $whatsapp_send = false;
                }


                echo json_encode([
                    'status'  => 'success',
                    'message' => 'Pembayaran berhasil disimpan. Silahkan menunggu konfirmasi.',
                    'id_pembayaran' => $last_id,
                    'confirmation_detail' => $confirmation_detail,
                    'whatsapp_send' => $whatsapp_send,
                    'whatsapp_number' => $detail_user->phone,
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

    public function proses_bayar_konfirmasi($id)
    {
        // Set the response header to JSON

        // Check if the request method is POST
        // Validate the ID to ensure it's a valid integer
        if (!is_numeric($id) || $id <= 0) {
            // Handle invalid ID, e.g., redirect or show an error
            echo json_encode(['status' => 'error', 'message' => 'ID tidak valid.']);
            return;
        }

        $now = (new DateTime())->format('Y-m-d H:i:s');
        $update_data = ['status_bayar' => 1];
        $confirmation_detail = $this->db
            ->from('premium_confirmation')
            ->where('id', $id)
            ->get()
            ->row(); // Note the s at the end of num_rows()

        if ($confirmation_detail->status_bayar == 1) {
            // $this->session->set_flashdata('message_name', 'Pembayaran telah dikonfirmasi, silahkan menunggu.');
            echo json_encode(['status' => 'success', 'message' => 'Pembayaran telah dikonfirmasi, silahkan menunggu.']);
        } else if ($confirmation_detail->expired_status_bayar < $now) {
            // $this->session->set_flashdata('error', 'Konfirmasi pembayaran telah kedaluwarsa. Silakan pesan ulang.');
            echo json_encode(['status' => 'error', 'message' => 'Konfirmasi pembayaran telah kedaluwarsa. Silakan pesan ulang.']);
        } else {
            // Set the data to be updated
            $update_data = ['status_bayar' => 1];

            // Update the record in the database
            $this->db->where('id', $id);
            $this->db->update('premium_confirmation', $update_data);

            // Check if the update was successful
            if ($this->db->affected_rows() > 0) {

                $confirmation_detail = $this->db
                    ->from('premium_confirmation')
                    ->where('id', $id)
                    ->get()
                    ->row(); // Note the s at the end of num_rows()
                // Send a success response

                $detail_perusahaan = $this->db->from('utility')->where('Id', $confirmation_detail->id_perusahaan)->get()->row();

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

                $tanggal_mulai_obj = new DateTime($confirmation_detail->tanggal_mulai);
                $tanggal_selesai_obj = new DateTime($confirmation_detail->tanggal_selesai);

                // 2. Format the dates to a string with English month names
                // For 'tanggal_mulai', include time
                $tanggal_mulai_english = $tanggal_mulai_obj->format('d F Y H:i:s');
                // For 'tanggal_selesai', just the date
                $tanggal_selesai_english = $tanggal_selesai_obj->format('d F Y');

                // 3. Translate the month names using the map
                $tanggal_mulai_formatted = strtr($tanggal_mulai_english, $indonesian_months);
                $tanggal_selesai_formatted = strtr($tanggal_selesai_english, $indonesian_months);
                $formatted_nominal = number_format($confirmation_detail->nominal, 0, ',', '.');


                //                 $msg = "Pembayaran telah dilakukan oleh user,
                // Rincian:
                // - Perusahaan: *$detail_perusahaan->nama_perusahaan* (ID: *$detail_perusahaan->Id*)
                // - Nama Paket: *" . $confirmation_detail->paket . "*
                // - Total Bulan: *" . $confirmation_detail->total_bulan . "*
                // - Tanggal Mulai: *" . $tanggal_mulai_formatted . "*
                // - Tanggal Selesai: *" . $tanggal_selesai_formatted . "*
                // - Nominal: *Rp. " . $formatted_nominal . "*

                // Mohon untuk memproses pembayaran segera.";



                $msg = "Pembayaran telah dilakukan oleh user,
                Rincian:
                - Perusahaan: *$detail_perusahaan->nama_perusahaan* (ID: *$detail_perusahaan->Id*)
                - Nama Paket: *" . $confirmation_detail->paket . "*
                - Total Bulan: *" . $confirmation_detail->total_bulan . "*
                - Nominal: *Rp. " . $formatted_nominal . "*

                Mohon untuk memproses pembayaran segera.";

                // $this->api_whatsapp->wa_notif($msg, "085157563305");
                $this->api_whatsapp->wa_notif($msg, "08127070700");


                echo json_encode(['status' => 'success', 'message' => 'Status pembayaran berhasil dikonfirmasi.']);
            } else {
                // The ID was valid, but no rows were updated (e.g., ID not found)
                echo json_encode(['status' => 'error', 'message' => 'Gagal mengonfirmasi status pembayaran. ID tidak ditemukan.']);
            }
        }
        return;
    }

    public function proses_bayar_konfirmasi_link($id)
    {
        // Set the response header to JSON

        // Check if the request method is POST
        // Validate the ID to ensure it's a valid integer

        // Set the data to be updated
        // $now = (new DateTime())->format('Y-m-d H:i:s');
        $now = new DateTime();


        $expired_time = clone $now;
        $expired_time->modify('+24 hours');
        $expired_status_bayar = $expired_time->format('Y-m-d H:i:s');

        $update_data = [
            'status_bayar' => 1,
            'tanggal_bayar' => $expired_status_bayar,
        ];
        $confirmation_detail = $this->db
            ->from('premium_confirmation')
            ->where('id', $id)
            ->get()
            ->row(); // Note the s at the end of num_rows()

        if ($confirmation_detail->status_bayar == 1) {
            $this->session->set_flashdata('message_name', 'Pembayaran telah dikonfirmasi, silahkan menunggu.');

            if ($this->session->userdata('isLogin') == FALSE) {
                redirect('auth');
            } else {
                redirect('home');
            }
        } else if ($confirmation_detail->expired_status_bayar < $now) {
            $this->session->set_flashdata('message_error', 'Konfirmasi pembayaran telah kedaluwarsa. Silakan pesan ulang.');

            if ($this->session->userdata('isLogin') == FALSE) {
                redirect('auth');
            } else {
                redirect('home');
            }
        } else {
            // Update the record in the database
            $this->db->where('id', $id);
            $this->db->update('premium_confirmation', $update_data);

            // Check if the update was successful
            if ($this->db->affected_rows() > 0) {

                $detail_perusahaan = $this->db->from('utility')->where('Id', $confirmation_detail->id_perusahaan)->get()->row();

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

                $tanggal_mulai_obj = new DateTime($confirmation_detail->tanggal_mulai);
                $tanggal_selesai_obj = new DateTime($confirmation_detail->tanggal_selesai);

                // 2. Format the dates to a string with English month names
                // For 'tanggal_mulai', include time
                $tanggal_mulai_english = $tanggal_mulai_obj->format('d F Y H:i:s');
                // For 'tanggal_selesai', just the date
                $tanggal_selesai_english = $tanggal_selesai_obj->format('d F Y');

                // 3. Translate the month names using the map
                $tanggal_mulai_formatted = strtr($tanggal_mulai_english, $indonesian_months);
                $tanggal_selesai_formatted = strtr($tanggal_selesai_english, $indonesian_months);
                $formatted_nominal = number_format($confirmation_detail->nominal, 0, ',', '.');

                $msg = "Pembayaran telah dilakukan oleh user,
Rincian:
- Perusahaan: *$detail_perusahaan->nama_perusahaan* (ID: *$detail_perusahaan->Id*)
- Nama Paket: *" . $confirmation_detail->paket . "*
- Total Bulan: *" . $confirmation_detail->total_bulan . "*
- Nominal: *Rp. " . $formatted_nominal . "*

Mohon untuk memproses pembayaran segera.";

                // $this->api_whatsapp->wa_notif($msg, "085157563305");
                $this->api_whatsapp->wa_notif($msg, "08127070700");


                // echo json_encode(['status' => 'success', 'message' => 'Status pembayaran berhasil dikonfirmasi.']);


                // $this->session->set_flashdata('swal_message', [
                //     'icon' => 'success', // or 'success', 'warning', 'info', 'question'
                //     'title' => 'Berhasil!',
                //     'text' => 'Status pembayaran berhasil dikonfirmasi.',
                //     'confirmButtonText' => 'Mengerti',
                // ]);
                $this->session->set_flashdata('message_name', 'Verifikasi Berhasil!');
                if ($this->session->userdata('isLogin') == FALSE) {
                    redirect('auth');
                } else {
                    redirect('home');
                }
            } else {
                // The ID was valid, but no rows were updated (e.g., ID not found)
                // echo json_encode(['status' => 'error', 'message' => 'Gagal mengonfirmasi status pembayaran. ID tidak ditemukan.']);


                // $this->session->set_flashdata('swal_message', [
                //     'icon' => 'error', // or 'success', 'warning', 'info', 'question'
                //     'title' => 'Error!',
                //     'text' => 'Gagal mengonfirmasi status pembayaran. ID tidak ditemukan.',
                //     'confirmButtonText' => 'Mengerti',
                // ]);
                $this->session->set_flashdata('message_error', 'Gagal mengonfirmasi status pembayaran. ID tidak ditemukan.');

                if ($this->session->userdata('isLogin') == FALSE) {
                    redirect('auth');
                } else {
                    redirect('home');
                }
            }
        }
    }

    public function premium_confirmation()
    {

        if ($this->session->userdata('isLogin') == FALSE) {
            $this->session->set_flashdata('error', 'Your session has expired');
            redirect('auth');
        } else if (!$this->session->userdata('nama_perusahaan')) {
            redirect('auth');
        }

        if ($this->session->userdata('username') == "bariskode") {
            $has_access = TRUE;
        } else {
            $has_access = FALSE;
        }

        if (!$has_access) {
            show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
        }

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
        $now = (new DateTime())->format('Y-m-d H:i:s');

        foreach ($list as $cat) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $cat->nama_perusahaan;
            $row[] = $cat->paket;
            $row[] = $cat->total_bulan;

            $start_date_obj = new DateTime($cat->tanggal_mulai);
            $end_date_obj = new DateTime($cat->tanggal_selesai);

            $formatted_start_date = strtr($start_date_obj->format('d F Y H:i:s'), $indonesian_months);
            $formatted_end_date = strtr($end_date_obj->format('d F Y'), $indonesian_months);

            // Nominal formatting
            $formatted_nominal = 'Rp. ' . number_format($cat->nominal, 0, ',', '.');

            // Updated data for the row
            $row[] = $formatted_start_date;
            $row[] = $formatted_end_date;
            $row[] = $formatted_nominal;

            // $button_update = '<button class="btn btn-warning text-white" data-toggle="modal" data-id="' . $cat->id . '" data-target="#edit_modal" type="button" style="color: white;">Edit</button>';
            $button_update = '<button class="btn btn-success text-white btn-sm" data-toggle="modal" data-id="' . $cat->id . '" data-target="#edit_modal" type="button" style="color: white;" onclick="onEdit(' . $cat->id . ')"><i class="fe fe-edit"></i></button>';

            if ($cat->status_bayar == 0 && $cat->approval == 0 && $cat->expired_status_bayar < $now) {
                // $button_aktifasi_expired = '<button class="btn btn-primary text-white" type="button" style="color: white;" onclick="onAktifasiExpired(' . $cat->id . ')"><i class="fe fe-refresh-cw"></i></button>';
                $button_aktifasi_expired = '';
            } else {
                $button_aktifasi_expired = '';
            }

            if ($cat->status_bayar == 1 && $cat->approval == 0) {
                $button_konfirmasi = '
                      <button class="btn btn-primary text-white" data-toggle="modal" data-id="' . $cat->id . '" data-target="#approval_modal" type="button" style="color: white;">Approval</button>';

                $status = "Belum Dikonfirmasi";
            } else if ($cat->status_bayar == 1 && $cat->approval == 1) {
                $button_konfirmasi = '';

                if ($cat->approval == 1) {
                    $status = "Disetujui";
                } else {
                    $status = "Tidak Disetujui";
                }
            } else {
                $button_konfirmasi = '';
                if ($cat->expired_status_bayar > $now) {
                    $status = "Belum Dikonfirmasi User";
                } else {
                    $status = "Expired";
                }
            }
            $row[] = $status;
            $row[] = $button_update . ' ' . $button_aktifasi_expired . ' ' . $button_konfirmasi;

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

    public function edit_premium($id)
    {
        $this->db->select('*');
        $this->db->from('premium_confirmation');
        $this->db->where('id', $id);
        $data = $this->db->get()->row();
        $response = [
            'data' => $data, // This will contain the COA object/array
            'now' => (new DateTime())->format('Y-m-d H:i:s')
        ];
        echo json_encode($response);
    }

    public function update_premium()
    {
        $id = $this->input->post('id_edit');
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID tidak ditemukan']);
            return;
        }

        $tanggal_mulai = $this->input->post('tanggal_mulai');
        if ($tanggal_mulai) {
            $tanggal_mulai = str_replace('T', ' ', trim($tanggal_mulai));
            if (strlen($tanggal_mulai) === 16) {
                $tanggal_mulai .= ':00';
            }
        }

        $updateData = [
            'paket' => $this->input->post('paket'),
            'total_bulan' => $this->input->post('total_bulan'),
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $this->input->post('tanggal_selesai'),
        ];

        $this->db->where('id', $id);
        if ($this->db->update('premium_confirmation', $updateData)) {
            echo json_encode(['status' => 'success', 'message' => 'Data berhasil diperbarui']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data']);
        }
    }

    public function reactivate_expired()
    {
        $id = $this->input->post('id');
        if (!$id || !is_numeric($id)) {
            echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
            return;
        }

        $expired_status_bayar = (new DateTime())->modify('+24 hours')->format('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update('premium_confirmation', ['expired_status_bayar' => $expired_status_bayar]);

        if ($this->db->affected_rows() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Expired status diperpanjang 24 jam dari sekarang']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui expired status']);
        }
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
        $approval = $this->input->post('confirmation');
        $edit_data = [
            "approval" => $approval,
        ];
        $this->db->where('id', $id);
        if ($this->db->update('premium_confirmation', $edit_data)) {
            $confirmation_detail = $this->db->from('premium_confirmation')->where('Id', $id)->get()->row();
            $total_bulan = $confirmation_detail->total_bulan;

            // Set the start date to the current date and time
            $detail_perusahaan = $this->db->from('utility')->where('Id', $confirmation_detail->id_perusahaan)->get()->row();

            if ($detail_perusahaan->is_premium) {
                $raw_expired_date = $detail_perusahaan->expired_day ?? 'now';

                $expired_date_now = new DateTime($raw_expired_date);
                $expired_date_now->modify("+$total_bulan months");


                $start_date = $detail_perusahaan->start_day;
                $expired_date = $expired_date_now->format('Y-m-d H:i:s');

                // echo $expired_date;
            } else {
                $start_date = date('Y-m-d H:i:s');

                // Calculate the expired date by adding $total_bulan to the start date
                $expired_date = date('Y-m-d H:i:s', strtotime("+$total_bulan months"));
            }


            $this->db->select('users.*'); // Select all from users, and specific columns from t_cabang
            $this->db->from('users');
            $this->db->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
            $this->db->where('t_cabang.id_perusahaan', $confirmation_detail->id_perusahaan);
            $this->db->where('nama_jabatan', 'Super Admin');
            $this->db->where('level_jabatan', 99);
            $detail_user = $this->db->get()->row();

            if ($approval == 1) {
                // $confirmation_detail = $this->db->from('premium_confirmation')->where('Id', $id)->get()->row();
                // $perusahaan_detail = $this->db->from('premium_confirmation')->where('Id', $id)->get()->row();
                if ($confirmation_detail->paket == "Saudagar Kaya") {
                    $nama_paket = "Saudagar Kaya";
                    $kuota_invoice = 1000;
                    $kuota_memo = 100;
                    $kuota_pengajuan_biaya = 1000;
                    $kuota_user = 5;
                    $kuota_cabang = 1;
                    $is_premium = 0;
                } else if ($confirmation_detail->paket == "Bangsawan Muda") {
                    $nama_paket = "Bangsawan Muda";
                    $kuota_invoice = 3000;
                    $kuota_memo = 500;
                    $kuota_pengajuan_biaya = 3000;
                    $kuota_user = 15;
                    $kuota_cabang = 3;
                    $is_premium = 1;
                } else if ($confirmation_detail->paket == "Kesatria Sejati") {
                    $nama_paket = "Kesatria Sejati";
                    $kuota_invoice = 5000;
                    $kuota_memo = 1000;
                    $kuota_pengajuan_biaya = 5000;
                    $kuota_user = 25;
                    $kuota_cabang = 5;
                    $is_premium = 1;
                } else if ($confirmation_detail->paket == "Raja Sultan") {
                    $nama_paket = "Raja Sultan";
                    $kuota_invoice = 10000;
                    $kuota_memo = 3000;
                    $kuota_pengajuan_biaya = 10000;
                    $kuota_user = 50;
                    $kuota_cabang = 10;
                    $is_premium = 1;
                }
                $edit_data_perusahaan = [
                    "nama_paket" => $nama_paket,
                    "kuota_invoice" => $kuota_invoice,
                    "kuota_memo" => $kuota_memo,
                    "kuota_pengajuan_biaya" => $kuota_pengajuan_biaya,
                    "kuota_user" => $kuota_user,
                    "kuota_cabang" => $kuota_cabang,
                    "is_premium" => $is_premium,
                    "start_day" => $start_date,
                    "expired_day" => $expired_date,
                    // "start_date" => $confirmation_detail->tanggal_selesai,
                    // "expired_day" => $confirmation_detail->tanggal_selesai,
                ];
                $this->db->where('Id', $confirmation_detail->id_perusahaan);
                // $this->db->update('utility', $edit_data_perusahaan);

                if ($this->db->update('utility', $edit_data_perusahaan)) {

                    $this->db->select('users.*'); // Select all from users, and specific columns from t_cabang
                    $this->db->from('users');
                    $this->db->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
                    $this->db->where('t_cabang.id_perusahaan', $confirmation_detail->id_perusahaan);
                    $this->db->where('nama_jabatan', 'Super Admin');
                    $this->db->where('level_jabatan', 99);
                    $detail_user = $this->db->get()->row();
                    // Send a success response


                    $start_date_obj = new DateTime($confirmation_detail->tanggal_selesai);

                    $formatted_start_date = strtr($start_date_obj->format('d F Y'), $indonesian_months);

                    $id_invoice = NULL;

                    $keterangan = "PDT Paket *{$confirmation_detail->paket}*, Perusahaan *{$detail_perusahaan->nama_perusahaan}*.";

                    $this->posting('10201', '40102', $keterangan, $this->_parse_rupiah($confirmation_detail->nominal), $confirmation_detail->tanggal_bayar, $id_invoice);


                    $msg = "Terima kasih, pembayaran Anda telah berhasil kami konfirmasi. Akun Anda telah *di-upgrade* ke *premium*, berlaku hingga tanggal *{$formatted_start_date}*.Dimohon untuk logout akun dan login kembali untuk menikmati fitur premium anda. Kami harap Anda puas dengan layanan kami.";

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
                $msg = "Mohon maaf, permintaan konfirmasi pembayaran Anda untuk paket {$confirmation_detail->paket} tidak dapat kami setujui. Silakan periksa kembali rincian pembayaran atau hubungi tim support kami untuk bantuan lebih lanjut.";

                // $this->api_whatsapp->wa_notif($msg, "085157563305");
                if ($this->api_whatsapp->wa_notif($msg, $detail_user->phone)) {
                    echo json_encode(array("status" => 'success', "message" => "Berhasil Mengkonfirmasi"));
                }
                // echo json_encode(array("status" => 'success', "message" => "Berhasil Mengkonfirmasi"));
            }
        } else {
            echo json_encode(array("status" => 'error', "message" => "Gagal Mengkonfirmasi"));
        }
    }

    private function posting($coa_debit, $coa_kredit, $keterangan, $nominal, $tanggal, $id_invoice = NULL)
    {
        // Update coa debit 
        $update_saldo_debit = $this->update_saldo_coa($coa_debit, $nominal, 'debit');
        // Update coa kredit
        $update_saldo_kredit = $this->update_saldo_coa($coa_kredit, $nominal, 'kredit');


        // Ambil saldo debit
        $saldo_debit = $this->get_saldo_coa($coa_debit);
        // Ambil saldo kredit
        $saldo_kredit = $this->get_saldo_coa($coa_kredit);

        $dt_jurnal = [
            'tanggal' => $tanggal,
            'akun_debit' => $coa_debit,
            'jumlah_debit' => $nominal,
            'akun_kredit' => $coa_kredit,
            'jumlah_kredit' => $nominal,
            'saldo_debit' => $saldo_debit,
            'saldo_kredit' => $saldo_kredit,
            'keterangan' => $keterangan,
            'created_by' => $this->session->userdata('nip'),
            'id_invoice' => ($id_invoice) ? $id_invoice : '',
            'id_cabang' => $this->session->userdata('kode_cabang'),
            'id_company' => $this->session->userdata('user_perusahaan_id')
        ];

        $this->M_coa->addJurnal($dt_jurnal);

        $data_transaksi = [
            'user_id' => $this->session->userdata('nip'),
            'tgl_trs' => date('Y-m-d H:i:s'),
            'nominal' => $nominal,
            'debet' => $coa_debit,
            'kredit' => $coa_kredit,
            'keterangan' => trim($keterangan),
            'id_cabang' => $this->session->userdata('kode_cabang'),
            'id_company' => $this->session->userdata('user_perusahaan_id')
        ];

        $this->M_coa->add_transaksi($data_transaksi);
    }

    private function update_saldo_coa($akun_no, $jumlah, $tipe)
    {
        $substr_coa = substr($akun_no, 0, 1);
        if ($substr_coa == "1" || $substr_coa == "2" || $substr_coa == "3") {
            $table = "t_coa_sbb";
            $kolom = "no_sbb";
        } else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
            $table = "t_coalr_sbb";
            $kolom = "no_lr_sbb";
        }

        $query = $this->cb->query(
            "SELECT posisi, nominal FROM $table WHERE " . $kolom . " = ? AND id_cabang = " . $this->session->userdata('kode_cabang') . " FOR UPDATE",
            [$akun_no]
        );

        $row = $query->row();
        if (!$row) return FALSE;

        $posisi = $row->posisi;
        $nominal = $row->nominal;

        if ($posisi == 'AKTIVA') {
            if ($tipe == 'debit') {
                $nominal += $jumlah;
            } else { // kredit
                $nominal -= $jumlah;
            }
        } elseif ($posisi == 'PASIVA') {
            if ($tipe == 'debit') {
                $nominal -= $jumlah;
            } else { // kredit
                $nominal += $jumlah;
            }
        }

        // Update saldo
        $this->cb->where(($table == 't_coa_sbb') ? 'no_sbb' : 'no_lr_sbb', $akun_no);
        $this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
        $this->cb->update($table, ['nominal' => $nominal]);
    }

    private function get_saldo_coa($akun_no)
    {
        $substr_coa = substr($akun_no, 0, 1);
        if ($substr_coa == "1" || $substr_coa == "2" || $substr_coa == "3") {
            $table = "t_coa_sbb";
            $kolom = "no_sbb";
        } else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
            $table = "t_coalr_sbb";
            $kolom = "no_lr_sbb";
        }

        $row = $this->cb->select('nominal')
            ->where($kolom, $akun_no)
            ->where('id_cabang', $this->session->userdata('kode_cabang'))
            ->get($table)
            ->row();

        return $row->nominal;
    }

    private function _parse_rupiah($rupiah)
    {
        // Hilangkan Rp, titik, dan ganti koma dengan titik
        $rupiah = str_replace(['Rp', '.', ' '], '', $rupiah);
        return floatval(str_replace(',', '.', $rupiah));
    }
}
