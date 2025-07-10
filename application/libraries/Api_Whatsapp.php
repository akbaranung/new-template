<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');
// require_once('PHPExcel.php');

class Api_Whatsapp {
    function wa_notif($msgg,$phonee)
    {
    // $sender = 'buskipm';
    $phone = $phonee;
    $msg = $msgg;
    
    $token = "whvSyvdbM5CDv1wKvXhQmajGAbTgJfyxfV5xuX1g7hpuMktyj4";
    // $phone= "62812xxxxxx"; //untuk group pakai groupid contoh: 62812xxxxxx-xxxxx
    // $message = "Testing by API ruangwa";
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://app.ruangwa.id/api/send_message',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => 'token='.$token.'&number='.$phone.'&message='.$msgg,
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
    }
}