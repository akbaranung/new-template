<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');
// require_once('PHPExcel.php');

class Api_Whatsapp
{
  function wa_notif($msgg, $phone)
  {
    // $sender = 'buskipm';
    // $phone = $phonee;
    // $msg = $msgg;

    $token = "53C92CE6A40AC365CD9D1FF128EB1B8E";
    // $phone= "62812xxxxxx"; //untuk group pakai groupid contoh: 62812xxxxxx-xxxxx
    // $message = "Testing by API ruangwa";

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://app.fastwa.com/api/v1/C9E3ED48A460F61F60384815FB4C0B83/send_text',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      // CURLOPT_POSTFIELDS => 'token=' . $token . '&number=' . $phone . '&message=' . $msgg,
      CURLOPT_POSTFIELDS => array('api_key' => $token, 'phone' => $phone, 'message' => $msgg),

    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
  }
}
