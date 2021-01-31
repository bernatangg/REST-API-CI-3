<?php

class UserM extends CI_Model {
  
  public function empty_response() {
    $response['status'] = 502;
    $response['error'] = true;
    $response['message'] = 'Field tidak boleh kosong';
    return $response;
  }

  public function login_user($phone, $pin) {
    $where = array(
      "HP" => $phone,
      "Pin" => $pin);

    $user = $this->db
      ->select('UserId, KYCID, type, verification')
      ->where($where)
      ->get('user')
      ->row();
    $response['status'] = 200;
    $response['error'] = false;
    $response['data'] = $user;
    return $response;
  }

  public function add_user(
    $nik, $nama, $alamat, $id_kota, $id_prov, $doc_nik, $foto_front, $gender,
    $tmp_lahir, $tgl_lahir, $phone, $pin, $type) {
  
    if (
      empty($nik) || empty($nama) || empty($alamat) || empty($id_kota) || empty($id_prov) || empty($doc_nik) || empty($foto_front)
      || empty($gender) || empty($tmp_lahir) || empty($tgl_lahir) || empty($phone) || empty($pin) || empty($type)) {
        return $this->empty_response();
      } else {
        $data = array(
          "NIK" => $nik,
          "Nama" => $nama,
          "Alamat" => $alamat,
          "Kota" => $id_kota,
          "Provinsi" => $id_prov,
          "Documentasi_NIK" => $doc_nik,
          "Documentasi_Foto" => $foto_front,
          "Jenis_kelamin" => $gender,
          "Tmp_lahir" => $tmp_lahir,
          "Tgl_lahir" => $tgl_lahir
        );
  
        $this->db->where('HP', $phone);
        $query = $this->db->get('user');
        $count_row = $query->num_rows();
        if ($count_row > 0) {
          $response['status'] = 502;
          $response['error'] = true;
          $response['message'] = 'Data user gagal ditambahkan.';
          return $response;
        } else {
          $this->db->where('NIK', $nik);
          $query = $this->db->get('kyc');
          $count_row = $query->num_rows();
          if ($count_row > 0) {
            $response['status'] = 502;
            $response['error'] = true;
            $response['message'] = 'Data user gagal ditambahkan.';
            return $response;
          } else {
            $insert = $this->db->insert("kyc", $data);
            if ($insert) {
              $kycid = $this->db->insert_id();
              $data2 = array(
                "KYCID" => $kycid,
                "HP" => $phone,
                "Pin" => $pin,
                "Type" => $type,
              );
              $insert2 = $this->db->insert("user", $data2);
              if ($insert2) {
                $response['status'] = 200;
                $response['error'] = false;
                $response['message'] = 'Data user ditambahkan.';
                return $response;
              }
            } else {
              $response['status'] = 502;
              $response['error'] = true;
              $response['message'] = 'ERROR.';
              return $response;
            }
          }
        }
      }
    }
}