<?php


defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx; // Make sure this 'use' statement is at the top of your file!
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup; // <--- ADD THIS LINE
use PhpOffice\PhpSpreadsheet\IOFactory; // <--- ADD THIS LINE FOR IOFactory


// Then your autoloader. This should also typically be near the top,
// after 'use' statements.
require APPPATH . 'third_party/autoload.php';

class Cuti extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        //$this->load->model('m_login');
        $this->load->model(['M_cuti', 'M_login']);
        $this->load->library(array('form_validation', 'session', 'user_agent', 'Api_Whatsapp', 'pdfgenerator'));
        $this->load->library('pagination');
        $this->load->database();
        $this->load->helper('url', 'form', 'download');
        $this->cb = $this->load->database('corebank', TRUE);

        if (!$this->session->userdata('nip')) {
            redirect('auth');
        }
    }

    function tgl_indo($tanggal, $cetak_hari = false)
    {
        $hari = array(
            1 => "Senin",
            "Selasa",
            "Rabu",
            "Kamis",
            "Jum'at",
            "Sabtu",
            "Minggu"
        );

        $bulan = array(
            1 =>   'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );
        $split     = explode('-', $tanggal);
        $tgl_indo = $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];

        if ($cetak_hari) {
            $num = date('N', strtotime($tanggal));
            return $hari[$num] . ', ' . $tgl_indo;
        }
        return $tgl_indo;
    }

    function view()
    {
        $nip = $this->session->userdata('nip');

        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('id', $this->session->userdata('user_user_id')); // Filter by username
        $query = $this->db->get()->row(); // Execute the query
        $supervisi = $query->supervisi; // Fetch results
        // var_dump($lokasi_presensi_by_id);
        if (empty($supervisi) || $supervisi == null || $supervisi == 0 || $supervisi == '0') {
            $this->session->set_flashdata('swal_message', [
                'icon' => 'info',
                'title' => 'Supervisor Belum Ditentukan',
                'text' => 'Anda harus memiliki supervisor untuk menggunakan fitur Approval. Silakan hubungi admin untuk pengaturan.',
                'confirmButtonText' => 'Mengerti',
            ]);
        }

        $data['sisa_cuti'] = $query->cuti;
        $data['jenis_cuti'] = $this->M_cuti->getJenisCuti();
        $data['all_jenis'] = $this->M_cuti->get_all_jenis_cuti();
        $data['karyawan'] = $this->M_cuti->getKaryawan();
        $data['bagian'] = $this->M_cuti->getBagian();

        $data['title'] = 'Cuti';
        $data['utility'] = $this->db->get('utility')->row_array();
        $data['user'] = $this->db->get_where('users', ['nip' => $nip])->row_array();
        $data['pages_script'] = 'script/cuti/s_cuti';
        $data['pages'] = 'pages/cuti/v_cuti';
        $data['menus'] = $this->M_menu->get_accessible_menus($nip);
        $this->load->view('index', $data);
    }

    public function cuti_all()
    {
        $nip = $this->session->userdata('nip');
        $user = $this->db->get_where('users', ['nip' => $nip])->row();
        if ($user->level_jabatan != '4') {
            redirect('cuti/view');
        }
        //inbox notif
        $data['jenis_cuti'] = $this->M_cuti->getJenisCuti();
        $data['all_jenis'] = $this->M_cuti->get_all_jenis_cuti();
        $data['karyawan'] = $this->M_cuti->getKaryawan();
        $data['bagian'] = $this->M_cuti->getBagian();

        $data['title'] = 'Cuti All';
        $data['utility'] = $this->db->get('utility')->row_array();
        $data['user'] = $this->db->get_where('users', ['nip' => $nip])->row_array();
        $data['pages_script'] = 'script/cuti/s_cuti_gm';
        $data['pages'] = 'pages/cuti/v_cuti_gm';
        $data['menus'] = $this->M_menu->get_accessible_menus($nip);

        $this->load->view('index', $data);
    }

    // public function cuti_all_gm()
    // {
    //     $nip = $this->session->userdata('nip');
    //     $usergm = $this->db->get_where('users', ['nip' => $nip])->row();
    //     $sql = "SELECT cuti.*, users.nama, users.nama_jabatan, jenis_cuti.nama_jenis, sub_jenis_cuti.nama_sub_jenis
    //             FROM cuti 
    //             JOIN users ON cuti.nip = users.nip
    //             JOIN jenis_cuti ON cuti.jenis = jenis_cuti.Id
    //             LEFT JOIN sub_jenis_cuti on cuti.detail_cuti = sub_jenis_cuti.Id
    //             WHERE users.bagian = '$usergm->bagian'
    //             ORDER BY cuti.id_cuti DESC";
    //     $result = $this->db->query($sql)->result();
    //     $i = 0;
    //     $no = 1;
    //     foreach ($result as $res) {
    //         // Atasan 
    //         $queryAtasan = "SELECT users.nama FROM users RIGHT JOIN cuti ON users.nip = '$res->atasan'";
    //         $atasan = $this->db->query($queryAtasan)->result();

    //         // HRD
    //         $queryHrd = "SELECT users.nama FROM users RIGHT JOIN cuti ON users.nip = '$res->hrd'";
    //         $hrd = $this->db->query($queryHrd)->result();

    //         // Status HRD
    //         if ($res->status_hrd == "Disetujui") {
    //             $statusHrd = "<p class='badge badge-success'>" . $res->status_hrd . ": " . $hrd[$i]->nama . "</p>";
    //         } else if ($res->status_hrd == "Ditolak") {
    //             $statusHrd = "<p class='badge badge-danger'>" . $res->status_hrd . ": " . $hrd[$i]->nama . "</p>";
    //         } else {
    //             $statusHrd = "";
    //         }

    //         // Status atasn
    //         if ($res->status_atasan == "Disetujui") {
    //             $statusAtasan = "<p class='badge badge-success'>" . $res->status_atasan . ": " . $atasan[$i]->nama . "</p>";
    //         } else if ($res->status_atasan == "Ditolak") {
    //             $statusAtasan = "<p class='badge badge-danger'>" . $res->status_atasan . ": " . $atasan[$i]->nama . "</p>";
    //         } else {
    //             $statusAtasan = "";
    //         }

    //         // Lihat dokumen yang diupload
    //         $lihatDokumen = '
    //             <span class="aksi badge badge-warning" onclick="detailCuti(' . $res->id_cuti . ')">
    //                 <i class="fa fa-eye" aria-hidden="true"></i> Detail
    //             </span>
    //             ';

    //         // History cuti karyawan
    //         $history = '
    //             <span class="aksi badge badge-secondary" onclick="historyCuti(' . $res->id_cuti . ')">
    //                 <i class="fa fa-history" aria-hidden="true"></i> History
    //             </span>
    //             ';

    //         $aksi = '
    //             <span class="aksi badge badge-success" onclick="update_cuti_atasan(' . $res->id_cuti . ')">
    //                 <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Update
    //             </span>
    //             ';

    //         $aksi = $res->status_atasan == null ? $aksi : "";

    //         $row = array();
    //         $row[] = $no++;
    //         $row[] = $res->nama;
    //         $row[] = $res->nama_jenis;
    //         $row[] = $res->alasan;
    //         $row[] = $statusHrd;
    //         $row[] = $statusAtasan;
    //         $row[] = $lihatDokumen . $history;
    //         $data['data'][] = $row;
    //         $i++;
    //     }

    //     count($result) > 0 ? $data = $data : $data['data'] = [];
    //     echo json_encode($data);
    // }

    public function ambilDataCuti()
    {
        $nip = $this->session->userdata('nip');
        $id_perusahaan = $this->session->userdata('user_perusahaan_id');

        $sql = "SELECT cuti.*, users.nama, users.nama_jabatan, jenis_cuti.nama_jenis, sub_jenis_cuti.nama_sub_jenis FROM cuti 
        JOIN users on cuti.nip = users.nip 
        JOIN jenis_cuti on cuti.jenis = jenis_cuti.Id 
        LEFT JOIN sub_jenis_cuti on cuti.detail_cuti = sub_jenis_cuti.Id
        WHERE cuti.nip = '$nip' AND cuti.id_perusahaan = '$id_perusahaan'
        ORDER BY cuti.id_cuti DESC";
        $result = $this->db->query($sql)->result();
        $no = 1;
        $i = 0;
        foreach ($result as $res) {
            // Atasan 
            $queryAtasan = "SELECT users.nama FROM users RIGHT JOIN cuti ON users.nip = '$res->atasan'";
            $atasan = $this->db->query($queryAtasan)->result();

            // Status atasn
            if ($res->status_atasan == "Disetujui") {
                // $statusAtasan = "<div class='bg-primary text-white'>" . $res->status_atasan . ": " . $atasan[$i]->nama . "</div>";
                $statusAtasan =  $res->status_atasan . ": " . $atasan[$i]->nama;
            } else if ($res->status_atasan == "Ditolak") {
                // $statusAtasan = "<div class='bg-pink text-white'>" . $res->status_atasan . ": " . $atasan[$i]->nama . "</div>";
                $statusAtasan =  $res->status_atasan . ": " . $atasan[$i]->nama;
            } else {
                $statusAtasan = "";
            }

            // Jika status masih kosong
            // $statusHrd = $res->status_hrd == null ? "<p class='badge'>Diajukan Kepada HRD</p>" : $statusHrd;
            $statusAtasan = ($res->status_atasan == null) ? "<div class='badge'>Diajukan Kepada Atasan</p>" : $statusAtasan;
            // $statusDirsdm = ($res->status_dirsdm == null && $res->status_hrd == "Disetujui" && $res->status_atasan == "Disetujui") ? "<p class='badge'>Diajukan Kepada Direktur SDM</p>" : $statusDirsdm;
            // $statusDirut = ($res->status_dirut == null && $res->status_hrd == "Disetujui" && $res->status_atasan == "Disetujui" && $res->status_dirsdm == "Disetujui") ? "<p class='badge'>Diajukan Kepada Direktur Utama</p>" : $statusDirut;


            $status_cuti = $statusAtasan;

            // Detail cuti 
            $lihatDetail = '
            <span class="aksi btn btn-primary" onclick="detailCuti(' . $res->id_cuti . ')">
                <i class="fa fa-eye" aria-hidden="true"></i> Detail
            </span>
            ';

            // Cetak form cuti
            $cetak = '
            <span class="aksi btn btn-pink" onclick="cetak(' . $res->id_cuti . ')" style="margin-top:2px">
                <i class="fa fa-print" aria-hidden="true"></i> Cetak
            </span>';

            $cetak = $res->status_atasan == "Disetujui" ? $cetak : "";



            if ($res->jenis == 2) {
                $jumlah_cuti = '1 Bulan';
            } else if ($res->jenis == 3) {
                $jumlah_cuti = '3 Bulan';
            } else {
                $jumlah_cuti = $res->jumlah_cuti . " hari";
            }

            $row = array();
            $row[] = $no++;
            $row[] = $res->nama;
            $row[] = $res->nama_jenis;
            $row[] = $res->alasan;
            $row[] = date('d F Y', strtotime($res->date_created));
            $row[] = $this->tgl_indo($res->tgl_cuti);
            $row[] = $jumlah_cuti;
            $row[] = $atasan[$i]->nama;
            $row[] = $status_cuti;

            $row[] = '<div style="display: flex; gap: 1px; align-items: center;">
' . $lihatDetail . $cetak . '</div>';
            // $row[] = $lihatDetail . $cetak;
            $row[] = $res->status_atasan; // THIS IS THE NEW HIDDEN COLUMN (index 10)
            $data['data'][] = $row;
            $i++;
        }
        count($result) > 0 ? $data = $data : $data['data'] = [];
        echo json_encode($data);
    }

    public function data_approve_atasan_view()
    {
        $nip = $this->session->userdata('nip');
        $user = $this->db->get_where('users', ['nip' => $nip])->row();
        if ($user->level_jabatan < '3') {
            redirect('cuti/view');
        }

        $data['jenis_cuti'] = $this->M_cuti->getJenisCuti();
        $data['all_jenis'] = $this->M_cuti->get_all_jenis_cuti();
        $data['karyawan'] = $this->M_cuti->getKaryawan();


        $data['title'] = 'Cuti All';
        $data['utility'] = $this->db->get('utility')->row_array();
        $data['user'] = $this->db->get_where('users', ['nip' => $nip])->row_array();
        $data['pages_script'] = 'script/cuti/s_atasan';
        $data['pages'] = 'pages/cuti/v_approve_atasan';
        $data['menus'] = $this->M_menu->get_accessible_menus($nip);

        $this->load->view('index', $data);
    }

    public function dataApproveAtasan()
    {
        $nip = $this->session->userdata('nip');
        $id_perusahaan = $this->session->userdata('user_perusahaan_id');

        // $sql = "SELECT cuti.*, users.nama, users.nama_jabatan, jenis_cuti.nama_jenis, sub_jenis_cuti.nama_sub_jenis
        //         FROM cuti 
        //         JOIN users ON cuti.nip = users.nip
        //         JOIN jenis_cuti ON cuti.jenis = jenis_cuti.Id
        //         LEFT JOIN sub_jenis_cuti on cuti.detail_cuti = sub_jenis_cuti.Id
        //         WHERE cuti.atasan = '$nip' AND cuti.id_perusahaan = '$id_perusahaan' AND 
        //         CASE 
        //             WHEN cuti.jenis = 2 THEN cuti.status_hrd = 'Disetujui' AND cuti.atasan != cuti.dirsdm AND cuti.atasan != cuti.dirut
        //             ELSE cuti.status_hrd = 'Disetujui'
        //         END
        //         ORDER BY cuti.id_cuti DESC";

        $sql = "SELECT cuti.*, users.nama, users.nama_jabatan, jenis_cuti.nama_jenis, sub_jenis_cuti.nama_sub_jenis
                FROM cuti 
                JOIN users ON cuti.nip = users.nip
                JOIN jenis_cuti ON cuti.jenis = jenis_cuti.Id
                LEFT JOIN sub_jenis_cuti on cuti.detail_cuti = sub_jenis_cuti.Id
                WHERE cuti.atasan = '$nip' AND cuti.id_perusahaan = '$id_perusahaan'
                ORDER BY cuti.id_cuti DESC";
        $result = $this->db->query($sql)->result();
        $i = 0;
        $no = 1;
        foreach ($result as $res) {
            if ($res->jenis == 2) {
                $jumlah_cuti = "1 Bulan";
            } else if ($res->jenis == 3) {
                $jumlah_cuti = "3 Bulan";
            } else {
                $jumlah_cuti = $res->jumlah_cuti . " Hari";
            }

            // Atasan 
            $queryAtasan = "SELECT users.nama FROM users RIGHT JOIN cuti ON users.nip = cuti.atasan WHERE cuti.atasan = '$nip'";
            $atasan = $this->db->query($queryAtasan)->result();

            // Status atasan
            // Status atasn
            if ($res->status_atasan == "Disetujui") {
                // $statusAtasan = "<div class='bg-primary text-white'>" . $res->status_atasan . ": " . $atasan[$i]->nama . "</div>";
                $statusAtasan =  $res->status_atasan . ": " . $atasan[$i]->nama;
            } else if ($res->status_atasan == "Ditolak") {
                // $statusAtasan = "<div class='bg-pink text-white'>" . $res->status_atasan . ": " . $atasan[$i]->nama . "</div>";
                $statusAtasan =  $res->status_atasan . ": " . $atasan[$i]->nama;
            } else {
                $statusAtasan = "";
            }
            // $statusAtasan = $res->status_atasan == "Disetujui"
            //     ? "<p class='badge badge-primary'>" . $res->status_atasan . "</p>"
            //     : "<p class='badge badge-pink'>" . $res->status_atasan . "</p>";

            // Jika status atasan atau status hrd tidak kosong
            $statusAtasan = $res->status_atasan != null ? $statusAtasan : "<p class=''>Belum diproses</p>";

            // Lihat dokumen yang diupload
            $lihatDokumen = '
                <span class="aksi btn btn-primary" onclick="detailCuti(' . $res->id_cuti . ')">
                    <i class="fa fa-eye" aria-hidden="true"></i> Detail
                </span>
                ';

            // History cuti karyawan
            $history = '
                <span class="aksi btn btn-dark" onclick="historyCuti(' . $res->id_cuti . ')">
                    <i class="fa fa-history" aria-hidden="true"></i> History
                </span>
                ';

            $aksi = '
                <span class="aksi btn btn-pink" onclick="update_cuti_atasan(' . $res->id_cuti . ')">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Update
                </span>
                ';

            $aksi = $res->status_atasan == null ? $aksi : "";

            $row = array();
            $row[] = $no++;
            $row[] = $res->nama;
            $row[] = $res->nama_jenis;
            $row[] = $res->alasan;
            $row[] = date('d F Y', strtotime($res->date_created));
            $row[] = $this->tgl_indo($res->tgl_cuti);
            $row[] = $jumlah_cuti;
            $row[] = $atasan[$i]->nama;
            $row[] = $statusAtasan;
            $row[] = '<div style="display: flex; gap: 1px; align-items: center;">
' . $aksi . $lihatDokumen . $history . '</div>';
            $row[] = $res->status_atasan;
            $data['data'][] = $row;
            $i++;
        }

        count($result) > 0 ? $data = $data : $data['data'] = [];
        echo json_encode($data);
    }

    // public function data_approve_hrd_view()
    // {
    //     $nip = $this->session->userdata('nip');
    //     $user = $this->db->get_where('users', ['nip' => $nip])->row();
    //     if ($user->level_jabatan != '3') {
    //         redirect('cuti/view');
    //     }

    //     $nip = $this->session->userdata('nip');

    //     $data['jenis_cuti'] = $this->M_cuti->getJenisCuti();
    //     $data['all_jenis'] = $this->M_cuti->get_all_jenis_cuti();
    //     $data['karyawan'] = $this->M_cuti->getKaryawan();

    //     $data['title'] = 'Cuti All';
    //     $data['utility'] = $this->db->get('utility')->row_array();
    //     $data['user'] = $this->db->get_where('users', ['nip' => $nip])->row_array();
    //     $data['pages_script'] = 'script/cuti/s_approve_hrd';
    //     $data['pages'] = 'pages/cuti/v_approve_hrd';
    //     $data['menus'] = $this->M_menu->get_accessible_menus($nip);

    //     $this->load->view('index', $data);
    //     // $this->load->view('approve_hrd', $data);
    // }

    // public function data_approve_hrd()
    // {
    //     $filter = $this->input->get('filter');
    //     $nip = $this->session->userdata('nip');
    //     $id_perusahaan = $this->session->userdata('user_perusahaan_id');

    //     $sql = "SELECT cuti.*, users.nama, users.nama_jabatan, jenis_cuti.nama_jenis, sub_jenis_cuti.nama_sub_jenis
    //             FROM cuti 
    //             JOIN users ON cuti.nip = users.nip
    //             JOIN jenis_cuti ON cuti.jenis = jenis_cuti.Id
    //             LEFT JOIN sub_jenis_cuti on cuti.detail_cuti = sub_jenis_cuti.Id
    //             WHERE cuti.nip != '$nip' AND cuti.id_perusahaan = '$id_perusahaan' AND
    //             cuti.status_atasan = 'Disetujui'
    //             AND  (cuti.tgl_cuti LIKE '%$filter%' OR cuti.akhir_cuti LIKE '%$filter%')
    //             ORDER BY cuti.id_cuti DESC";
    //     $result = $this->db->query($sql)->result();

    //     $i = 0;
    //     $no = 1;
    //     foreach ($result as $res) {
    //         if ($res->jenis == 2) {
    //             $jumlah_cuti = "1 Bulan";
    //         } else if ($res->jenis == 3) {
    //             $jumlah_cuti = "3 Bulan";
    //         } else {
    //             $jumlah_cuti = $res->jumlah_cuti . " Hari";
    //         }
    //         // Atasan 
    //         $queryAtasan = "SELECT users.nama FROM users RIGHT JOIN cuti ON users.nip = '$res->atasan'";
    //         $atasan = $this->db->query($queryAtasan)->result();

    //         // Status hrd
    //         $statusHrd = $res->status_hrd == "Disetujui"
    //             ? "<p class='badge badge-success'>" . $res->status_hrd . "</p>"
    //             : "<p class='badge badge-danger'>" . $res->status_hrd . "</p>";

    //         // Jika status atasan atau status hrd masih kosong
    //         $statusHrd = $res->status_hrd != null ? $statusHrd : "<p class='badge'>Belum Diproses</p>";

    //         // Lihat dokumen yang diupload
    //         $lihatDokumen = '
    //             <a class="aksi badge badge-warning" onclick="detailCuti(' . $res->id_cuti . ')">
    //                 <i class="fa fa-eye" aria-hidden="true"></i> Detail
    //             </a>
    //             ';

    //         // History cuti karyawan
    //         $history = '
    //             <a class="aksi badge badge-secondary" onclick="historyCuti(' . $res->id_cuti . ')">
    //                 <i class="fa fa-history" aria-hidden="true"></i> History
    //             </a>
    //             ';

    //         $aksi = '
    //             <a class="aksi badge badge-success" onclick="update_cuti_hrd(' . $res->id_cuti . ')">
    //                 <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Update
    //             </a>
    //             ';

    //         $aksi = $res->status_hrd == null ? $aksi : "";

    //         $row = array();
    //         $row[] = $no++;
    //         $row[] = $res->nama;
    //         $row[] = $res->nama_jenis;
    //         $row[] = $res->alasan;
    //         $row[] = date('d F Y', strtotime($res->date_created));
    //         $row[] = $this->tgl_indo($res->tgl_cuti);
    //         $row[] = $jumlah_cuti;
    //         $row[] = $atasan[$i]->nama;
    //         $row[] = $statusHrd;
    //         $row[] = $aksi . $lihatDokumen . $history;
    //         $data['data'][] = $row;
    //         $i++;
    //     }
    //     count($result) > 0 ? $data = $data : $data['data'] = [];
    //     echo json_encode($data);
    // }

    // public function data_approve_supervisi_view()
    // {
    //     $nip = $this->session->userdata('nip');
    //     $user = $this->db->get_where('users', ['nip' => $nip])->row();
    //     if ($user->level_jabatan != '2') {
    //         redirect('cuti/view');
    //     }

    //     $nip = $this->session->userdata('nip');

    //     $data['jenis_cuti'] = $this->M_cuti->getJenisCuti();
    //     $data['all_jenis'] = $this->M_cuti->get_all_jenis_cuti();
    //     $data['karyawan'] = $this->M_cuti->getKaryawan();

    //     $data['title'] = 'Cuti All';
    //     $data['utility'] = $this->db->get('utility')->row_array();
    //     $data['user'] = $this->db->get_where('users', ['nip' => $nip])->row_array();
    //     $data['pages_script'] = 'script/cuti/s_approve_supervisi';
    //     $data['pages'] = 'pages/cuti/v_approve_supervisi';
    //     $data['menus'] = $this->M_menu->get_accessible_menus($nip);

    //     $this->load->view('index', $data);
    //     // $this->load->view('approve_hrd', $data);
    // }

    // public function data_approve_supervisisss()
    // {
    //     $filter = $this->input->get('filter');
    //     $nip = $this->session->userdata('nip');
    //     $id_perusahaan = $this->session->userdata('user_perusahaan_id');

    //     $sql = "SELECT cuti.*, users.nama, users.nama_jabatan, jenis_cuti.nama_jenis, sub_jenis_cuti.nama_sub_jenis
    //             FROM cuti 
    //             JOIN users ON cuti.nip = users.nip
    //             JOIN jenis_cuti ON cuti.jenis = jenis_cuti.Id
    //             LEFT JOIN sub_jenis_cuti on cuti.detail_cuti = sub_jenis_cuti.Id
    //             WHERE cuti.nip != '$nip' AND cuti.id_perusahaan = '$id_perusahaan' AND  (cuti.tgl_cuti LIKE '%$filter%' OR cuti.akhir_cuti LIKE '%$filter%')
    //             ORDER BY cuti.id_cuti DESC";
    //     $result = $this->db->query($sql)->result();

    //     $i = 0;
    //     $no = 1;
    //     foreach ($result as $res) {
    //         if ($res->jenis == 2) {
    //             $jumlah_cuti = "1 Bulan";
    //         } else if ($res->jenis == 3) {
    //             $jumlah_cuti = "3 Bulan";
    //         } else {
    //             $jumlah_cuti = $res->jumlah_cuti . " Hari";
    //         }
    //         // Atasan 
    //         $queryAtasan = "SELECT users.nama FROM users RIGHT JOIN cuti ON users.nip = '$res->atasan'";
    //         $atasan = $this->db->query($queryAtasan)->row();

    //         // Status hrd
    //         $statusHrd = $res->status_hrd == "Disetujui"
    //             ? "<p class='badge badge-success'>" . $res->status_hrd . "</p>"
    //             : "<p class='badge badge-danger'>" . $res->status_hrd . "</p>";

    //         // Jika status atasan atau status hrd masih kosong
    //         $statusHrd = $res->status_hrd != null ? $statusHrd : "<p class='badge'>Belum Diproses</p>";

    //         // Lihat dokumen yang diupload
    //         $lihatDokumen = '
    //             <a class="aksi badge badge-warning" onclick="detailCuti(' . $res->id_cuti . ')">
    //                 <i class="fa fa-eye" aria-hidden="true"></i> Detail
    //             </a>
    //             ';

    //         // History cuti karyawan
    //         $history = '
    //             <a class="aksi badge badge-secondary" onclick="historyCuti(' . $res->id_cuti . ')">
    //                 <i class="fa fa-history" aria-hidden="true"></i> History
    //             </a>
    //             ';

    //         $aksi = '
    //             <a class="aksi badge badge-success" onclick="update_cuti_hrd(' . $res->id_cuti . ')">
    //                 <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Update
    //             </a>
    //             ';

    //         $aksi = $res->status_hrd == null ? $aksi : "";

    //         $row = array();
    //         $row[] = $no++;
    //         $row[] = $res->nama;
    //         $row[] = $res->nama_jenis;
    //         $row[] = $res->alasan;
    //         $row[] = date('d F Y', strtotime($res->date_created));
    //         $row[] = $this->tgl_indo($res->tgl_cuti);
    //         $row[] = $jumlah_cuti;
    //         // $row[] = $atasan[$i]->nama;
    //         $row[] = $atasan->nama;
    //         $row[] = $statusHrd;
    //         $row[] = $aksi . $lihatDokumen . $history;
    //         $data['data'][] = $row;
    //         $i++;
    //     }
    //     count($result) > 0 ? $data = $data : $data['data'] = [];
    //     echo json_encode($data);
    // }

    // public function data_approve_supervisi()
    // {
    //     $nip = $this->session->userdata('nip');
    //     $id_perusahaan = $this->session->userdata('user_perusahaan_id');

    //     $sql = "SELECT cuti.*, users.nama, users.nama_jabatan, jenis_cuti.nama_jenis, sub_jenis_cuti.nama_sub_jenis
    //             FROM cuti 
    //             JOIN users ON cuti.nip = users.nip
    //             JOIN jenis_cuti ON cuti.jenis = jenis_cuti.Id
    //             LEFT JOIN sub_jenis_cuti on cuti.detail_cuti = sub_jenis_cuti.Id
    //             WHERE cuti.atasan = '$nip' AND cuti.id_perusahaan = '$id_perusahaan'
    //             ORDER BY cuti.id_cuti DESC";
    //     $result = $this->db->query($sql)->result();
    //     $i = 0;
    //     $no = 1;
    //     foreach ($result as $res) {
    //         if ($res->jenis == 2) {
    //             $jumlah_cuti = "1 Bulan";
    //         } else if ($res->jenis == 3) {
    //             $jumlah_cuti = "3 Bulan";
    //         } else {
    //             $jumlah_cuti = $res->jumlah_cuti . " Hari";
    //         }

    //         // Atasan 
    //         $queryAtasan = "SELECT users.nama FROM users RIGHT JOIN cuti ON users.nip = cuti.atasan WHERE cuti.atasan = '$nip'";
    //         $atasan = $this->db->query($queryAtasan)->result();

    //         // Status atasan
    //         $statusAtasan = $res->status_atasan == "Disetujui"
    //             ? "<p class='badge badge-success'>" . $res->status_atasan . "</p>"
    //             : "<p class='badge badge-danger'>" . $res->status_atasan . "</p>";

    //         // Jika status atasan atau status hrd tidak kosong
    //         $statusAtasan = $res->status_atasan != null ? $statusAtasan : "<p class='badge'>Belum diproses</p>";

    //         // Lihat dokumen yang diupload
    //         $lihatDokumen = '
    //             <a class="aksi badge badge-warning" onclick="detailCuti(' . $res->id_cuti . ')">
    //                 <i class="fa fa-eye" aria-hidden="true"></i> Detail
    //             </a>
    //             ';

    //         // History cuti karyawan
    //         $history = '
    //             <a class="aksi badge badge-secondary" onclick="historyCuti(' . $res->id_cuti . ')">
    //                 <i class="fa fa-history" aria-hidden="true"></i> History
    //             </a>
    //             ';

    //         $aksi = '
    //             <a class="aksi badge badge-success" onclick="update_cuti_hrd(' . $res->id_cuti . ')">
    //                 <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Update
    //             </a>
    //             ';

    //         $aksi = $res->status_atasan == null ? $aksi : "";

    //         $row = array();
    //         $row[] = $no++;
    //         $row[] = $res->nama;
    //         $row[] = $res->nama_jenis;
    //         $row[] = $res->alasan;
    //         $row[] = date('d F Y', strtotime($res->date_created));
    //         $row[] = $this->tgl_indo($res->tgl_cuti);
    //         $row[] = $jumlah_cuti;
    //         $row[] = $atasan[$i]->nama;
    //         $row[] = $statusAtasan;
    //         $row[] = $aksi . $lihatDokumen . $history;
    //         $data['data'][] = $row;
    //         $i++;
    //     }

    //     count($result) > 0 ? $data = $data : $data['data'] = [];
    //     echo json_encode($data);
    // }

    function ambilDataDetail()
    {
        $id = $this->input->post('id');
        $cuti = $this->input->post('cuti');
        if ($cuti == "hrd") {
            $detail = $this->M_cuti->getDetailByJenis($id);
        }

        if ($cuti == "user") {
            $detail = $this->M_cuti->get_detail_by_jenis($id);
        }
        $jenis = $this->M_cuti->getDataJenis($id);

        $data = [
            'detail' => $detail,
            'jenis' => $jenis,
        ];

        echo json_encode($data);
    }

    function ambil_data_detail()
    {
        $id = $this->input->post('id');
        $detail = $this->M_cuti->get_detail_by_jenis($id);
        $jenis = $this->M_cuti->get_data_jenis($id);

        $data = [
            'detail' => $detail,
            'jenis' => $jenis,
        ];

        echo json_encode($data);
    }

    public function dataDetail()
    {
        $id = $this->input->post('idDetail');
        $detail = $this->M_cuti->getDataDetail($id);

        echo json_encode($detail);
    }

    public function sendCuti()
    {
        $nip = $this->input->post('nip');
        $jenisCuti = $this->input->post('jenisCuti');
        $detailCuti = $this->input->post('detailCuti');
        $alamatCuti = $this->input->post('alamat');
        $mulaiCuti = $this->input->post('mulaiCuti');
        $mulaiCuti = str_replace('/', '-', $mulaiCuti);
        $akhirCuti = $this->input->post('akhirCuti');
        $akhirCuti = str_replace('/', '-', $akhirCuti);
        $jumlahCuti = $this->input->post('jumlahCuti');
        $alasan = $this->input->post('alasan');
        $nipAtasan = $this->input->post('nipAtasan');

        // Form validation
        $this->form_validation->set_rules('jenisCuti', 'Jenis cuti', 'required', array(
            'required' => "%s wajib diisi!"
        ));
        $this->form_validation->set_rules('mulaiCuti', 'Mulai cuti', 'required', array(
            'required' => "%s wajib diisi!"
        ));
        $this->form_validation->set_rules('alasan', 'Alasan cuti', 'required|min_length[5]', array(
            'required' => "%s wajib diisi!",
            'min_length' => '%s setidaknya terdiri dari 5 karakter!'
        ));
        $this->form_validation->set_rules('alamat', 'Alamat cuti', 'required|min_length[5]', array(
            'required' => "%s wajib diisi!",
            'min_length' => "%s setidaknya terdiri dari 5 karakter"
        ));
        $this->form_validation->set_rules('mulaiCuti', 'Mulai cuti', 'required', array(
            'required' => "%s wajib diisi!"
        ));
        $this->form_validation->set_rules('akhirCuti', 'Akhir cuti', 'required', array(
            'required' => "%s wajib diisi!"
        ));
        $this->form_validation->set_rules('jumlahCuti', 'Jumlah cuti', 'required', array(
            'required' => "%s wajib diisi!"
        ));

        // Jika terdapat detail cuti
        $sql = "SELECT jenis_cuti.Id FROM jenis_cuti WHERE '$jenisCuti' in (SELECT parents FROM sub_jenis_cuti)";
        $detail = $this->db->query($sql)->result();
        $detail
            ? $this->form_validation->set_rules('detailCuti', 'Detail Cuti', 'required', array(
                'required' => '%s wajib diisi!'
            ))
            : "";

        // Jika cuti diperlukan dokumen
        $sql = "SELECT jenis_cuti.Id FROM jenis_cuti WHERE '$jenisCuti' in (SELECT jenis_cuti.Id FROM jenis_cuti WHERE file_pendukung = 1)";
        $dokumen = $this->db->query($sql)->result();

        // Jika Form Validation Berhasil Dijalankan
        if ($this->form_validation->run()) {
            if ($jumlahCuti < 1) {
                $data = [
                    'msg' => 'Jumlah cuti tidak boleh kurang dari satu hari',
                    'sukses' => false,
                ];
            } else {
                // Jika Cuti Memerlukan File Dokumen Pendukung
                if ($dokumen) {
                    $config = array(
                        'upload_path' => 'upload/cuti',
                        'allowed_types' => 'jpg|png|jpeg|pdf',
                        'file_name' => 'cuti-' . $nip . "-" . time()
                    );
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);

                    // mengecek apakah melakukan upload atau tidak
                    // Jika Melakukan Upload File
                    if ($this->upload->do_upload('file')) {
                        $file = $this->upload->data();
                        $fileUpload = $file['file_name'];
                        $params = [
                            'file_pendukung' => $fileUpload,
                            'nip' => $nip,
                            'jumlah_cuti' => $jumlahCuti,
                            'tgl_cuti' => date("Y-m-d", strtotime($mulaiCuti)),
                            'akhir_cuti' => date("Y-m-d", strtotime($akhirCuti)),
                            'alasan' => $alasan,
                            'alamat_cuti' => $alamatCuti,
                            'jenis' => $jenisCuti,
                            'detail_cuti' => $detailCuti,
                            'atasan' => $nipAtasan,
                            // 'dirsdm' => '2195903',
                            // 'dirut' => '2146501',
                            // 'hrd_bagian' => 4,
                            // 'hrd_jabatan' => 2,
                            'id_perusahaan' => $this->session->userdata('user_perusahaan_id')
                        ];

                        $this->M_cuti->insertCuti('cuti', $params);
                        $data = [
                            'sukses' => true,
                            'msg' => 'Cuti berhasil ditambahkan!',
                        ];
                        // Jika Tidak Melakukan Upload File
                    } else {
                        $data = [
                            'msg' => $this->upload->display_errors(),
                            'sukses' => false,
                        ];
                    }
                    // Jika Cuti Tidak Memerlukan File Dokumen Pendukung 
                } else {
                    // data yang akan dimasukan ke database
                    $params = [
                        'nip' => $nip,
                        'jumlah_cuti' => $jumlahCuti,
                        'tgl_cuti' => date("Y-m-d", strtotime($mulaiCuti)),
                        'akhir_cuti' => date("Y-m-d", strtotime($akhirCuti)),
                        'alasan' => $alasan,
                        'alamat_cuti' => $alamatCuti,
                        'jenis' => $jenisCuti,
                        'detail_cuti' => $detailCuti,
                        'atasan' => $nipAtasan,
                        // 'dirsdm' => '2195903',
                        // 'dirut' => '2146501',
                        // 'hrd_bagian' => 4,
                        // 'hrd_jabatan' => 3,
                        'id_perusahaan' => $this->session->userdata('user_perusahaan_id')
                    ];
                    // Mengecek jumlah cuti dengan sisa cuti
                    $this->db->select('cuti');
                    $sisa = $this->db->get_where('users', ['nip' => $nip])->row();

                    if ($jenisCuti == 1) {
                        if ($jumlahCuti > $sisa->cuti) {
                            $data = [
                                'sukses' => false,
                                'msg' => "Jumlah cuti melebihi sisa cuti anda!"
                            ];
                        } else {
                            $data = [
                                'sukses' => true,
                                'msg' => 'Cuti berhasil ditambahkan!',
                            ];
                            $this->M_cuti->insertCuti('cuti', $params);
                        }
                    } else {
                        $data = [
                            'sukses' => true,
                            'msg' => 'Cuti berhasil ditambahkan!',
                        ];
                        $this->M_cuti->insertCuti('cuti', $params);
                    }
                }
                // send notif wa
                $nama_session = $this->session->userdata('nama');
                $cuti_jenis = $this->db->get_where('jenis_cuti', ['Id' => $jenisCuti])->row();
                $jenis_nama = $cuti_jenis->nama_jenis;
                $msgatasan = "*Pengajuan Cuti*\n\nFrom: *$nama_session*\nJenis Cuti: *$jenis_nama*\nMohon untuk segera diproses.";
                // $hrd = $this->db->get_where('users', ['bagian' => 4, 'status' => 1])->result();
                // $hrd = $this->db->get_where('users', ['bagian' => $this->input->post('tujuanCuti'), 'status' => 1])->result();
                $atasan = $this->db->get_where('users', ['nip' => $nipAtasan])->row();
                if (!$atasan) {
                    $data = [
                        'sukses' => false,
                        'msg' => "User atasan tidak ditemukan!"
                    ];
                    echo json_encode($data);
                    return;
                }

                if ($this->session->userdata('is_premium')) {
                    $this->api_whatsapp->wa_notif($msgatasan, $atasan->phone);
                }
            }
            // Jika Form Validation Gagal Dijalankan 
        } else {
            $data = [
                'sukses' => false,
                'msg' => "Mohon periksa kembali data cuti anda!",
                'err_jenis' => form_error('jenisCuti'),
                'err_detail' => form_error('detailCuti'),
                'err_alasan' => form_error('alasan'),
                'err_detail' => form_error('detailCuti'),
                'err_mulai' => form_error('mulaiCuti'),
                'err_akhir' => form_error('akhirCuti'),
                'err_jumlah' => form_error('jumlahCuti'),
                'err_alamat' => form_error('alamat'),
            ];
        }
        echo json_encode($data);
    }

    public function approveAtasan($id)
    {
        $cuti = $this->M_cuti->getDataCuti($id);
        $user = $this->db->get_where('users', ['nip' => $cuti['nip']])->row();

        // $this->db->where('nip !=', $user['nip']);
        // $this->db->where('supervisi', $user['supervisi']);
        // $this->db->or_where('bagian', $user['bagian']);
        // $pengganti = $this->db->get('users')->result_array();
        $sql = "SELECT * FROM users WHERE (bagian ='$user->bagian' OR supervisi = '$user->supervisi') AND nip != '$user->nip'";
        $pengganti = $this->db->query($sql)->result_array();

        $option = "<option value=''>-- Pilih Pengganti --</option>";
        foreach ($pengganti as $row) {
            $option .= "<option value='$row[nip]'>$row[nama]</option>";
        }

        $data = [
            'cuti' => $cuti,
            'user' => $user,
            'pengganti' => $pengganti,
            'option' => $option
        ];
        echo json_encode($data);
    }

    public function update_cuti_atasan($id)
    {
        $pengganti = $this->input->post('pengganti');
        $catatan = $this->input->post('catatan');
        $status = $this->input->post('status_cuti');

        $cuti = $this->db->get_where('cuti', ['id_cuti' => $id])->row();
        $user = $this->db->get_where('users', ['nip' => $cuti->nip])->row();
        $atasan = $this->db->get_where('users', ['nip' => $cuti->atasan])->row();
        $dirsdm = $this->db->get_where('users', ['nip' => $cuti->dirsdm])->row();

        $this->form_validation->set_rules('status_cuti', 'Status cuti', 'required', array('required' => '%s wajib diisi'));
        if ($status == "Disetujui") {
            // $this->form_validation->set_rules('pengganti', 'Pengganti', 'required', array('required' => '%s wajib diisi'));
        }

        if ($this->form_validation->run()) {

            $params = [
                'pengganti' => $pengganti,
                'status_atasan' => $status,
                'catatan_atasan' => $catatan
            ];

            $where = [
                'id_cuti' => $id
            ];
            // update sisa cuti
            if ($status == "Disetujui") {
                if ($cuti->jenis == 1) { //jenis cuti tahunan
                    $sisa = $user->cuti - $cuti->jumlah_cuti;
                    if ($sisa < 0) {
                        $data = [
                            'error' => true,
                            'msg' => 'Mohon maaf jumlah cuti melebihi sisa cuti!'
                        ];
                    } else {
                        $this->M_cuti->updateAtasan($params, $where);

                        $this->db->set('cuti', $sisa);
                        $this->db->where('nip', $cuti->nip);
                        $this->db->update('users');

                        $data = [
                            'error' => false,
                            'msg' => 'Cuti berhasil ' . $status . '!'
                        ];

                        $catatan = $catatan != "" || $catatan != null ? $catatan : "Tidak ada catatan";
                        $msg = "*Notifikasi Cuti*\n\nCuti anda selesai di proses oleh *" . $atasan->nama . "* sebagai atasan/supervisi, dengan status *" . $status . "*.\n\n*Catatan* : " . $catatan;

                        if ($this->session->userdata('is_premium')) {
                            $this->api_whatsapp->wa_notif($msg, $user->phone);
                        }
                    }
                } else {
                    $data = [
                        'error' => false,
                        'msg' => 'Cuti berhasil ' . $status . '!'
                    ];

                    $this->M_cuti->updateAtasan($params, $where);
                    $catatan = $catatan != "" || $catatan != null ? $catatan : "Tidak ada catatan";
                    $msg = "*Notifikasi Cuti*\n\nCuti anda selesai di proses oleh *" . $atasan->nama . "* sebagai atasan/supervisi, dengan status *" . $status . "*.\n\n*Catatan* : " . $catatan;
                    if ($this->session->userdata('is_premium')) {

                        $this->api_whatsapp->wa_notif($msg, $user->phone);
                    }
                }
            } else {
                $data = [
                    'error' => false,
                    'msg' => 'Cuti berhasil ' . $status . '!'
                ];
                $this->M_cuti->updateAtasan($params, $where);

                $catatan = $catatan != "" || $catatan != null ? $catatan : "Tidak ada catatan";
                $msg = "*Notifikasi Cuti*\n\nCuti anda selesai di proses oleh *" . $atasan->nama . "* sebagai atasan/supervisi, dengan status *" . $status . "*.\n\n*Catatan* : " . $catatan;
                if ($this->session->userdata('is_premium')) {

                    $this->api_whatsapp->wa_notif($msg, $user->phone);
                }
            }
        } else {
            $data = [
                'error' => true,
                'msg' => 'Cuti gagal diupdate!',
                'err_status_cuti' => form_error('status_cuti'),
                'err_pengganti' => form_error('pengganti'),
            ];
        }
        echo json_encode($data);
    }

    // public function update_cuti_hrd($id)
    // {
    //     $hrd = $this->db->get_where('users', ['nip' => $this->session->userdata['nip']])->row();
    //     $cuti = $this->db->get_where('cuti', ['id_cuti' => $id])->row();
    //     $user = $this->db->get_where('users', ['nip' => $cuti->nip])->row();
    //     $atasan = $this->db->get_where('users', ['nip' => $cuti->atasan])->row();
    //     $dirsdm = $this->db->get_where('users', ['nip' => $cuti->dirsdm])->row();
    //     $status = $this->input->post('status_cuti');
    //     $catatan = $this->input->post('catatan');

    //     // Form validation
    //     $this->form_validation->set_rules('status_cuti', 'Status cuti', 'required', array(
    //         'required' => "%s wajib diisi!"
    //     ));

    //     if ($this->form_validation->run()) {
    //         $where = [
    //             'id_cuti' => $id
    //         ];

    //         $params = [
    //             'hrd' => $hrd->nip,
    //             'status_hrd' => $status,
    //             'catatan_hrd' => $catatan
    //         ];

    //         $this->M_cuti->statusHrd($params, $where);

    //         $data = [
    //             'error' => false,
    //             'msg' => 'Status cuti berhasil ' . $status . '!'
    //         ];

    //         if ($status == 'Disetujui') {
    //             if ($cuti->jenis == 2) {
    //                 if ($cuti->atasan == $cuti->dirsdm || $cuti->atasan == $cuti->dirut) {
    //                     $cuti_jenis = $this->db->get_where('jenis_cuti', ['Id' => $cuti->jenis])->row();
    //                     $jenis_nama = $cuti_jenis->nama_jenis;
    //                     $msg = "*Pengajuan Cuti*\n\nFrom: *$user->nama*\nJenis Cuti: *$jenis_nama*\n\nSudah selesai diproses oleh *" . $hrd->nama . "* sebagai HRD. Untuk selanjutnya diproses oleh anda sebagai Direktur SDM.";
    //                     $this->api_whatsapp->wa_notif($msg, $dirsdm->phone);
    //                 } else {
    //                     $cuti_jenis = $this->db->get_where('jenis_cuti', ['Id' => $cuti->jenis])->row();
    //                     $jenis_nama = $cuti_jenis->nama_jenis;
    //                     $msg = "*Pengajuan Cuti*\n\nFrom: *$user->nama*\nJenis Cuti: *$jenis_nama*\n\nSudah selesai diproses oleh *" . $hrd->nama . "* sebagai HRD. Untuk selanjutnya diproses oleh anda sebagai atasan/supervisi.";
    //                     $this->api_whatsapp->wa_notif($msg, $atasan->phone);
    //                 }
    //             } else {
    //                 $cuti_jenis = $this->db->get_where('jenis_cuti', ['Id' => $cuti->jenis])->row();
    //                 $jenis_nama = $cuti_jenis->nama_jenis;
    //                 $msg = "*Pengajuan Cuti*\n\nFrom: *$user->nama*\nJenis Cuti: *$jenis_nama*\n\nSudah selesai diproses oleh *" . $hrd->nama . "* sebagai HRD. Untuk selanjutnya diproses oleh atasan/supervisi.";
    //                 $this->api_whatsapp->wa_notif($msg, $atasan->phone);
    //             }
    //         } else {
    //             $cuti_jenis = $this->db->get_where('jenis_cuti', ['Id' => $cuti->jenis])->row();
    //             $jenis_nama = $cuti_jenis->nama_jenis;
    //             $msgg = "*Pengajuan Cuti*\n\nFrom: *$user->nama*\nJenis Cuti: *$jenis_nama*\n\nSudah selesai diproses oleh *" . $hrd->nama . "* sebagai HRD, dengan status *" . $status . "*.\n\n*Catatan* : " . $catatan;
    //             $this->api_whatsapp->wa_notif($msgg, $atasan->phone);
    //         }

    //         $catatan = $catatan != "" || $catatan != null ? $catatan : "Tidak ada catatan";

    //         $msg = "*Notifikasi Cuti*\n\nCuti anda selesai di proses oleh *" . $hrd->nama . "* sebagai HRD, dengan status *" . $status . "*.\n\n*Catatan* : " . $catatan;
    //         $this->api_whatsapp->wa_notif($msg, $user->phone);
    //     } else {
    //         $data = [
    //             'error' => true,
    //             'msg' => "Cuti gagal diupdate!",
    //             'err_status' => form_error('status_cuti'),
    //         ];
    //     }
    //     echo json_encode($data);
    // }

    // public function update_cuti_direksi($id)
    // {
    //     $status = $this->input->post('status_cuti');
    //     $catatan = $this->input->post('catatan');
    //     $direksi = $this->input->post('direksi');
    //     $pengganti = $this->input->post('pengganti');

    //     $cuti = $this->db->get_where('cuti', ['id_cuti' => $id])->row();
    //     $user = $this->db->get_where('users', ['nip' => $cuti->nip])->row();
    //     $dirsdm = $this->db->get_where('users', ['nip' => $cuti->dirsdm])->row();
    //     $dirut = $this->db->get_where('users', ['nip' => $cuti->dirut])->row();
    //     $user_peng = $this->db->get_where('users', ['nip' => $pengganti])->row();

    //     // Form validation
    //     $this->form_validation->set_rules('status_cuti', 'Status cuti', 'required', array(
    //         'required' => "%s wajib diisi!"
    //     ));

    //     if ($status == 'Disetujui') {
    //         if ($direksi == 'dirsdm') {
    //             if ($cuti->atasan == $cuti->dirsdm) {
    //                 $this->form_validation->set_rules('pengganti', 'Pengganti', 'required', array(
    //                     'required' => "%s wajib diisi!"
    //                 ));
    //             }
    //         } else {
    //             if ($cuti->atasan == $cuti->dirut) {
    //                 $this->form_validation->set_rules('pengganti', 'Pengganti', 'required', array(
    //                     'required' => "%s wajib diisi!"
    //                 ));
    //             }
    //         }
    //     }

    //     if ($this->form_validation->run()) {
    //         if ($direksi == 'dirsdm') {
    //             $where = [
    //                 'id_cuti' => $id
    //             ];

    //             if ($cuti->atasan == $cuti->dirsdm) {
    //                 $params = [
    //                     'status_dirsdm' => $status,
    //                     'catatan_dirsdm' => $catatan,
    //                     'pengganti' => $pengganti,
    //                     'status_atasan' => $status,
    //                     'catatan_atasan' => $catatan
    //                 ];

    //                 if ($status == 'Disetujui') {
    //                     $msg = "*Pemberitahuan Pengganti*\n\nDikarenakan *$user->nama* mengajukan cuti, *$dirsdm->nama* sebagai Direktur SDM menunjuk anda sebagai pengganti.";
    //                     $this->api_whatsapp->wa_notif($msg, $user_peng->phone);
    //                 }

    //                 $catatan = $catatan != "" || $catatan != null ? $catatan : "Tidak ada catatan";
    //                 $msg = "*Notifikasi Cuti*\n\nCuti anda selesai di proses oleh *" . $dirsdm->nama . "* sebagai atasan/supervisi dan Direktur SDM, dengan status *" . $status . "*.\n\n*Catatan* : " . $catatan;
    //                 $this->api_whatsapp->wa_notif($msg, $user->phone);
    //             } else {
    //                 $params = [
    //                     'status_dirsdm' => $status,
    //                     'catatan_dirsdm' => $catatan,
    //                 ];

    //                 $catatan = $catatan != "" || $catatan != null ? $catatan : "Tidak ada catatan";
    //                 $msg = "*Notifikasi Cuti*\n\nCuti anda selesai di proses oleh *" . $dirsdm->nama . "* sebagai Direktur SDM, dengan status *" . $status . "*.\n\n*Catatan* : " . $catatan;
    //                 $this->api_whatsapp->wa_notif($msg, $user->phone);
    //             }

    //             if ($status == 'Disetujui') {
    //                 $cuti_jenis = $this->db->get_where('jenis_cuti', ['Id' => $cuti->jenis])->row();
    //                 $jenis_nama = $cuti_jenis->nama_jenis;
    //                 $msg = "*Pengajuan Cuti*\n\nFrom: *$user->nama*\nJenis Cuti: *$jenis_nama*\n\nSudah selesai diproses oleh *" . $dirsdm->nama . "* sebagai Direktur SDM. Untuk selanjutnya diproses oleh anda sebagai Direktur Utama.";
    //                 $this->api_whatsapp->wa_notif($msg, $dirut->phone);
    //             }
    //         }

    //         if ($direksi == 'dirut') {
    //             $where = [
    //                 'id_cuti' => $id
    //             ];

    //             if ($cuti->atasan == $cuti->dirut) {
    //                 $params = [
    //                     'status_dirut' => $status,
    //                     'catatan_dirut' => $catatan,
    //                     'pengganti' => $pengganti,
    //                     'status_atasan' => $status,
    //                     'catatan_atasan' => $catatan
    //                 ];

    //                 if ($status == 'Disetujui') {
    //                     $msg = "*Pemberitahuan Pengganti*\n\nDikarenakan *$user->nama* mengajukan cuti, *$dirut->nama* sebagai Direktur Utama menunjuk anda sebagai pengganti.";
    //                     $this->api_whatsapp->wa_notif($msg, $user_peng->phone);
    //                 }

    //                 $catatan = $catatan != "" || $catatan != null ? $catatan : "Tidak ada catatan";
    //                 $msg = "*Notifikasi Cuti*\n\nCuti anda selesai di proses oleh *" . $dirut->nama . "* sebagai atasan/supervisi dan Direktur Utama, dengan status *" . $status . "*.\n\n*Catatan* : " . $catatan;
    //                 $this->api_whatsapp->wa_notif($msg, $user->phone);
    //             } else {
    //                 $params = [
    //                     'status_dirut' => $status,
    //                     'catatan_dirut' => $catatan,
    //                 ];

    //                 $catatan = $catatan != "" || $catatan != null ? $catatan : "Tidak ada catatan";
    //                 $msg = "*Notifikasi Cuti*\n\nCuti anda selesai di proses oleh *" . $dirut->nama . "* sebagai Direktur Utama, dengan status *" . $status . "*.\n\n*Catatan* : " . $catatan;
    //                 $this->api_whatsapp->wa_notif($msg, $user->phone);
    //             }

    //             if ($status == "Disetujui") {
    //                 $this->db->set(['cuti_panjang' => 1]);
    //                 $this->db->where(['nip' => $cuti->nip]);
    //                 $this->db->update('users');
    //             }
    //         }

    //         $this->M_cuti->approveDireksi($params, $where);
    //         $data = [
    //             'error' => false,
    //             'msg' => 'Status cuti berhasil ' . $status . '!'
    //         ];
    //     } else {
    //         $data = [
    //             'error' => true,
    //             'msg' => "Cuti gagal diupdate!",
    //             'err_status' => form_error('status_cuti'),
    //             'err_pengganti' => form_error('pengganti')
    //         ];
    //     }


    //     echo json_encode($data);
    // }


    public function detailCuti($id)
    {
        $where = [
            'id_cuti' => $id
        ];

        $detailCuti = $this->M_cuti->detailCutiById('cuti', $where);

        if ($detailCuti['jenis'] == 2) {
            $jumlah_cuti = "1 Bulan";
        } else if ($detailCuti['jenis'] == 3) {
            $jumlah_cuti = "3 Bulan";
        } else {
            $jumlah_cuti = $detailCuti['jumlah_cuti'] . " Hari";
        }

        // Nama Karyawan
        $this->db->select('nama, cuti');
        $nama = $this->db->get_where('users', ['nip' => $detailCuti['nip']])->row();

        // Nama Pengganti 
        $this->db->select('nama');
        $pengganti = $this->db->get_where('users', ['nip' => $detailCuti['pengganti']])->row();

        // Nama Atasan
        $this->db->select('nama');
        $atasan = $this->db->get_where('users', ['nip' => $detailCuti['atasan']])->row();

        // Nama Hrd
        $this->db->select('nama');
        $hrd = $this->db->get_where('users', ['nip' => $detailCuti['hrd']])->row();

        // Nama dirsdm
        $this->db->select('nama');
        $dirsdm = $this->db->get_where('users', ['nip' => $detailCuti['dirsdm']])->row();

        // Nama dirut
        $this->db->select('nama');
        $dirut = $this->db->get_where('users', ['nip' => $detailCuti['dirut']])->row();

        // Nama Jenis Cuti
        $this->db->select('nama_jenis');
        $jenis = $this->db->get_where('jenis_cuti', ['Id' => $detailCuti['jenis']])->row();

        // Nama Sub Jenis Cuti
        $this->db->select('nama_sub_jenis');
        $subJenis = $this->db->get_where('sub_jenis_cuti', ['Id' => $detailCuti['detail_cuti']])->row();

        // Cek apakah ada sub jenis atau tidak
        $sql = ("SELECT jenis_cuti.Id FROM jenis_cuti WHERE jenis_cuti.Id = " . $detailCuti["jenis"] . " AND jenis_cuti.Id IN (SELECT sub_jenis_cuti.parents FROM sub_jenis_cuti)");
        $adaSub = $this->db->query($sql)->num_rows();
        $subJenis = $adaSub > 0 ? $subJenis->nama_sub_jenis : "-";

        // Pengganti
        $pengganti = is_null($detailCuti['pengganti']) || $detailCuti['pengganti'] == "" ? "Belum ada pengganti" : $pengganti->nama;

        // View File Pendukung
        $download = '<a class="badge badge-success" href=' . base_url('upload/cuti/') . $detailCuti['file_pendukung'] . ' target="_blank">View</a>';
        $file_pendukung = is_null($detailCuti['file_pendukung']) ? "Tidak memerlukan file pendukung" : $detailCuti['file_pendukung'] . ' | ' . $download;

        $data = '
            <tr>
				<th>NIP</th>
                <td>:</td>
				<td>' . $detailCuti["nip"] . '</td>
			</tr>
            <tr>
				<th>Nama</th>
                <td>:</td>
				<td>' . $nama->nama . '</td>
			</tr>
            <tr>
				<th>Jenis Cuti</th>
                <td>:</td>
				<td>' . $jenis->nama_jenis . '</td>
			</tr>
            <tr>
				<th>Detail Cuti</th>
                <td>:</td>
				<td>' . $subJenis . '</td>
			</tr>
            <tr>
				<th>Alasan</th>
                <td>:</td>
				<td>' . $detailCuti['alasan'] . '</td>
			</tr>
            <tr>
				<th>Alamat Cuti</th>
                <td>:</td>
				<td>' . $detailCuti['alamat_cuti'] . '</td>
			</tr>
            <tr>
				<th>Dari</th>
                <td>:</td>
				<td>' . $this->tgl_indo($detailCuti['tgl_cuti'], true) . '</td>
			</tr>
            <tr>
				<th>Sampai</th>
                <td>:</td>
				<td>' . $this->tgl_indo($detailCuti['akhir_cuti'], true) . '</td>
			</tr>
            <tr>
				<th>Jumlah Cuti</th>
                <td>:</td>
				<td>' . $jumlah_cuti . '</td>
			</tr>
            <tr>
				<th>Pengganti</th>
                <td>:</td>
				<td>' . $pengganti . '</td>
			</tr>
            <tr>
				<th>Atasan</th>
                <td>:</td>
				<td>' . $atasan->nama . '</td>
			</tr>
            <tr>
				<th>File Pendukung</th>
                <td>:</td>
				<td>' . $file_pendukung . '</td>
			</tr>
            <tr>
				<th>Sisa Cuti</th>
                <td>:</td>
				<td>' . $nama->cuti . '</td>
			</tr>
    ';

        echo json_encode($data);
    }

    public function cetakPdf($id)
    {
        $nipsession = $this->session->userdata('nip');
        $cuti = $this->M_cuti->getDataCuti($id);

        if ($nipsession != $cuti['nip']) {
            redirect('cuti/view');
        }


        if ($cuti['status_atasan'] != 'Disetujui') {
            redirect('cuti/view');
        }


        if ($cuti['jenis'] == 2) {
            $jumlah_cuti = "1 Bulan";
        } else {
            $jumlah_cuti = $cuti['jumlah_cuti'] . " Hari";
        }

        // User
        $this->db->select('cuti, cuti_haji, cuti_panjang, cuti_spiritual, nama,nama_jabatan,bagian,tmt');
        $user = $this->db->get_where('users', ['nip' => $cuti['nip']])->row_array();

        // Pengganti
        $this->db->select('nama, nip, nama_jabatan');
        $pengganti = $this->db->get_where('users', ['nip' => $cuti['pengganti']])->row_array();

        // Atasan
        $this->db->select('nama, nama_jabatan');
        $atasan = $this->db->get_where('users', ['nip' => $cuti['atasan']])->row_array();

        // Divisi
        $this->db->select('nama, kode_nama');
        $bagian = $this->db->get_where('bagian', ['Id' => $user['bagian']])->row_array();

        $jenis = $this->M_cuti->getJenisCuti();

        $data = [
            'jenis' => $jenis,
            'cuti' => $cuti,
            'user' => $user,
            'bagian' => $bagian,
            'pengganti' => $pengganti,
            'atasan' => $atasan,
            // 'hrd' => $hrd,
            'jumlah_cuti' => $jumlah_cuti,

        ];
        // include APPPATH . 'libraries/dompdfv-3/autoload.inc.php';

        // // $this->load->view('cetak_form_cuti', $data);
        // $dompdf = new Dompdf\Dompdf();
        // $dompdf->loadHtml($this->load->view('cetak_form_cuti', $data, true));
        // // (Optional) Setup the paper size and orientation
        // $dompdf->setPaper('a4', 'potrait');

        // // Render the HTML as PDF
        // $dompdf->render();

        // // Output the generated PDF to Browser
        // $dompdf->stream('form-cuti-' . $cuti['nip'] . '.pdf', array("Attachment" => 0));
        $file_pdf = 'form-cuti- ' . $cuti['nip'];

        // setting paper
        $paper = 'A4';

        //orientasi paper potrait / landscape
        $orientation = "portrait";
        // $this->load->view('pages/cuti/cetak_form_cuti', $data);

        $html = $this->load->view('pages/cuti/cetak_form_cuti', $data, true);

        // run dompdf
        $this->pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
    }

    public function historyCuti($id)
    {
        //inbox notif
        $nip = $this->session->userdata('nip');

        $data['data'] = $this->M_cuti->getDataCuti($id);
        $data['users'] = $this->db->get_where('users', ['nip' => $data['data']['nip']])->row_array();
        $data['historyCuti'] = $this->M_cuti->historyCutiById($id);

        $data['title'] = 'Cuti All';
        $data['utility'] = $this->db->get('utility')->row_array();
        $data['user'] = $this->db->get_where('users', ['nip' => $nip])->row_array();
        $data['pages_script'] = 'script/cuti/s_history_cuti';
        $data['pages'] = 'pages/cuti/v_history_cuti';
        $data['menus'] = $this->M_menu->get_accessible_menus($nip);

        $this->load->view('index', $data);
    }

    public function resetCuti()
    {
        $nipSession = $this->input->post('nip');
        $this->db->set(['cuti' => 12]);
        $this->db->where(['nip' => $nipSession]);
        $this->db->update('users');

        // $data = [
        //     'msg' => "Reset berhasil!",
        //     'err' => false
        // ];
        $data = [
            'status' => "success",
            'message' => "Reset berhasil!"
        ];
        echo json_encode($data);
    }

    public function resetCutiAll()
    {
        $password = $this->input->post('password');
        $this->form_validation->set_rules('password', 'password', 'required');

        if ($this->form_validation->run() == FALSE) {
            $response = [
                'success' => false,
                'msg' => array_values($this->form_validation->error_array())[0]
            ];
        } else {
            $data = $this->M_login->datapengguna($this->session->userdata('username'));
            if (password_verify($password, $data->password)) {
                $id_perusahaan = $this->session->userdata('user_perusahaan_id');

                $this->db->select('users.id'); // Select the user ID
                $this->db->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
                $this->db->where('t_cabang.id_perusahaan', $id_perusahaan);
                $user_ids_to_update = $this->db->get('users')->result_array();

                // Check if any users were found
                if (!empty($user_ids_to_update)) {
                    // Step 2: Use the user IDs in the UPDATE statement
                    // First, convert the array of objects into a simple array of IDs
                    $ids = array_column($user_ids_to_update, 'id');

                    // Perform the batch update
                    $this->db->set('cuti', 12);
                    $this->db->where_in('id', $ids); // Use where_in to update multiple IDs
                    $this->db->update('users');
                }

                $response = [
                    'success' => true,
                    'msg' => "Reset berhasil!",
                    'reload' => base_url('perusahaan/user')
                ];
            } else {
                $response = [
                    'success' => false,
                    'msg' => 'Password salah!'
                ];
            }
        }

        echo json_encode($response);
    }

    public function export_cuti($filter = null)
    {

        $nipSession = $this->session->userdata('nip');
        $user = $this->db->get_where('users', ['nip' => $nipSession])->row();

        if ($user->level_jabatan != 3) {
            redirect('cuti/view');
        } else {
            // echo '<table><tbody>';
            $no = 1;


            // include APPPATH . 'third_party/PHPExcel/PHPExcel.php';

            // $excel = new PHPExcel();
            // require APPPATH . 'third_party/autoload.php';

            // Include PhpSpreadsheet from third_party
            require APPPATH . 'third_party/psr/simple-cache/src/CacheInterface.php';

            $excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

            // 1. Place all your 'use' statements right after the opening PHP tag



            // Settingan awal fil excel
            $excel->getProperties()->setCreator('My Notes Code')
                ->setLastModifiedBy('Bangun Desa LogistIndo')
                ->setTitle("Daftar Cuti Karyawan")
                ->setSubject("Cuti")
                ->setDescription("Laporan Data Cuti Karyawan")
                ->setKeywords("Daftar Cuti Karyawan");

            // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
            $style_col = array(
                'font' => array('bold' => true), // Set font nya jadi bold
                'alignment' => array(
                    'horizontal' => Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                    'vertical' => Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
                ),
                'borders' => array(
                    'top' => array('borderStyle'  => Border::BORDER_THIN), // Set border top dengan garis tipis
                    'right' => array('borderStyle'  => Border::BORDER_THIN),  // Set border right dengan garis tipis
                    'bottom' => array('borderStyle'  => Border::BORDER_THIN), // Set border bottom dengan garis tipis
                    'left' => array('borderStyle'  => Border::BORDER_THIN) // Set border left dengan garis tipis
                )
            );

            // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
            $style_row = array(
                'alignment' => array(
                    'vertical' => Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
                ),
                'borders' => array(
                    'top' => array('borderStyle'  => Border::BORDER_THIN), // Set border top dengan garis tipis
                    'right' => array('borderStyle'  => Border::BORDER_THIN),  // Set border right dengan garis tipis
                    'bottom' => array('borderStyle'  => Border::BORDER_THIN), // Set border bottom dengan garis tipis
                    'left' => array('borderStyle'  => Border::BORDER_THIN) // Set border left dengan garis tipis
                )
            );

            $excel->setActiveSheetIndex(0)->setCellValue('A1', "Daftar Cuti Karyawan");
            $excel->getActiveSheet()->mergeCells('A1:T1'); // Set Merge Cell pada kolom A1 sampai E1
            $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE); // Set bold kolom A1
            $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15); // Set font size 15 untuk kolom A1
            $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Set text center untuk kolom A1
            $excel->getActiveSheet()->getStyle('F1')->getAlignment()->setWrapText(true); // Set text wrapper
            $excel->getActiveSheet()->getStyle('H1')->getAlignment()->setWrapText(true); // Set text wrapper

            // Buat header tabel nya pada baris ke 3
            $excel->setActiveSheetIndex(0)->setCellValue('A3', "No.");
            $excel->setActiveSheetIndex(0)->setCellValue('B3', "NIP");
            $excel->setActiveSheetIndex(0)->setCellValue('C3', "Nama Karyawan");
            $excel->setActiveSheetIndex(0)->setCellValue('D3', "Jabatan");
            $excel->setActiveSheetIndex(0)->setCellValue('E3', "Jenis Cuti");
            $excel->setActiveSheetIndex(0)->setCellValue('F3', "Detail Cuti");
            $excel->setActiveSheetIndex(0)->setCellValue('G3', "Alasan Cuti");
            $excel->setActiveSheetIndex(0)->setCellValue('H3', "Alamat Cuti");
            $excel->setActiveSheetIndex(0)->setCellValue('I3', "Tanggal Cuti");
            $excel->setActiveSheetIndex(0)->setCellValue('J3', "Akhir Cuti");
            $excel->setActiveSheetIndex(0)->setCellValue('K3', "Jumlah Cuti");
            $excel->setActiveSheetIndex(0)->setCellValue('L3', "NIP Atasan");
            $excel->setActiveSheetIndex(0)->setCellValue('M3', "Nama Atasan");
            $excel->setActiveSheetIndex(0)->setCellValue('N3', "Status Atasan");
            $excel->setActiveSheetIndex(0)->setCellValue('O3', "NIP Hrd");
            $excel->setActiveSheetIndex(0)->setCellValue('P3', "Nama Hrd");
            $excel->setActiveSheetIndex(0)->setCellValue('Q3', "Status Hrd");
            $excel->setActiveSheetIndex(0)->setCellValue('R3', "NIP Pengganti");
            $excel->setActiveSheetIndex(0)->setCellValue('S3', "Nama Pengganti");
            $excel->setActiveSheetIndex(0)->setCellValue('T3', "Jabatan Pengganti");

            // Apply style header yang telah kita buat tadi ke masing-masing kolom header
            $excel->getActiveSheet()->getStyle('A3')->applyFromArray($style_col);
            $excel->getActiveSheet()->getStyle('B3')->applyFromArray($style_col);
            $excel->getActiveSheet()->getStyle('C3')->applyFromArray($style_col);
            $excel->getActiveSheet()->getStyle('D3')->applyFromArray($style_col);
            $excel->getActiveSheet()->getStyle('E3')->applyFromArray($style_col);
            $excel->getActiveSheet()->getStyle('F3')->applyFromArray($style_col);
            $excel->getActiveSheet()->getStyle('G3')->applyFromArray($style_col);
            $excel->getActiveSheet()->getStyle('H3')->applyFromArray($style_col);
            $excel->getActiveSheet()->getStyle('I3')->applyFromArray($style_col);
            $excel->getActiveSheet()->getStyle('J3')->applyFromArray($style_col);
            $excel->getActiveSheet()->getStyle('K3')->applyFromArray($style_col);
            $excel->getActiveSheet()->getStyle('L3')->applyFromArray($style_col);
            $excel->getActiveSheet()->getStyle('M3')->applyFromArray($style_col);
            $excel->getActiveSheet()->getStyle('N3')->applyFromArray($style_col);
            $excel->getActiveSheet()->getStyle('O3')->applyFromArray($style_col);
            $excel->getActiveSheet()->getStyle('P3')->applyFromArray($style_col);
            $excel->getActiveSheet()->getStyle('Q3')->applyFromArray($style_col);
            $excel->getActiveSheet()->getStyle('R3')->applyFromArray($style_col);
            $excel->getActiveSheet()->getStyle('S3')->applyFromArray($style_col);
            $excel->getActiveSheet()->getStyle('T3')->applyFromArray($style_col);


            $sql = "SELECT cuti.*, users.nama, users.nama_jabatan, jenis_cuti.nama_jenis, sub_jenis_cuti.nama_sub_jenis FROM cuti 
            JOIN users on cuti.nip = users.nip 
            JOIN jenis_cuti on cuti.jenis = jenis_cuti.Id 
            LEFT JOIN sub_jenis_cuti on cuti.detail_cuti = sub_jenis_cuti.Id WHERE cuti.tgl_cuti LIKE '%$filter%' OR cuti.akhir_cuti LIKE '%$filter%'";

            $cuti = $this->db->query($sql)->result_array();

            $no = 1; // Untuk penomoran tabel, di awal set dengan 1
            $i = 0;
            $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4
            foreach ($cuti as $data) { // Lakukan looping pada variabel siswa

                // Pengganti 
                $query = "SELECT users.nama, users.nama_jabatan FROM users RIGHT JOIN cuti ON users.nip = '$data[pengganti]'";
                $pengganti = $this->db->query($query)->result_array();

                // Atasan
                $query = "SELECT users.nama FROM users RIGHT JOIN cuti ON users.nip = '$data[atasan]'";
                $atasan = $this->db->query($query)->result_array();

                // HRD
                $query = "SELECT users.nama FROM users RIGHT JOIN cuti ON users.nip = '$data[hrd]'";
                $hrd = $this->db->query($query)->result_array();

                $excel->setActiveSheetIndex(0)->setCellValue('A' . $numrow, $no);
                $excel->setActiveSheetIndex(0)->setCellValue('B' . $numrow, $data['nip']);
                $excel->setActiveSheetIndex(0)->setCellValue('C' . $numrow, $data['nama']);
                $excel->setActiveSheetIndex(0)->setCellValue('D' . $numrow, $data['nama_jabatan']);
                $excel->setActiveSheetIndex(0)->setCellValue('E' . $numrow, $data['nama_jenis']);
                $excel->setActiveSheetIndex(0)->setCellValue('F' . $numrow, $data['detail_cuti'] != 0 ? $data['nama_sub_jenis'] : '-');
                $excel->setActiveSheetIndex(0)->setCellValue('G' . $numrow, $data['alasan']);
                $excel->setActiveSheetIndex(0)->setCellValue('H' . $numrow, $data['alamat_cuti']);
                $excel->setActiveSheetIndex(0)->setCellValue('I' . $numrow, $data['tgl_cuti']);
                $excel->setActiveSheetIndex(0)->setCellValue('J' . $numrow, $data['akhir_cuti']);
                $excel->setActiveSheetIndex(0)->setCellValue('K' . $numrow, $data['jumlah_cuti'] . " Hari");
                $excel->setActiveSheetIndex(0)->setCellValue('L' . $numrow, $data['atasan']);
                $excel->setActiveSheetIndex(0)->setCellValue('M' . $numrow, $atasan[$i]['nama']);
                $excel->setActiveSheetIndex(0)->setCellValue('N' . $numrow, $data['status_atasan']);
                $excel->setActiveSheetIndex(0)->setCellValue('O' . $numrow, $data['hrd'] ? $data['hrd'] : '-');
                $excel->setActiveSheetIndex(0)->setCellValue('P' . $numrow, $data['hrd'] ? $hrd[$i]['nama'] : '-');
                $excel->setActiveSheetIndex(0)->setCellValue('Q' . $numrow, $data['status_hrd']);
                $excel->setActiveSheetIndex(0)->setCellValue('R' . $numrow, $data['pengganti'] ? $data['pengganti'] : '-');
                $excel->setActiveSheetIndex(0)->setCellValue('S' . $numrow, $data['pengganti'] ? $pengganti[$i]['nama'] : '-');
                $excel->setActiveSheetIndex(0)->setCellValue('T' . $numrow, $data['pengganti'] ? $pengganti[$i]['nama_jabatan'] : '-');

                // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
                $excel->getActiveSheet()->getStyle('A' . $numrow)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('B' . $numrow)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('C' . $numrow)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('D' . $numrow)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('E' . $numrow)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('F' . $numrow)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('G' . $numrow)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('H' . $numrow)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('I' . $numrow)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('J' . $numrow)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('K' . $numrow)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('L' . $numrow)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('M' . $numrow)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('N' . $numrow)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('O' . $numrow)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('P' . $numrow)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('Q' . $numrow)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('R' . $numrow)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('S' . $numrow)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('T' . $numrow)->applyFromArray($style_row);

                $no++; // Tambah 1 setiap kali looping
                $i++;
                $numrow++; // Tambah 1 setiap kali looping
            }
            // Set width kolom
            $excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true); // Set width kolom A
            $excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true); // Set width kolom B
            $excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true); // Set width kolom C
            $excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true); // Set width kolom D
            $excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true); // Set width kolom E
            $excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true); // Set width kolom E
            $excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true); // Set width kolom E
            // $excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true); // Set width kolom E
            $excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true); // Set width kolom E
            $excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true); // Set width kolom E
            $excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true); // Set width kolom E
            $excel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true); // Set width kolom E
            $excel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true); // Set width kolom E
            $excel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true); // Set width kolom E
            $excel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true); // Set width kolom E
            $excel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true); // Set width kolom E
            $excel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true); // Set width kolom E
            $excel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true); // Set width kolom E
            $excel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true); // Set width kolom E
            $excel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true); // Set width kolom E

            // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
            $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
            // Set orientasi kertas jadi LANDSCAPE
            $excel->getActiveSheet()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
            // Set judul file excel nya
            $excel->getActiveSheet(0)->setTitle("Data Cuti Karyawan");
            $excel->setActiveSheetIndex(0);
            // Proses file excel
            // ob_end_clean();

            // $write = IOFactory::createWriter($excel, 'Xlsx');
            // header("Content-type: application/vnd.ms-excel");
            // header('Content-Disposition: attachment; filename="Data-cuti-karyawan.xlsx"');
            // header("Pragma: no-cache");
            // header("Expires: 0");
            // ob_end_clean();
            // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // header('Content-Disposition: attachment; filename="Daftar Belanja.xlsx"'); // Set nama file excel nya
            // header('Cache-Control: max-age=0');
            // $write->save('php://output');
            // Set the filename and save the file
            require APPPATH . 'third_party/autoload_zip.php';
            // Define filename for clarity (and consistency with Content-Disposition)
            $outputFileName = 'Absensi_' . $month . '_' . $year . '.xlsx';

            // Initialize the Xlsx writer
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excel);

            // ======================================================================================
            // CRITICAL: Clear any output buffer BEFORE sending headers and the file content.
            // This prevents any stray whitespace or HTML from corrupting the Excel file.
            // ======================================================================================
            ob_end_clean(); // Clears and turns off output buffering

            // Set headers to force download (these must be sent before any file data)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $outputFileName . '"'); // Use the consistent filename
            header('Cache-Control: max-age=0');
            header('Pragma: public'); // Often recommended for better cross-browser compatibility
            header('Expires: 0');

            // Save the file directly to the browser's output stream
            $writer->save('php://output');

            // Terminate the script immediately after the file is sent
            exit();
        }
    }

    public function get_data_karyawan()
    {
        $nip = $this->input->post('nip');
        $this->db->select('bagian, level_jabatan, cuti, nip, supervisi');
        $karyawan = $this->db->get_where('users', ['nip' => $nip])->row();

        $data_atasan = $this->db->get_where('users', ['nip' => $karyawan->supervisi])->row();

        $this->db->select('nama, nip');
        $this->db->where([
            'supervisi' => $karyawan->supervisi,
            'nip !=' => $karyawan->nip
        ]);
        $this->db->or_where('bagian', $karyawan->bagian);
        $pengganti = $this->db->get('users');

        $data_pengganti = "<option value=''> -- Pilih Pengganti --</option>";
        foreach ($pengganti->result() as $row) {
            $data_pengganti .= "<option value='$row->nip'>$row->nama" . " [" . $row->nip . "]</option>";
        }

        $data = [
            'atasan' => $data_atasan,
            'sisa_cuti' => $karyawan->cuti,
            'pengganti' => $data_pengganti
        ];

        echo json_encode($data);
    }

    public function cuti_manual()
    {
        $nip_karyawan = $this->input->post('nama_karyawan');
        $jumlah_cuti = $this->input->post('jumlah_cuti');
        $tgl_cuti = $this->input->post('mulai_cuti');
        $tgl_cuti = str_replace('/', '-', $tgl_cuti);
        $akhir_cuti = $this->input->post('akhir_cuti');
        $akhir_cuti = str_replace('/', '-', $akhir_cuti);
        $alamat_cuti = $this->input->post('alamat_cuti');
        $alasan_cuti = $this->input->post('alasan_cuti');
        $jenis_cuti = $this->input->post('jenis_cuti');
        $detail_cuti = $this->input->post('detail_cuti');
        $file_pendukung = $this->input->post('file_pendukung');
        $pengganti = $this->input->post('pengganti_cuti');
        $atasan = $this->input->post('nip_atasan');
        $hrd = $this->session->userdata('nip');

        $sql = "SELECT jenis_cuti.Id FROM jenis_cuti WHERE '$jenis_cuti' IN (SELECT parents FROM sub_jenis_cuti)";
        $ada_detail = $this->db->query($sql)->result();
        // Form validation 
        $this->form_validation->set_rules('nama_karyawan', 'Nama karyawan', 'required', array(
            'required' => '%s wajib diisi!'
        ));

        $this->form_validation->set_rules('pengganti_cuti', 'Pengganti', 'required', array(
            'required' => '%s wajib diisi!'
        ));

        $this->form_validation->set_rules('jenis_cuti', 'Jenis cuti', 'required', array(
            'required' => '%s wajib diisi!'
        ));

        // Cek jika terdapat detail cuti
        $ada_detail
            ? $this->form_validation->set_rules('detail_cuti', 'Detail cuti', 'required', array(
                'required' => '%s wajib diisi!'
            ))
            : "";

        $this->form_validation->set_rules('alamat_cuti', 'Alamat', 'required|min_length[5]', array(
            'required' => '%s wajib diisi!',
            'min_length' => '% minimal 5 karakter'
        ));

        $this->form_validation->set_rules('alasan_cuti', 'Alasan', 'required|min_length[5]', array(
            'required' => '%s wajib diisi!',
            'min_length' => '% minimal 5 karakter'
        ));

        $this->form_validation->set_rules('mulai_cuti', 'Tanggal mulai cuti', 'required', array(
            'required' => '%s wajib diisi!'
        ));

        $this->form_validation->set_rules('akhir_cuti', 'Tanggal selesai cuti', 'required', array(
            'required' => '%s wajib diisi!'
        ));

        $this->form_validation->set_rules('jumlah_cuti', 'Jumlah cuti', 'required', array(
            'required' => '%s wajib diisi!'
        ));

        // Jika cuti memerlukan dokumen pendukung
        $sql = "SELECT jenis_cuti.Id FROM jenis_cuti WHERE '$jenis_cuti' IN (SELECT jenis_cuti.Id FROM jenis_cuti WHERE file_pendukung = 1)";
        $perlu_dokumen = $this->db->query($sql)->result();

        if ($this->form_validation->run()) { // Jika form validation berhasil dijalankan
            if ($jumlah_cuti < 1) {
                $data = [
                    'error' => true,
                    'msg' => 'Cuti tidak boleh kurang dari satu hari'
                ];
            } else {
                if ($perlu_dokumen) { // Jika cuti memerlukan dokumen pendukung
                    $config = [
                        'upload_path' => 'upload/cuti',
                        'allowed_types' => 'jpg|png|jpeg|pdf',
                        'file_name' => 'cuti-' . $nip_karyawan . "-" . time()
                    ];

                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);

                    if ($this->upload->do_upload('file_pendukung')) { // Jika melakukan upload file
                        $file = $this->upload->data();
                        $file_pendukung = $file['file_name'];

                        $params = [
                            'nip' => $nip_karyawan,
                            'jumlah_cuti' => $jumlah_cuti,
                            'tgl_cuti' => date('Y-m-d', strtotime($tgl_cuti)),
                            'akhir_cuti' => date('Y-m-d', strtotime($akhir_cuti)),
                            'alamat_cuti' => $alamat_cuti,
                            'alasan' => $alasan_cuti,
                            'jenis' => $jenis_cuti,
                            'detail_cuti' => $detail_cuti,
                            'file_pendukung' => $file_pendukung,
                            'pengganti' => $pengganti,
                            'atasan' => $atasan,
                            'status_atasan' => "Disetujui",
                            'hrd' => $hrd,
                            'hrd_bagian' => 4,
                            'hrd_jabatan' => 2,
                            'status_hrd' => 'Disetujui'
                        ];
                        // Cek cuti karyawan 
                        $this->db->select('nip, cuti_haji, cuti_spiritual');
                        $data_karyawan = $this->db->get_where('users', ['nip' => $nip_karyawan])->row();
                        if ($jenis_cuti == 4) { // Jika mengambil cuti perjalan spiritual
                            if ($data_karyawan->cuti_spiritual == 1) { // Jika sudah pernah mengambil cuti perjalanan spiritual
                                $data = [
                                    'error' => true,
                                    'msg' => 'Cuti perjalanan spiritual sudah diambil'
                                ];
                            } else {
                                $data = [
                                    'error' => false,
                                    'msg' => 'Cuti berhasil ditambahkan!'
                                ];
                                $this->M_cuti->insert_cuti_manual('cuti', $params);

                                // update cuti perjalanan spiritual 
                                $this->db->set(['cuti_spiritual' => 1]);
                                $this->db->where(['nip' => $nip_karyawan]);
                                $this->db->update('users');
                            }
                        } elseif ($jenis_cuti == 5) { // Jika mengambil cuti ibadah haji
                            if ($data_karyawan->cuti_haji == 1) { // Jika sudha pernah mengambil cuti haji
                                $data = [
                                    'error' => true,
                                    'msg' => 'Cuti ibadah haji sudah diambil'
                                ];
                            } else {
                                $data = [
                                    'error' => false,
                                    'msg' => 'Cuti berhasil ditambahkan!'
                                ];
                                $this->M_cuti->insert_cuti_manual('cuti', $params);

                                // Update cuti haji 
                                $this->db->set(['cuti_haji' => 1]);
                                $this->db->where(['nip' => $nip_karyawan]);
                                $this->db->update('users');
                            }
                        } else {
                            $data = [
                                'error' => false,
                                'msg' => "Cuti berhasil ditambahkan!"
                            ];
                            $this->M_cuti->insert_cuti_manual('cuti', $params);
                        }
                    } else { // Jika tidak melakukan upload file
                        $data = [
                            'msg' => $this->upload->display_errors(),
                            'error' => true
                        ];
                    }
                } else { // Jika cuti tidak memerlukan dokumen pendukung
                    $params = [
                        'nip' => $nip_karyawan,
                        'jumlah_cuti' => $jumlah_cuti,
                        'tgl_cuti' => date('Y-m-d', strtotime($tgl_cuti)),
                        'akhir_cuti' => date('Y-m-d', strtotime($akhir_cuti)),
                        'alamat_cuti' => $alamat_cuti,
                        'alasan' => $alasan_cuti,
                        'jenis' => $jenis_cuti,
                        'detail_cuti' => $detail_cuti,
                        'pengganti' => $pengganti,
                        'atasan' => $atasan,
                        'status_atasan' => "Disetujui",
                        'hrd' => $hrd,
                        'hrd_bagian' => 4,
                        'hrd_jabatan' => 2,
                        'status_hrd' => 'Disetujui'
                    ];

                    // Cek jumlah cuti dengan sisa cuti karyawan
                    $this->db->select('nip, cuti, cuti_spiritual, cuti_haji');
                    $data_kar = $this->db->get_where('users', ['nip' => $nip_karyawan])->row();
                    if ($jenis_cuti == 1) { //Jika memilih cuti tahunan
                        if ($jumlah_cuti > $data_kar->cuti) {
                            $data = [
                                'error' => true,
                                'msg' => 'Jumlah cuti melebihi sisa cuti karyawan!'
                            ];
                        } else {
                            $data = [
                                'error' => false,
                                'msg' => 'Cuti berhasil ditambahkan!'
                            ];
                            // insert cuti karyawan
                            $this->M_cuti->insert_cuti_manual('cuti', $params);

                            // update sisa cuti reguler karyawan
                            $cuti_kar = $data_kar->cuti - $jumlah_cuti;
                            $this->db->set(['cuti' => $cuti_kar]);
                            $this->db->where(['nip' => $nip_karyawan]);
                            $this->db->update('users');
                        }
                    } elseif ($jenis_cuti == 2) { // Jika memilih cuti panjang
                        $data = [
                            'error' => false,
                            'msg' => 'Cuti berhasil ditambahkan!'
                        ];
                        // insert cuti karyawan
                        $this->M_cuti->insert_cuti_manual('cuti', $params);
                        // update cuti panjang karyawan
                        $this->db->set(['cuti_panjang' => 1]);
                        $this->db->where(['nip' => $data_kar->nip]);
                        $this->db->update('users');
                    } else {
                        $data = [
                            'error' => false,
                            'msg' => 'Cuti berhasil ditambahkan!',
                        ];

                        $this->M_cuti->insert_cuti_manual('cuti', $params);
                    }
                }
            }
        } else { // Jika form validation gagal dijalankan
            $data = [
                'error' => true,
                'msg' => 'Mohon periksa kembali data cuti anda',
                'err_namakar' => form_error('nama_karyawan'),
                'err_namapeng' => form_error('pengganti_cuti'),
                'err_jenis' => form_error('jenis_cuti'),
                'err_detail' => form_error('detail_cuti'),
                'err_alasan' => form_error('alasan_cuti'),
                'err_mulai' => form_error('mulai_cuti'),
                'err_akhir' => form_error('akhir_cuti'),
                'err_jumlah' => form_error('jumlah_cuti'),
                'err_alamat' => form_error('alamat_cuti'),
            ];
        }
        echo json_encode($data);
    }

    public function reset_cuti()
    {
        $sql = "UPDATE users SET cuti = 12 WHERE TIMESTAMPDIFF(YEAR, tmt, CURDATE()) >= 1";
        $this->db->query($sql);
        $this->session->set_flashdata('msg', 'Cuti berhasil direset!');
        redirect('app/user');
    }
}
