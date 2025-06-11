<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Absensi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Api_Whatsapp');
        $this->load->model('m_app');
        $this->load->model('absen_m', 'user');
        if ($this->session->userdata('isLogin') == FALSE) {
            $this->session->set_flashdata(
                'msg',
                '<div class="alert rounded-s bg-red-dark" role="alert">
                    Your session has been expired! Please login!
                    <button type="button" class="close color-white opacity-60 font-16" data-bs-dismiss="alert" aria-label="Close">&times;</button>
                </div>'

            );
            redirect('auth');
        }
    }
    public function list()
    {
        $has_access = $this->M_menu->has_access();

        if (!$has_access) {
            show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
        }

        $data['user'] = $this->user->user_get_detail($this->session->userdata('nip'));

        $this->db->select('*'); // Fetch only these columns
        $this->db->from('tblattendance'); // Table name
        $this->db->where('attendanceStatus', 'Pending');
        $data['notif'] = $this->db->get()->num_rows();

        // $this->load->view('pages/absensi/absensi_list', $data);

        $data['title'] = 'Absensi List';
        $data['utility'] = $this->db->get('utility')->row_array();
        $data['pages_script'] = 'script/absensi/s_absensi_list';
        $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

        $data['pages'] = 'pages/absensi/v_absensi_list';

        $this->load->view('index', $data);
    }

    public function ajax_list()
    {
        $this->load->model('absen_m', 'user');

        $list = $this->user->get_datatables();
        $data = array();
        $crs = "";
        $no = $_POST['start'];
        $months = [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'Mei',
            'Jun',
            'Jul',
            'Agu',
            'Sep',
            'Okt',
            'Nov',
            'Des'
        ];

        foreach ($list as $cat) {
            $date = new DateTime($cat->date);

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $cat->nip;
            $row[] = $cat->nama;

            $monthIndex = (int) $date->format('n') - 1; // Get the month index (0-based)
            $row[] = $date->format('d') . ' ' . $months[$monthIndex] . ' ' . $date->format('Y');
            $row[] = $cat->waktu;
            $row[] = $cat->attendanceStatus;
            $row[] = $cat->lokasiAttendance;
            $row[] = $cat->tipe;
            $path = base_url() . "/upload/attendance/" . $cat->image;
            // $path = "https://mobileadmin.kodesis.id/upload/attendance/" . $cat->image;

            $row[] = "<img width='100px' src='" . $path . "'>";

            // $row[] = $date->format('d') . ' ' . $months[$monthIndex] . ' ' . $date->format('Y');


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->user->count_all(),
            "recordsFiltered" => $this->user->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }
    public function ajax_list2()
    {
        $this->load->model('absen_m', 'user');

        $list = $this->user->get_datatables2();
        $data = array();
        $crs = "";
        $no = $_POST['start'];
        $months = [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'Mei',
            'Jun',
            'Jul',
            'Agu',
            'Sep',
            'Okt',
            'Nov',
            'Des'
        ];
        foreach ($list as $cat) {
            $date = new DateTime($cat->date);


            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $cat->nip;
            $row[] = $cat->nama;

            $monthIndex = (int) $date->format('n') - 1; // Get the month index (0-based)
            $row[] = $date->format('d') . ' ' . $months[$monthIndex] . ' ' . $date->format('Y');
            $row[] = $cat->waktu;

            $row[] = $cat->attendanceStatus;

            $row[] = $cat->lokasiAttendance;
            $row[] = $cat->tipe;
            $path = base_url() . "/upload/attendance/" . $cat->image;
            // $path = "https://mobileadmin.kodesis.id/upload/attendance/" . $cat->image;
            $row[] = "<img width='100px' src='" . $path . "'>";
            // $row[] = $date->format('d') . ' ' . $months[$monthIndex] . ' ' . $date->format('Y');



            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->user->count_all2(),
            "recordsFiltered" => $this->user->count_filtered2(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function ajax_list3()
    {
        $this->load->model('absen_m', 'user');

        $list = $this->user->get_datatables3();
        $data = array();
        $crs = "";
        $no = $_POST['start'];

        $months = [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'Mei',
            'Jun',
            'Jul',
            'Agu',
            'Sep',
            'Okt',
            'Nov',
            'Des'
        ];

        foreach ($list as $cat) {
            $date = new DateTime($cat->date);


            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $cat->nip;
            $row[] = $cat->nama;

            $monthIndex = (int) $date->format('n') - 1; // Get the month index (0-based)
            $row[] = $date->format('d') . ' ' . $months[$monthIndex] . ' ' . $date->format('Y');
            $row[] = $cat->waktu;

            $row[] = $cat->attendanceStatus;
            $row[] = $cat->lokasiAttendance;
            $row[] = $cat->tipe;
            $path = base_url() . "/upload/attendance/" . $cat->image;
            // $path = "https://mobileadmin.kodesis.id/upload/attendance/" . $cat->image;
            $row[] = "<img width='100px' src='" . $path . "'>";
            // $row[] = $date->format('d') . ' ' . $months[$monthIndex] . ' ' . $date->format('Y');
            if ($cat->attendanceStatus == 'Pending') {
                $row[] = '<center> <div class="list-icons d-inline-flex">
                <button title="Update User" onclick="onApprove(' . $cat->id . ')" class="btn btn-success"><svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
  <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
</svg></button>
                                                <button title="Delete User" onclick="onNotApprove(' . $cat->id . ')" class="btn btn-danger"><svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
</svg></button>
            </div>
    </center>';
            } else {
                $row[] = 'Approved';
            }


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->user->count_all3(),
            "recordsFiltered" => $this->user->count_filtered3(),
            "data" => $data,
        );
        echo json_encode($output);
    }
    public function approval($tipe, $id)
    {
        $this->load->model('absen_m', 'user');

        if ($tipe == "Approved") {
            $status = 'Present';
        } else {
            $status = 'Absent';
        }
        $date = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $this->user->update(
            array(
                'attendanceStatus'      => $status,
            ),
            array('id' => $id)
        );
        echo json_encode(array("status" => TRUE));
    }
    public function process_export()
    {
        $tanggal = $this->input->post('tanggal');
        list($month, $year) = explode('/', $tanggal);
        $data_absensi = $this->input->post('data_absensi');
        require APPPATH . 'third_party/autoload.php';

        // Include PhpSpreadsheet from third_party
        require APPPATH . 'third_party/psr/simple-cache/src/CacheInterface.php';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header columns
        $sheet->setCellValue('A1', 'Nomor');
        $sheet->setCellValue('B1', 'Username');
        $sheet->setCellValue('C1', 'Nip');
        $sheet->setCellValue('D1', 'FullName');
        $sheet->setCellValue('E1', 'Status');
        $sheet->setCellValue('F1', 'Lokasi');
        $sheet->setCellValue('G1', 'Tipe');
        $sheet->setCellValue('H1', 'Tanggal');
        $sheet->setCellValue('I1', 'Waktu');
        $sheet->setCellValue('J1', 'Image');

        // Get data from the database
        $this->load->database();
        if ($data_absensi == 'Team') {
            $this->db->select('tblattendance.*,users.bagian');
        } else {
            $this->db->select('tblattendance.*');
        }
        $this->db->from('tblattendance'); // Replace with your table name
        $this->db->where('YEAR(date)', $year);
        $this->db->where('MONTH(date)', $month);
        if ($data_absensi == 'User') {
            $this->db->where('username', $this->session->userdata('username'));
        } else if ($data_absensi == 'Team') {
            $this->db->where('bagian', $this->session->userdata('bagian'));
            $this->db->join('users', 'users.username = tblattendance.username');
        }
        $query = $this->db->get();
        $rows = $query->result_array();

        // Populate rows with data
        $nomor = 1;
        $rowNumber = 2; // Start at row 2 because row 1 is the header
        foreach ($rows as $row) {
            $sheet->setCellValue('A' . $rowNumber, $nomor);
            $sheet->setCellValue('B' . $rowNumber, $row['username']);
            $sheet->setCellValue('C' . $rowNumber, $row['nip']);
            $sheet->setCellValue('D' . $rowNumber, $row['nama']);
            $sheet->setCellValue('E' . $rowNumber, $row['attendanceStatus']);
            $sheet->setCellValue('F' . $rowNumber, $row['lokasiAttendance']);
            $sheet->setCellValue('G' . $rowNumber, $row['tipe']);
            $sheet->setCellValue('H' . $rowNumber, $row['date']);
            $sheet->setCellValue('I' . $rowNumber, $row['waktu']);
            if (!empty($row['image'])) {
                $imagePath = FCPATH . 'upload' . DIRECTORY_SEPARATOR . 'attendance' . DIRECTORY_SEPARATOR . $row['image'];

                // Check if the image exists
                if (file_exists($imagePath)) {
                    // If the image exists, insert it into the spreadsheet
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('Attendance Image');
                    $drawing->setDescription('Attendance Image');
                    $drawing->setPath($imagePath);  // Set the path to the image
                    $drawing->setHeight(100); // Optional: Set the image height (you can adjust this)
                    $drawing->setCoordinates('J' . $rowNumber); // Set the position of the image in the sheet
                    $drawing->setWorksheet($sheet); // Attach the image to the worksheet
                } else {
                    // If the image is not found, set a message or placeholder
                    $sheet->setCellValue('J' . $rowNumber, 'Image not found');  // Display a placeholder text in the cell
                }
            } else {
                $sheet->setCellValue('J' . $rowNumber, 'Image Null');  // Display a placeholder text in the cell
            }
            $sheet->getRowDimension($rowNumber)->setRowHeight(80);
            $rowNumber++;
            $nomor++;
        }

        $sheet->getColumnDimension('A')->setWidth(3); // Set width kolom A
        $sheet->getColumnDimension('B')->setWidth(15); // Set width kolom B
        $sheet->getColumnDimension('C')->setWidth(15); // Set width kolom C
        $sheet->getColumnDimension('D')->setWidth(15); // Set width kolom D
        $sheet->getColumnDimension('E')->setWidth(15); // Set width kolom E
        $sheet->getColumnDimension('F')->setWidth(15); // Set width kolom D
        $sheet->getColumnDimension('G')->setWidth(18); // Set width kolom E
        $sheet->getColumnDimension('H')->setWidth(18); // Set width kolom E
        $sheet->getColumnDimension('I')->setWidth(18); // Set width kolom E
        $sheet->getColumnDimension('J')->setWidth(25); // Set width kolom E

        // Set the filename and save the file
        $fileName = 'Export_' . date('Y-m-d_H-i-s') . '.xlsx';
        require APPPATH . 'third_party/autoload_zip.php';

        // Now PhpSpreadsheet's Xlsx writer can use ZipStream
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filePath = FCPATH . 'downloads/' . $fileName; // Save to a downloads folder

        // Set headers to force download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Absensi_' . $month . '_' . $year . '.xlsx"');
        header('Cache-Control: max-age=0');


        // Save the file to the browser for download
        $writer->save('php://output');

        // After the file is downloaded, perform the redirection to a list page or display a message
        exit(); // Terminate script after download is complete
    }


    public function absen_wfa()
    {
        $has_access = $this->M_menu->has_access();

        if (!$has_access) {
            show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
        }

        $this->load->model(
            'Absen_m',
            'user'
        );

        $data['data_users'] = $this->user->data_user();

        // Access properties using '->' because $cek_user is an object
        $data_user = $this->user->data_user();
        $jam_masuk_plus_two = (new DateTime($data_user->jam_masuk))->modify('+2 hours')->format('H:i:s');
        $jam_keluar_plus_two = (new DateTime($data_user->jam_keluar))->modify('+2 hours')->format('H:i:s');

        $this->db->select('*');
        $this->db->from('tblattendance');
        $this->db->where('username', $this->session->userdata('username')); // Filter by username
        $this->db->where('DATE(date)', date('Y-m-d')); // Today's date
        $this->db->where('TIME(waktu) <=', $jam_masuk_plus_two); // Check for records under jam_masuk_plus_two
        $query = $this->db->get(); // Execute the query
        $result1 = $query->result_array(); // Fetch results

        $this->db->select('*');
        $this->db->from('tblattendance');
        $this->db->where('username', $this->session->userdata('username')); // Filter by username
        $this->db->where('DATE(date)', date('Y-m-d')); // Today's date
        $this->db->where('TIME(waktu) >=', $jam_keluar_plus_two); // Check for records under jam_keluar_plus_two
        $query = $this->db->get(); // Execute the query
        $result2 = $query->result_array(); // Fetch results

        $this->db->select('*');
        $this->db->from('tblattendance');
        $this->db->where('username', $this->session->userdata('username')); // Filter by username
        $this->db->where('DATE(date)', date('Y-m-d')); // Today's date
        $this->db->where('TIME(waktu) >=', $jam_masuk_plus_two); // Check for records after jam_masuk_plus_two
        $this->db->where('TIME(waktu) <=', $jam_keluar_plus_two); // Check for records before jam_keluar_plus_two
        $query = $this->db->get(); // Execute the query
        $result3 = $query->result_array(); // Fetch results

        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('username', $this->session->userdata('username')); // Filter by username
        $query = $this->db->get(); // Execute the query
        $lokasi_presensi_user = $query->row(); // Fetch results

        $data['result1'] = $result1;
        $data['result2'] = $result2;
        $data['result3'] = $result3;
        $data['lokasi_presensi_user'] = $lokasi_presensi_user;

        $data['cek_user'] = $this->user->cek_user();
        $data['lokasi_absensi'] = $this->user->get_location();

        $data['data_user'] = $this->user->get_user();

        $data['title'] = 'Absensi List';
        $data['utility'] = $this->db->get('utility')->row_array();
        $data['pages_script'] = 'script/absensi/s_absen_wfh_view';
        $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

        $data['pages'] = 'pages/absensi/v_absen_wfh_view';

        $this->load->view('index', $data);

        // $this->load->view('pages/absensi/absen_wfh_view', $data);
    }
    public function fetch_user($tipe = null)
    {
        $this->load->model('Absen_m', 'user');
        $users = $this->user->get_user(); // Fetch all users from the database
        $data['tipe'] = $tipe;

        // Access properties using '->' because $cek_user is an object
        $data_user = $this->user->data_user();
        $jam_masuk_plus_two = (new DateTime($data_user->jam_masuk))->modify('+2 hours')->format('H:i:s');
        $jam_keluar_plus_two = (new DateTime($data_user->jam_keluar))->modify('+0 hours')->format('H:i:s');

        if ($users) {
            // If using result_array(), users will be an array, even if there's only one user
            $hasPicture = false;

            // Iterate over users (even if it's just one user) to check if 'userImage' is not null
            foreach ($users as $user) {
                if (!empty($user['userImage'])) {
                    $hasPicture = true; // If 'userImage' is not empty, set flag to true
                    break; // No need to continue looping if we find a picture
                }
            }

            if (!$hasPicture) {
                echo json_encode([
                    'status' => 'No Picture'
                ]);
            } else {
                if ($tipe == 'masuk') {
                    $this->db->select('*'); // Fetch only these columns
                    $this->db->from('tblattendance'); // Table name
                    $this->db->where('username', $this->session->userdata('username'));
                    $this->db->where('DATE(date)', date('Y-m-d')); // Today's date
                    $this->db->where('tipe', 'Masuk'); // Check for records under jam_keluar_plus_two
                    $users = $this->db->get()->result_array();

                    $data['users'] = $users;
                } else if ($tipe == 'pulang') {
                    $this->db->select('*'); // Fetch only these columns
                    $this->db->from('tblattendance'); // Table name
                    $this->db->where('username', $this->session->userdata('username'));
                    $this->db->where('DATE(date)', date('Y-m-d')); // Today's date
                    $this->db->where('tipe', 'Pulang'); // Check for records under jam_keluar_plus_two
                    $users = $this->db->get()->result_array();
                    // return $query->result_array(); // Return the result as an array

                    $data['users'] = $users;
                } else if ($tipe == 'absensi') {
                    $this->db->select('*');
                    $this->db->from('tblattendance');
                    $this->db->where('username', $this->session->userdata('username')); // Filter by username
                    $this->db->where('DATE(date)', date('Y-m-d')); // Today's date
                    $this->db->where_in('tipe', ['Masuk', 'Telat']);
                    $users = $this->db->get()->result_array();
                    $data['users'] = $users;
                } else {
                    $data['users'] = $users;
                }
                $tableHTML = $this->load->view('pages/absensi/userTable', $data, TRUE);
                echo json_encode([
                    'status' => 'success',
                    'tipe' => $tipe,
                    'data' => $users,
                    'html' => $tableHTML
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'No records found'
            ]);
        }
    }
    public function user_photo()
    {
        $has_access = $this->M_menu->has_access();

        if (!$has_access) {
            show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
        }
        $data['title'] = 'User Photo';
        $data['utility'] = $this->db->get('utility')->row_array();
        $data['pages_script'] = 'script/absensi/s_user_view_photo';
        $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

        $data['pages'] = 'pages/absensi/v_user_view_photo';

        $data['user'] = $this->user->user_get_detail_with_nip($this->session->userdata('nip'));

        $this->load->view('index', $data);

        // $this->load->view('pages/absensi/user_view_photo', $data);
    }
    public function add_photo()
    {
        $this->load->model('Absen_m', 'user');
        $id_edit = $this->input->post('id');
        $username = $this->input->post('username');

        $imageFileNames = [];
        $folderPath = FCPATH . "resources/labels/{$username}/";

        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Process images
        for ($i = 1; $i <= 5; $i++) {
            $capturedImage = $this->input->post("capturedImage{$i}");
            if ($capturedImage) {
                $base64Data = explode(',', $capturedImage)[1];
                $imageData = base64_decode($base64Data);
                $labelName = "{$i}.png";
                file_put_contents("{$folderPath}{$labelName}", $imageData);
                $imageFileNames[] = $labelName;
            }
        }

        $imagesJson = json_encode($imageFileNames);

        // Check for duplicate registration number

        echo $imagesJson;
        // Save the student
        $edit_data = [
            'userImage' => $imagesJson,
        ];
        $this->db->where(
            'id',
            $id_edit
        );
        $edit = $this->db->update('users', $edit_data);
        echo $id_edit;
        $this->session->set_flashdata('message', "Student: $username added successfully!");
        echo "Student: $username added successfully!";



        redirect('absensi/user_photo');
    }
    // public function recordAttendance()
    // {
    //     $this->load->model('Absen_m', 'user');

    //     // Only allow POST requests
    //     if ($this->input->server('REQUEST_METHOD') !== 'POST') {
    //         show_error('Method Not Allowed', 405);
    //         return;
    //     }

    //     $attendanceData = json_decode(file_get_contents("php://input"), true);

    //     if (!$attendanceData) {
    //         echo json_encode([
    //             'status' => 'error',
    //             'message' => 'No attendance data received.'
    //         ]);
    //         return;
    //     }
    //     $folderPath = FCPATH . "upload/attendance/";

    //     // Ensure the directory exists
    //     if (!is_dir($folderPath)) {
    //         mkdir($folderPath, 0755, true);
    //     }

    //     var_dump($attendanceData);
    //     echo $attendanceData['lokasiAttendance'][1];
    //     // Process and save the image
    //     $base64Data = explode(',', $attendanceData['capturedImage'])[1];
    //     $imageData = base64_decode($base64Data);
    //     $filename = 'Attendance_' . uniqid() . '.png';

    //     if (file_put_contents($folderPath . $filename, $imageData)) {
    //         // Save attendance data to the database
    //         $attendance = [
    //             'username' => $attendanceData['username'],
    //             'nip' => $attendanceData['nip'],
    //             'nama' => $attendanceData['nama'],
    //             'attendanceStatus' => $attendanceData['attendanceStatus'],
    //             'lokasiAttendance' => $attendanceData['lokasiAttendance'],
    //             'tanggalAttendance' => $attendanceData['tanggalAttendance'],
    //             'image' => $filename
    //         ];

    //         // Call the method to insert attendance
    //         $response = $this->user->insertAttendance($attendance);

    //         // Return the response to the client
    //         // echo json_encode($response);


    //         echo json_encode(['status' => 'success', 'message' => 'Attendance recorded successfully.']);
    //     } else {
    //         echo json_encode(['status' => 'error', 'message' => 'Failed to save image.']);
    //     }
    //     // $response = $this->user->insertAttendance($attendanceData);

    //     // echo json_encode($response);
    // }
    public function recordAttendance()
    {
        $this->load->model('Absen_m', 'user');

        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            show_error('Method Not Allowed', 405);
            return;
        }

        $rawData = file_get_contents("php://input");
        $attendanceArray = json_decode($rawData, true);

        if (!$attendanceArray || !is_array($attendanceArray) || empty($attendanceArray)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'No valid attendance data received or invalid JSON format.'
            ]);
            return;
        }

        // *** THIS IS THE CRUCIAL LINE THAT FIXES YOUR PROBLEM ***
        // It extracts the actual attendance object from the outer array.
        $attendanceData = $attendanceArray[0];

        // --- From here onwards, you can access fields directly like $attendanceData['fieldName'] ---

        // Validate essential fields exist
        $requiredFields = ['username', 'nip', 'nama', 'attendanceStatus', 'lokasiAttendance', 'tanggalAttendance', 'capturedImage'];
        foreach ($requiredFields as $field) {
            if (!isset($attendanceData[$field])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => "Missing required field: {$field}."
                ]);
                return;
            }
        }

        $folderPath = FCPATH . "upload/attendance/";

        if (!is_dir($folderPath)) {
            if (!mkdir($folderPath, 0755, true)) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to create upload directory.']);
                return;
            }
        }

        // Now you can safely access $attendanceData['capturedImage']
        $base64Parts = explode(',', $attendanceData['capturedImage']);
        if (count($base64Parts) < 2) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid image data format.']);
            return;
        }
        $base64Data = $base64Parts[1];
        $imageData = base64_decode($base64Data);

        if ($imageData === FALSE) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to decode image data.']);
            return;
        }

        $filename = 'Attendance_' . uniqid() . '.png';

        if (file_put_contents($folderPath . $filename, $imageData)) {
            $attendance = [
                'username' => $attendanceData['username'],
                'nip' => $attendanceData['nip'],
                'nama' => $attendanceData['nama'],
                'attendanceStatus' => $attendanceData['attendanceStatus'],
                'lokasiAttendance' => $attendanceData['lokasiAttendance'], // Now correctly accessed
                'tanggalAttendance' => $attendanceData['tanggalAttendance'],
                'image' => $filename
            ];

            $response = $this->user->insertAttendance($attendance);

            if ($response) {
                echo json_encode(['status' => 'success', 'message' => 'Attendance recorded successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to insert attendance data into database.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save image to server.']);
        }
    }
    public function delete_user_images()
    {
        // Get JSON input
        // $input = json_decode(file_get_contents('php://input'), true);

        $username = $this->input->post('username');
        if (!isset($username) || empty($username)) {
            echo json_encode(['status' => 'error', 'message' => 'Username is required.']);
            return;
        }

        // $username = $input['username'];

        // Fetch user data
        $user = $this->db->get_where('users', ['username' => $username])->row();

        if (!$user || empty($user->userImage)) {
            echo json_encode(['status' => 'error', 'message' => 'No images found for this user.']);
            return;
        }

        $images = json_decode($user->userImage, true); // Decode JSON array
        $path = FCPATH . 'resources/labels/' . $username . '/';

        // Delete all images in the directory
        foreach ($images as $image) {
            $file = $path . $image;
            if (is_file($file)) {
                unlink($file); // Delete each image
            }
        }

        // Clear userImage field by setting it to NULL
        $this->db->where('username', $username);
        $this->db->set('userImage', 'NULL', false);
        $this->db->update('users');

        if ($this->db->affected_rows() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'All images deleted and userImage set to NULL successfully.']);
            return;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update userImage field to NULL.']);
            return;
        }

        echo json_encode(['status' => 'success', 'message' => 'All images deleted and userImage set to NULL successfully.']);
    }

    public function lokasi_presensi()
    {

        $has_access = $this->M_menu->has_access();

        if (!$has_access) {
            show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
        }
        $data['title'] = 'Lokasi Presensi';
        $data['utility'] = $this->db->get('utility')->row_array();
        $data['pages_script'] = 'script/absensi/s_lokasi_presensi';
        $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

        $data['pages'] = 'pages/absensi/v_lokasi_presensi';

        $this->load->view('index', $data);

        // $this->load->view('pages/absensi/lokasi_presensi', $data);

    }

    public function ajax_lokasi_presensi_list()
    {
        $this->load->model('Lokasi_Presensi_m', 'lpm');

        $list = $this->lpm->get_datatables();
        $data = array();
        $crs = "";
        $no = $_POST['start'];

        foreach ($list as $cat) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $cat->nama_lokasi;
            $row[] = $cat->alamat_lokasi;

            $row[] = $cat->tipe_lokasi;
            $row[] = $cat->latitude;
            $row[] = $cat->longitude;
            $radius_meter = $cat->radius * 1000;
            $row[] = $radius_meter . ' Meter';
            // $row[] = $cat->zona_waktu;
            $row[] = $cat->jam_masuk . ' ' . $cat->zona_waktu;
            $row[] = $cat->jam_pulang . ' ' . $cat->zona_waktu;

            $row[] = '<a href="' . base_url('absensi/edit_lokasi_presensi/' . $cat->id) . '" class="btn btn-warning">
								Update
							</a><button onclick="onDelete(' . $cat->id . ')" class="btn btn-danger">
								Delete
							</button>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->lpm->count_all(),
            "recordsFiltered" => $this->lpm->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }
    public function add_lokasi_presensi()
    {


        $data['title'] = 'Add Lokasi Presensi';
        $data['utility'] = $this->db->get('utility')->row_array();
        $data['pages_script'] = 'script/absensi/s_lokasi_presensi_form';
        $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
        $data['pages'] = 'pages/absensi/v_lokasi_presensi_form';

        $this->load->view('index', $data);

        // $this->load->view('pages/absensi/lokasi_presensi_form', $data);
    }
    public function edit_lokasi_presensi($id)
    {
        $this->load->model('Lokasi_Presensi_m', 'lpm');


        $data['detail'] = $this->lpm->get_detail_id($id);
        $data['title'] = 'Add Lokasi Presensi';
        $data['utility'] = $this->db->get('utility')->row_array();
        $data['pages_script'] = 'script/absensi/s_lokasi_presensi_form';
        $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
        $data['pages'] = 'pages/absensi/v_lokasi_presensi_form';

        $this->load->view('index', $data);
        // $this->load->view('pages/absensi/lokasi_presensi_form', $data);
    }

    public function proses_tambah_lokasi_presensi()
    {
        $raw_slug = $this->input->post('nama_lokasi');
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($raw_slug)));

        $radius = $this->input->post('radius_lokasi') / 1000;
        $data_insert = array(
            'nama_lokasi'                 => $this->input->post('nama_lokasi'),
            'slug'            => $slug,
            'alamat_lokasi'            => $this->input->post('alamat_lokasi'),
            'tipe_lokasi'                => $this->input->post('tipe_lokasi'),
            'latitude'            => $this->input->post('latitude_lokasi'),
            'longitude'                => $this->input->post('longitude_lokasi'),
            'radius'                => $radius,
            'zona_waktu'            => $this->input->post('zona_waktu'),
            'jam_masuk'                => $this->input->post('jam_masuk'),
            'jam_pulang'                => $this->input->post('jam_pulang'),
        );
        $this->db->insert('lokasi_presensi', $data_insert);
        redirect('absensi/lokasi_presensi');
    }

    public function proses_update_lokasi_presensi()
    {
        $raw_slug = $this->input->post('nama_lokasi');
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($raw_slug)));

        $radius = $this->input->post('radius_lokasi') / 1000;
        $data_insert = array(
            'nama_lokasi'                 => $this->input->post('nama_lokasi'),
            'slug'            => $slug,
            'alamat_lokasi'            => $this->input->post('alamat_lokasi'),
            'tipe_lokasi'                => $this->input->post('tipe_lokasi'),
            'latitude'            => $this->input->post('latitude_lokasi'),
            'longitude'                => $this->input->post('longitude_lokasi'),
            'radius'                => $radius,
            'zona_waktu'            => $this->input->post('zona_waktu'),
            'jam_masuk'                => $this->input->post('jam_masuk'),
            'jam_pulang'                => $this->input->post('jam_pulang'),
        );
        $this->db->where('id', $this->input->post('id_lokasi')); // Ensure to specify the record to update
        $this->db->update('lokasi_presensi', $data_insert);
        redirect('absensi/lokasi_presensi');
    }
    public function hapus_lokasi_presensi()
    {
        $id = $this->input->post('id');

        // 1. Basic validation for ID
        if (empty($id)) { // Using empty() is often better for checking if a variable is considered "empty"
            echo json_encode(['status' => 'error', 'message' => 'ID lokasi presensi tidak ditemukan atau tidak valid.']);
            return;
        }

        // 2. Optional: Check if the record exists before attempting deletion
        // This provides a more specific error message if the ID doesn't exist
        $this->db->where('id', $id);
        $query = $this->db->get('lokasi_presensi');

        if ($query->num_rows() == 0) {
            echo json_encode(['status' => 'info', 'message' => 'Lokasi presensi tidak ditemukan atau sudah dihapus.']);
            return;
        }

        // 3. Attempt the deletion
        $this->db->where('id', $id);
        $delete_result = $this->db->delete('lokasi_presensi');

        // 4. Check the direct result of the delete operation and affected rows
        if ($delete_result) { // $delete_result will be TRUE on successful query execution
            if ($this->db->affected_rows() > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Lokasi presensi berhasil dihapus.']);
            } else {
                // This 'else' block means the query ran without error but affected 0 rows.
                // Given the num_rows() check above, this is now less likely unless
                // something very unusual happened between check and delete.
                // Could also happen if a row was deleted by another process milliseconds before.
                echo json_encode(['status' => 'info', 'message' => 'Lokasi presensi tidak ditemukan atau sudah dihapus. (Affected rows 0)']);
            }
        } else {
            // This 'else' block means the DELETE query itself failed (e.g., database error, syntax error).
            // You might want to log this error.
            error_log("Database delete error for ID: " . $id . " - " . $this->db->error()['message']);
            echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan saat menghapus lokasi presensi. Silakan coba lagi.']);
        }
    }
}
