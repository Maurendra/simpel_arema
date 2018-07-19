<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Email extends CI_Controller
{
 function __construct()
 {
  session_start();
  parent::__construct();
  $this->load->model('m_email');
  $this->load->model('m_chat');
  $this->load->model('m_notifikasi');
  $this->load->model('m_user');
  $this->load->library(array('template','pagination','form_validation','upload'));
  if ( !isset($_SESSION['login_app']) ) {
    redirect('c_login');
  }
}

public function index()
{
 $data['data']= $this->m_chat->getPesan();
 $data['title']="Chats";
 $data['side1']="";
 $data['side2']="";
 $data['side3']="";
 $data['side4']="";
 $data['side5']="";
 $data['side6']="";
 $data['side7']="";
 $data['side8']="active";
 $this->template->display('v_email',$data);
}

function tampil_pesan($c="")
{
  $hasil = $this->m_email->getPesan($c);
  $data = $hasil->result();
  $query = $this->db->query("SELECT COUNT(*) AS count FROM rekap_email");
  $count = $query->row();
  if ($count->COUNT < 1){
   echo '<li class="in">';
   echo '<img class="avatar" width="20px" alt="" src="'.base_url().'assets/img/pln.PNG"/>';
   echo	'<div class="message">';
   echo		'<span class="arrow"></span>';
   echo		'<a href="#" class="name">SYSTEM </a>';
   echo		'<span class="body">TIDAK ADA PESAN TERKIRIM, SILAHKAN MASUKAN PESAN</span>';
   echo	'</div>';
   echo '</li>';
 }else{
  $i=1;
  foreach ($data as $key):
    $status="in";

    echo '<li class="'.$status.'">';
    echo '<img class="avatar" width="20px" alt="" src="'.base_url().'assets/img/pln.PNG"/>';
    echo	'<div class="message">';
    echo		'<span class="arrow"></span>';
    echo		'<a href="#" class="name" onclick="return tampil_detail_pesan('.$i.')">'.$key->SUBJEK.' </a>';
    echo		'<span class="datetime"> at <font color="red">'.$key->TGL_KIRIM.'</font> </span>';
    echo		'<span class="body">'.$key->KEPADA.' </span>';
    echo		'<span class="body isi-pesan" id="psn-'.$i.'" style="display:none"><hr>'.$key->PESAN.' </span>';
    echo	'</div>';
    echo '</li>';
    $i++;
  endforeach;
}
}

public function send_email($subject, $jenis_pesan, $redirect){
 $config = [
   'useragent' => 'CodeIgniter',
   'protocol'  => 'smtp',
   'mailpath'  => '/usr/sbin/sendmail',
   'smtp_host' => 'ssl://smtp.gmail.com',
   'smtp_user' => 'tryit@gmail.com',
   'smtp_pass' => 'try', 
   'smtp_port' => 465,
   'smtp_keepalive' => TRUE,
   'smtp_crypto' => 'SSL',
   'wordwrap'  => TRUE,
   'wrapchars' => 80,
   'mailtype'  => 'html',
   'charset'   => 'utf-8',
   'validate'  => TRUE,
   'crlf'      => "\r\n",
   'newline'   => "\r\n",
 ];
 $this->load->library('email', $config);
 $this->email->to('try@gmail.com');
 $this->email->from('try@gmail.com','SIMPEL');
 $this->email->subject(rawurldecode($subject));
 $this->email->message(rawurldecode($jenis_pesan));
 if ($this->email->send()) {
  echo "string";
} else {
  show_error($this->email->print_debugger());
}
redirect(rawurldecode($redirect));
}

public function send_email_override($nopel, $jenis_pesan, $jenis_pelanggan){
  $config = [
   'useragent' => 'CodeIgniter',
   'protocol'  => 'smtp',
   'mailpath'  => '/usr/sbin/sendmail',
   'smtp_host' => 'ssl://smtp.gmail.com',
   'smtp_user' => 'tryit@gmail.com',
   'smtp_pass' => 'try',
   'smtp_port' => 465,
   'smtp_keepalive' => TRUE,
   'smtp_crypto' => 'SSL',
   'wordwrap'  => TRUE,
   'wrapchars' => 80,
   'mailtype'  => 'html',
   'charset'   => 'utf-8',
   'validate'  => TRUE,
   'crlf'      => "\r\n",
   'newline'   => "\r\n",
 ];
//AMBIL DATA EMAIL SELURUH ADMIN

 $this->load->library('email', $config);
 $tujuan = "try@gmail.com, try@gmail.com";
 $this->email->to($tujuan);
 $this->email->from('try@gmail.com','SIMPEL');
 date_default_timezone_set('Asia/Jakarta');
 $tanggal = date('d-m-Y');

     //KIRIM EMAIL DENGAN INFORMASI JENIS PELANGGAN TR
 if ($jenis_pelanggan == "TR") {
  $this->load->model('m_pelanggantr');
  $data_pelanggan = $this->m_pelanggantr->showdataedit($nopel)->row();
  if ($jenis_pesan == "Survey") {
    $this->email->subject("Survey");
     $pesan_kesan =
    '<h2>Pelanggan TR <small> dengan :</small></h2>

    <table >

    <tr>
    <td>Nama</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_PEL .'</td>
    </tr>
    <tr>
    <td>Alamat</td>
    <td>:</td>
    <td>'.$data_pelanggan->ALAMAT_PEL .'</td>
    </tr>
    <tr>
    <td>Rayon</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_RYN .'</td>
    </tr>
    <tr>
    <td>Daya Baru</td>
    <td>:</td>
    <td>'.$data_pelanggan->DAYA_BARU .'</td>
    </tr>
    <tr>
    <td>Jenis Transaksi</td>
    <td>:</td>
    <td>'.$data_pelanggan->JNS_TRANSAKSI.'</td>
    </tr>
    </table>

    <p><b>TELAH DILAKUKAN SURVEY</b> pada <b>'.$tanggal.'</b></p>';

 
    $this->email->message($pesan_kesan);
    date_default_timezone_set('Asia/Jakarta');
    $notif['tanggal_second'] = date('d-m-Y H:i:s');
    $notif['jenis_pelanggan'] = "TR";
    $notif['jenis_transaksi'] = $data_pelanggan->JNS_TRANSAKSI;
    $notif['daya_baru'] = $data_pelanggan->DAYA_BARU;
    $notif['waktu'] = $tanggal;
    $notif['nama'] = $data_pelanggan->NAMA_PEL;
    $notif['nama_rayon'] = $data_pelanggan->NAMA_RYN;
    $notif['alamat'] = $data_pelanggan->ALAMAT_PEL;
    $notif['keterangan'] = "Sudah Survey";
    $this->m_notifikasi->insert_notif($notif);
    if ($this->email->send()) {
      echo "string";
    } else {
      $log = show_error($this->email->print_debugger());
      $_SESSION['log']="<div class='alert alert-warning alert-dismissable'>
      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" .$log ."Pengiriman email gagal
      </div>";
    }
    redirect('pelanggan_TR/editdata/' .$nopel);
  }elseif ($jenis_pesan == "Bayar") {
    $this->email->subject("Bayar");
    $pesan_kesan =
    '<h2>Pelanggan TR <small> dengan :</small></h2>

    <table >

    <tr>
    <td>Nama</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_PEL .'</td>
    </tr>
    <tr>
    <td>Alamat</td>
    <td>:</td>
    <td>'.$data_pelanggan->ALAMAT_PEL .'</td>
    </tr>
    <tr>
    <td>Rayon</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_RYN .'</td>
    </tr>
    <tr>
    <td>Daya Baru</td>
    <td>:</td>
    <td>'.$data_pelanggan->DAYA_BARU .'</td>
    </tr>
    <tr>
    <td>Jenis Transaksi</td>
    <td>:</td>
    <td>'.$data_pelanggan->JNS_TRANSAKSI.'</td>
    </tr>
    </table>

    <p><b>TELAH MELAKUKAN PEMBAYARAN </b> pada <b>'.$tanggal.'</b></p>';

    $this->email->message($pesan_kesan);
    $notif['tanggal_second'] = date('d-m-Y H:i:s');
    $notif['jenis_pelanggan'] = "TR";
    $notif['jenis_transaksi'] = $data_pelanggan->JNS_TRANSAKSI;
    $notif['daya_baru'] = $data_pelanggan->DAYA_BARU;
    $notif['waktu'] = $tanggal;
    $notif['nama'] = $data_pelanggan->NAMA_PEL;
    $notif['nama_rayon'] = $data_pelanggan->NAMA_RYN;
    $notif['alamat'] = $data_pelanggan->ALAMAT_PEL;
    $notif['keterangan'] = "Sudah Bayar";
    $this->m_notifikasi->insert_notif($notif);
    if ($this->email->send()) {
      echo "string";
    } else {
      $log = show_error($this->email->print_debugger());
      $_SESSION['log']="<div class='alert alert-warning alert-dismissable'>
      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" .$log ."Pengiriman email gagal
      </div>";
    }
    redirect('pelanggan_TR/editdata/' .$nopel);
  }elseif ($jenis_pesan == "RAB") {
    $tgl_nodin_ke_KON = "(Belum diisi)"; $tgl_nodin_ke_PENGADAAN = "(Belum diisi)";
    if ($data_pelanggan->TGL_NODINKEKON != "") {
      $tgl_nodin_ke_KON = $data_pelanggan->TGL_NODINKEKON;
    }
    if ($data_pelanggan->TGL_NODINKEPENG != "") {
      $tgl_nodin_ke_PENGADAAN = $data_pelanggan->TGL_NODINKEPENG;
    }
    date_default_timezone_set('Asia/Jakarta');
    $tanggal = date('d-m-Y');

    $pesan_kesan =
    '<h2>Pelanggan TR <small> dengan :</small></h2>

    <table >

    <tr>
    <td>Nama</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_PEL .'</td>
    </tr>
    <tr>
    <td>Alamat</td>
    <td>:</td>
    <td>'.$data_pelanggan->ALAMAT_PEL .'</td>
    </tr>
    <tr>
    <td>Rayon</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_RYN .'</td>
    </tr>
    <tr>
    <td>Daya Baru</td>
    <td>:</td>
    <td>'.$data_pelangga->DAYA_BARU .'</td>
    </tr>
    <tr>
    <td>Jenis Transaksi</td>
    <td>:</td>
    <td>'.$data_pelanggan->JNS_TRANSAKSI.'</td>
    </tr>
    </table>

    <p><b>TELAH DIBUATKAN DAN DIKIRIMKAN RAB</b> pada </p>

    <table >

    <tr>
    <td>Tanggal</td>
    <td>:</td>
    <td>'.$tanggal.'</td>
    </tr>
    <tr>
    <td>No. Nota Dinas</td>
    <td>:</td>
    <td>'.$data_pelanggan->NO_NOTADINAS.'</td>
    </tr>
    <tr>
    <td>Tgl Kirim Nota Dinas Ke Konstruksi</td>
    <td>:</td>
    <td>'.$tgl_nodin_ke_KON.'</td>
    </tr>
    <tr>
    <td>Tgl Kirim Nota Dinas Ke Pengadaan</td>
    <td>:</td>
    <td>'.$tgl_nodin_ke_PENGADAAN.'</td>
    </tr>
    
    </table>';
    $subject = "RAB";
    $this->email->subject("RAB");
    $this->email->message($pesan_kesan);
    $notif['tanggal_second'] = date('d-m-Y H:i:s');
    $notif['jenis_pelanggan'] = "TR";
    $notif['jenis_transaksi'] = $data_pelanggan->JNS_TRANSAKSI;
    $notif['daya_baru'] = $data_pelanggan->DAYA_BARU;
    $notif['waktu'] = $tanggal;
    $notif['nama'] = $data_pelanggan->NAMA_PEL;
    $notif['nama_rayon'] = $data_pelanggan->NAMA_RYN;
    $notif['alamat'] = $data_pelanggan->ALAMAT_PEL;
    $notif['keterangan'] = "Sudah RAB";
    $this->m_notifikasi->insert_notif($notif);
    if ($this->email->send()) {
      echo "string";
    } else {
      $log = show_error($this->email->print_debugger());
      $_SESSION['log']="<div class='alert alert-warning alert-dismissable'>
      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" .$log ."Pengiriman email gagal
      </div>";
    }
    redirect('pelanggan_TR/editdata/' .$nopel);
  }elseif ($jenis_pesan == "Pelaksanaan") {
    date_default_timezone_set('Asia/Jakarta');
    $tanggal = date('d-m-Y');
    $this->email->subject("Data Pelaksanaan");
    $pesan_kesan =
    '<h2>Pelanggan TR <small> dengan :</small></h2>

    <table >

    <tr>
    <td>Nama</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_PEL .'</td>
    </tr>
    <tr>
    <td>Alamat</td>
    <td>:</td>
    <td>'.$data_pelanggan->ALAMAT_PEL .'</td>
    </tr>
    <tr>
    <td>Rayon</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_RYN .'</td>
    </tr>
    <tr>
    <td>Daya Baru</td>
    <td>:</td>
    <td>'.$data_pelangga->DAYA_BARU .'</td>
    </tr>
    <tr>
    <td>Jenis Transaksi</td>
    <td>:</td>
    <td>'.$data_pelanggan->JNS_TRANSAKSI.'</td>
    </tr>
    </table>

    <p><b>TELAH DILAKUKAN PELAKSANAAN</b> pada </p>

    <table >

    <tr>
    <td>Tanggal</td>
    <td>:</td>
    <td>'.$tanggal.'</td>
    </tr>
    <tr>
    <td>No. SPK</td>
    <td>:</td>
    <td>'.$data_pelanggan->NO_SPK.'</td>
    </tr>
    <tr>
    <td>Vendor</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_VENDORPELAK.'</td>
    </tr>
    
    </table>';
    $this->email->message($pesan_kesan);
    $notif['tanggal_second'] = date('d-m-Y H:i:s');
    $notif['jenis_pelanggan'] = "TR";
    $notif['jenis_transaksi'] = $data_pelanggan->JNS_TRANSAKSI;
    $notif['daya_baru'] = $data_pelanggan->DAYA_BARU;
    $notif['waktu'] = $tanggal;
    $notif['nama'] = $data_pelanggan->NAMA_PEL;
    $notif['nama_rayon'] = $data_pelanggan->NAMA_RYN;
    $notif['alamat'] = $data_pelanggan->ALAMAT_PEL;
    $notif['keterangan'] = "Sudah Pelaksanaan";
    $this->m_notifikasi->insert_notif($notif);
    if ($this->email->send()) {
      echo "string";
    } else {
      $log = show_error($this->email->print_debugger());
      $_SESSION['log']="<div class='alert alert-warning alert-dismissable'>
      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" .$log ."Pengiriman email gagal
      </div>";
    }
    redirect('pelanggan_TR/editdata/' .$nopel);
  }elseif ($jenis_pesan == "Nyala") {
    date_default_timezone_set('Asia/Jakarta');
    $tanggal = date('d-m-Y');
    $this->email->subject("Data Nyala");

    $pesan_kesan =
    '<h2>Pelanggan TR <small> dengan :</small></h2>

    <table >

    <tr>
    <td>Nama</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_PEL .'</td>
    </tr>
    <tr>
    <td>Alamat</td>
    <td>:</td>
    <td>'.$data_pelanggan->ALAMAT_PEL .'</td>
    </tr>
    <tr>
    <td>Rayon</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_RYN .'</td>
    </tr>
    <tr>
    <td>Daya Baru</td>
    <td>:</td>
    <td>'.$data_pelanggan->DAYA_BARU .'</td>
    </tr>
    <tr>
    <td>Jenis Transaksi</td>
    <td>:</td>
    <td>'.$data_pelanggan->JNS_TRANSAKSI.'</td>
    </tr>
    </table>

    <p><b>TELAH NYALA </b> pada <b>'.$tanggal.'</b></p>';

  
    $this->email->message($pesan_kesan);
    $notif['tanggal_second'] = date('d-m-Y H:i:s');
    $notif['jenis_pelanggan'] = "TR";
    $notif['jenis_transaksi'] = $data_pelanggan->JNS_TRANSAKSI;
    $notif['daya_baru'] = $data_pelanggan->DAYA_BARU;
    $notif['waktu'] = $tanggal;
    $notif['nama'] = $data_pelanggan->NAMA_PEL;
    $notif['nama_rayon'] = $data_pelanggan->NAMA_RYN;
    $notif['alamat'] = $data_pelanggan->ALAMAT_PEL;
    $notif['keterangan'] = "Sudah Nyala";
    $this->m_notifikasi->insert_notif($notif);
    if ($this->email->send()) {
      echo "string";
    } else {
      $log = show_error($this->email->print_debugger());
      $_SESSION['log']="<div class='alert alert-warning alert-dismissable'>
      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" .$log ."Pengiriman email gagal
      </div>";
    }
    redirect('pelanggan_TR/editdata/' .$nopel);
  }else{
    $_SESSION['log']="<div class='alert alert-warning alert-dismissable'>
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button> Jenis pesan tidak ditemukan
    </div>";
    redirect('pelanggan_TR/editdata/' .$nopel);
  }
}
    //END KIRIM EMAIL DENGAN INFORMASI JENIS PELANGGAN TR

    //KIRIM EMAIL DENGAN INFORMASI JENIS PELANGGAN TM
elseif ($jenis_pelanggan == "TM") {
  $this->load->model('m_pelanggantm');
  $data_pelanggan = $this->m_pelanggantm->showdataedit($nopel)->row();
  if ($jenis_pesan == "Survey") {
    $this->email->subject("Survey");
    $pesan_kesan = 
    '<h2>Pelanggan TM <small> dengan :</small></h2>

    <table >

    <tr>
    <td>Nama</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_PEL .'</td>
    </tr>
    <tr>
    <td>Alamat</td>
    <td>:</td>
    <td>'.$data_pelanggan->ALAMAT_PEL .'</td>
    </tr>
    <tr>
    <td>Rayon</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_RYN .'</td>
    </tr>
    <tr>
    <td>Daya Baru</td>
    <td>:</td>
    <td>'.$data_pelanggan->DAYA_BARU .'</td>
    </tr>
    <tr>
    <td>Jenis Transaksi</td>
    <td>:</td>
    <td>'.$data_pelanggan->JNS_TRANSAKSI.'</td>
    </tr>
    </table>

    <p><b>TELAH DILAKUKAN SURVEY</b> pada <b>'.$tanggal.'</b></p>';


    $this->email->message($pesan_kesan);
    $notif['tanggal_second'] = date('d-m-Y H:i:s');
    $notif['jenis_pelanggan'] = "TM";
    $notif['jenis_transaksi'] = $data_pelanggan->JNS_TRANSAKSI;
    $notif['daya_baru'] = $data_pelanggan->DAYA_BARU;
    $notif['waktu'] = $tanggal;
    $notif['nama'] = $data_pelanggan->NAMA_PEL;
    $notif['nama_rayon'] = $data_pelanggan->NAMA_RYN;
    $notif['alamat'] = $data_pelanggan->ALAMAT_PEL;
    $notif['keterangan'] = "Sudah Survey";
    $this->m_notifikasi->insert_notif($notif);
    if ($this->email->send()) {
      echo "string";
    } else {
      $log = show_error($this->email->print_debugger());
      $_SESSION['log']="<div class='alert alert-warning alert-dismissable'>
      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" .$log ."Pengiriman email gagal
      </div>";
    }
    redirect('pelanggan_TM/editdata/' .$nopel);
  }elseif ($jenis_pesan == "Bayar") {
    $this->email->subject("Bayar");
    $pesan_kesan =
    '<h2>Pelanggan TM <small> dengan :</small></h2>

    <table >

    <tr>
    <td>Nama</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_PEL .'</td>
    </tr>
    <tr>
    <td>Alamat</td>
    <td>:</td>
    <td>'.$data_pelanggan->ALAMAT_PEL .'</td>
    </tr>
    <tr>
    <td>Rayon</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_RYN .'</td>
    </tr>
    <tr>
    <td>Daya Baru</td>
    <td>:</td>
    <td>'.$data_pelanggan->DAYA_BARU .'</td>
    </tr>
    <tr>
    <td>Jenis Transaksi</td>
    <td>:</td>
    <td>'.$data_pelanggan->JNS_TRANSAKSI.'</td>
    </tr>
    </table>

    <p><b>TELAH MELAKUKAN PEMBAYARAN </b> pada <b>'.$tanggal.'</b></p>';

    $this->email->message($pesan_kesan);
    $notif['tanggal_second'] = date('d-m-Y H:i:s');
    $notif['jenis_pelanggan'] = "TM";
    $notif['jenis_transaksi'] = $data_pelanggan->JNS_TRANSAKSI;
    $notif['daya_baru'] = $data_pelanggan->DAYA_BARU;
    $notif['waktu'] = $tanggal;
    $notif['nama'] = $data_pelanggan->NAMA_PEL;
    $notif['nama_rayon'] = $data_pelanggan->NAMA_RYN;
    $notif['alamat'] = $data_pelanggan->ALAMAT_PEL;
    $notif['keterangan'] = "Sudah Bayar";
    $this->m_notifikasi->insert_notif($notif);
    if ($this->email->send()) {
      echo "string";
    } else {
      $log = show_error($this->email->print_debugger());
      $_SESSION['log']="<div class='alert alert-warning alert-dismissable'>
      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" .$log ."Pengiriman email gagal
      </div>";
    }
    redirect('pelanggan_TM/editdata/' .$nopel);
  }elseif ($jenis_pesan == "RAB") {
    $tgl_nodin_ke_KON = "(Belum diisi)"; $tgl_nodin_ke_PP = "(Belum diisi)";
    if ($data_pelanggan->TGL_NODINKEKON != "") {
      $tgl_nodin_ke_KON = $data_pelanggan->TGL_NODINKEKON;
    }
    if ($data_pelanggan->TGL_NODINPPDARIREN != "") {
      $tgl_nodin_ke_PP = $data_pelanggan->TGL_NODINPPDARIREN;
    }
    date_default_timezone_set('Asia/Jakarta');
    $tanggal = date('d-m-Y');

    $pesan_kesan =
    '<h2>Pelanggan TM <small> dengan :</small></h2>

    <table >

    <tr>
    <td>Nama</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_PEL .'</td>
    </tr>
    <tr>
    <td>Alamat</td>
    <td>:</td>
    <td>'.$data_pelanggan->ALAMAT_PEL .'</td>
    </tr>
    <tr>
    <td>Rayon</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_RYN .'</td>
    </tr>
    <tr>
    <td>Daya Baru</td>
    <td>:</td>
    <td>'.$data_pelangga->DAYA_BARU .'</td>
    </tr>
    <tr>
    <td>Jenis Transaksi</td>
    <td>:</td>
    <td>'.$data_pelanggan->JNS_TRANSAKSI.'</td>
    </tr>
    </table>

    <p><b>TELAH DIBUATKAN DAN DIKIRIMKAN RAB</b> pada </p>

    <table >

    <tr>
    <td>Tanggal</td>
    <td>:</td>
    <td>'.$tanggal.'</td>
    </tr>
    <tr>
    <td>No. Nota Dinas</td>
    <td>:</td>
    <td>'.$data_pelanggan->NO_NOTADINAS.'</td>
    </tr>
    <tr>
    <td>Tgl Kirim Nota Dinas Ke Konstruksi</td>
    <td>:</td>
    <td>'.$tgl_nodin_ke_KON.'</td>
    </tr>
    <tr>
    <td>Tgl Kirim Nota Dinas Ke Pengadaan</td>
    <td>:</td>
    <td>'.$tgl_nodin_ke_PP.'</td>
    </tr>
    
    </table>';

    
    $subject = "RAB";
    $this->email->subject("RAB");
    $this->email->message($pesan_kesan);
    $notif['tanggal_second'] = date('d-m-Y H:i:s');
    $notif['jenis_pelanggan'] = "TM";
    $notif['jenis_transaksi'] = $data_pelanggan->JNS_TRANSAKSI;
    $notif['daya_baru'] = $data_pelanggan->DAYA_BARU;
    $notif['waktu'] = $tanggal;
    $notif['nama'] = $data_pelanggan->NAMA_PEL;
    $notif['nama_rayon'] = $data_pelanggan->NAMA_RYN;
    $notif['alamat'] = $data_pelanggan->ALAMAT_PEL;
    $notif['keterangan'] = "Sudah RAB";
    $this->m_notifikasi->insert_notif($notif);
    if ($this->email->send()) {
      echo "string";
    } else {
      $log = show_error($this->email->print_debugger());
      $_SESSION['log']="<div class='alert alert-warning alert-dismissable'>
      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" .$log ."Pengiriman email gagal
      </div>";
    }
    redirect('pelanggan_TM/editdata/' .$nopel);
  }elseif ($jenis_pesan == "Pelaksanaan") {
    date_default_timezone_set('Asia/Jakarta');
    $tanggal = date('d-m-Y');
    $this->email->subject("Data Pelaksanaan");

    $pesan_kesan =
    '<h2>Pelanggan TM <small> dengan :</small></h2>

    <table >

    <tr>
    <td>Nama</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_PEL .'</td>
    </tr>
    <tr>
    <td>Alamat</td>
    <td>:</td>
    <td>'.$data_pelanggan->ALAMAT_PEL .'</td>
    </tr>
    <tr>
    <td>Rayon</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_RYN .'</td>
    </tr>
    <tr>
    <td>Daya Baru</td>
    <td>:</td>
    <td>'.$data_pelangga->DAYA_BARU .'</td>
    </tr>
    <tr>
    <td>Jenis Transaksi</td>
    <td>:</td>
    <td>'.$data_pelanggan->JNS_TRANSAKSI.'</td>
    </tr>
    </table>

    <p><b>TELAH DILAKUKAN PELAKSANAAN</b> pada </p>

    <table >

    <tr>
    <td>Tanggal</td>
    <td>:</td>
    <td>'.$tanggal.'</td>
    </tr>
    <tr>
    <td>No. SPK</td>
    <td>:</td>
    <td>'.$data_pelanggan->NO_SPK.'</td>
    </tr>
    <tr>
    <td>Vendor</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_VENDORPELAK.'</td>
    </tr>
    
    </table>';

   
    $this->email->message($pesan_kesan);
    $notif['tanggal_second'] = date('d-m-Y H:i:s');
    $notif['jenis_pelanggan'] = "TM";
    $notif['jenis_transaksi'] = $data_pelanggan->JNS_TRANSAKSI;
    $notif['daya_baru'] = $data_pelanggan->DAYA_BARU;
    $notif['waktu'] = $tanggal;
    $notif['nama'] = $data_pelanggan->NAMA_PEL;
    $notif['nama_rayon'] = $data_pelanggan->NAMA_RYN;
    $notif['alamat'] = $data_pelanggan->ALAMAT_PEL;
    $notif['keterangan'] = "Sudah Pelaksanaan";
    $this->m_notifikasi->insert_notif($notif);
    if ($this->email->send()) {
      echo "string";
    } else {
      $log = show_error($this->email->print_debugger());
      $_SESSION['log']="<div class='alert alert-warning alert-dismissable'>
      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" .$log ."Pengiriman email gagal
      </div>";
    }
    redirect('pelanggan_TM/editdata/' .$nopel);
  }elseif ($jenis_pesan == "Nyala") {
    date_default_timezone_set('Asia/Jakarta');
    $tanggal = date('d-m-Y');
    $this->email->subject("Data Nyala");


    $pesan_kesan =
    '<h2>Pelanggan TM <small> dengan :</small></h2>

    <table >

    <tr>
    <td>Nama</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_PEL .'</td>
    </tr>
    <tr>
    <td>Alamat</td>
    <td>:</td>
    <td>'.$data_pelanggan->ALAMAT_PEL .'</td>
    </tr>
    <tr>
    <td>Rayon</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_RYN .'</td>
    </tr>
    <tr>
    <td>Daya Baru</td>
    <td>:</td>
    <td>'.$data_pelanggan->DAYA_BARU .'</td>
    </tr>
    <tr>
    <td>Jenis Transaksi</td>
    <td>:</td>
    <td>'.$data_pelanggan->JNS_TRANSAKSI.'</td>
    </tr>
    </table>

    <p><b>TELAH NYALA </b> pada <b>'.$tanggal.'</b></p>';

    $this->email->message($pesan_kesan);
    $notif['tanggal_second'] = date('d-m-Y H:i:s');
    $notif['jenis_pelanggan'] = "TM";
    $notif['jenis_transaksi'] = $data_pelanggan->JNS_TRANSAKSI;
    $notif['daya_baru'] = $data_pelanggan->DAYA_BARU;
    $notif['waktu'] = $tanggal;
    $notif['nama'] = $data_pelanggan->NAMA_PEL;
    $notif['nama_rayon'] = $data_pelanggan->NAMA_RYN;
    $notif['alamat'] = $data_pelanggan->ALAMAT_PEL;
    $notif['keterangan'] = "Sudah Nyala";
    $this->m_notifikasi->insert_notif($notif);
    if ($this->email->send()) {
      echo "string";
    } else {
      $log = show_error($this->email->print_debugger());
      $_SESSION['log']="<div class='alert alert-warning alert-dismissable'>
      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" .$log ."Pengiriman email gagal
      </div>";
    }
    redirect('pelanggan_TM/editdata/' .$nopel);
  }else{
    $_SESSION['log']="<div class='alert alert-warning alert-dismissable'>
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button> Jenis pesan tidak ditemukan
    </div>";
    redirect('pelanggan_TM/editdata/' .$nopel);
  }
}
    //END KIRIM EMAIL DENGAN INFORMASI JENIS PELANGGAN TM

    //KIRIM EMAIL DENGAN INFORMASI JENIS PELANGGAN KOLEKTIF
elseif ($jenis_pelanggan == "KOLEKTIF") {
  $this->load->model('m_kolektif');
  $data_pelanggan = $this->m_kolektif->showdataedit($nopel)->row();
  if ($jenis_pesan == "Survey") {
    $this->email->subject("Survey");

    $pesan_kesan = 
    '<h2>Pelanggan KOLEKTIF <small> dengan :</small></h2>

    <table >

    <tr>
    <td>Nama</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_PEL .'</td>
    </tr>
    <tr>
    <td>Alamat</td>
    <td>:</td>
    <td>'.$data_pelanggan->ALAMAT_PEL .'</td>
    </tr>
    <tr>
    <td>Rayon</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_RYN .'</td>
    </tr>
    <tr>
    <td>Daya Baru</td>
    <td>:</td>
    <td>'.$data_pelanggan->DAYA_BARU .'</td>
    </tr>
    <tr>
    <td>Jenis Transaksi</td>
    <td>:</td>
    <td>'.$data_pelanggan->JNS_TRANSAKSI.'</td>
    </tr>
    </table>

    <p><b>TELAH DILAKUKAN SURVEY</b> pada <b>'.$tanggal.'</b></p>';

    
    $this->email->message($pesan_kesan);
    $notif['tanggal_second'] = date('d-m-Y H:i:s');
    $notif['jenis_pelanggan'] = "KOLEKTIF";
    $notif['jenis_transaksi'] = "-";
    $notif['daya_baru'] = "-";
    $notif['waktu'] = $tanggal;
    $notif['nama'] = $data_pelanggan->NAMA_PEL;
    $notif['nama_rayon'] = $data_pelanggan->NAMA_RYN;
    $notif['alamat'] = $data_pelanggan->ALAMAT_PEL;
    $notif['keterangan'] = "Sudah Survey";
    $this->m_notifikasi->insert_notif($notif);
    if ($this->email->send()) {
      echo "string";
    } else {
      $log = error_get_last();
      $_SESSION['log']="<div class='alert alert-warning alert-dismissable'>
      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" .$log ."Pengiriman email gagal
      </div>";
    }
    redirect('kolektif/editdata/' .$nopel);
  }elseif ($jenis_pesan == "Bayar") {
    $this->email->subject("Bayar");

    $pesan_kesan =
    '<h2>Pelanggan KOLEKTIF <small> dengan :</small></h2>

    <table >

    <tr>
    <td>Nama</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_PEL .'</td>
    </tr>
    <tr>
    <td>Alamat</td>
    <td>:</td>
    <td>'.$data_pelanggan->ALAMAT_PEL .'</td>
    </tr>
    <tr>
    <td>Rayon</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_RYN .'</td>
    </tr>
    <tr>
    <td>Daya Baru</td>
    <td>:</td>
    <td>'.$data_pelanggan->DAYA_BARU .'</td>
    </tr>
    <tr>
    <td>Jenis Transaksi</td>
    <td>:</td>
    <td>'.$data_pelanggan->JNS_TRANSAKSI.'</td>
    </tr>
    </table>

    <p><b>TELAH MELAKUKAN PEMBAYARAN </b> pada <b>'.$tanggal.'</b></p>';

    $this->email->message($pesan_kesan);
    $notif['tanggal_second'] = date('d-m-Y H:i:s');
    $notif['jenis_pelanggan'] = "KOLEKTIF";
    $notif['jenis_transaksi'] = "-";
    $notif['daya_baru'] = "-";
    $notif['waktu'] = $tanggal;
    $notif['nama'] = $data_pelanggan->NAMA_PEL;
    $notif['nama_rayon'] = $data_pelanggan->NAMA_RYN;
    $notif['alamat'] = $data_pelanggan->ALAMAT_PEL;
    $notif['keterangan'] = "Sudah Bayar";
    $this->m_notifikasi->insert_notif($notif);
    if ($this->email->send()) {
      echo "string";
    } else {
      $log = error_get_last();
      $_SESSION['log']="<div class='alert alert-warning alert-dismissable'>
      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" .$log ."Pengiriman email gagal
      </div>";
    }
    redirect('kolektif/editdata/' .$nopel);
  }elseif ($jenis_pesan == "RAB") {
    $tgl_nodin_ke_KON = "(Belum diisi)"; $tgl_nodin_ke_PENGADAAN = "(Belum diisi)";
    if ($data_pelanggan->TGL_NODINKEKON != "") {
      $tgl_nodin_ke_KON = $data_pelanggan->TGL_NODINKEKON;
    }
    if ($data_pelanggan->TGL_NODINKEPENG != "") {
      $tgl_nodin_ke_PENGADAAN = $data_pelanggan->TGL_NODINKEPENG;
    }
    date_default_timezone_set('Asia/Jakarta');
    $tanggal = date('d-m-Y');

    $pesan_kesan =
    '<h2>Pelanggan KOLEKTIF <small> dengan :</small></h2>

    <table >

    <tr>
    <td>Nama</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_PEL .'</td>
    </tr>
    <tr>
    <td>Alamat</td>
    <td>:</td>
    <td>'.$data_pelanggan->ALAMAT_PEL .'</td>
    </tr>
    <tr>
    <td>Rayon</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_RYN .'</td>
    </tr>
    <tr>
    <td>Daya Baru</td>
    <td>:</td>
    <td>'.$data_pelangga->DAYA_BARU .'</td>
    </tr>
    <tr>
    <td>Jenis Transaksi</td>
    <td>:</td>
    <td>'.$data_pelanggan->JNS_TRANSAKSI.'</td>
    </tr>
    </table>

    <p><b>TELAH DIBUATKAN DAN DIKIRIMKAN RAB</b> pada </p>

    <table >

    <tr>
    <td>Tanggal</td>
    <td>:</td>
    <td>'.$tanggal.'</td>
    </tr>
    <tr>
    <td>No. Nota Dinas</td>
    <td>:</td>
    <td>'.$data_pelanggan->NO_NOTADINAS.'</td>
    </tr>
    <tr>
    <td>Tgl Kirim Nota Dinas Ke Konstruksi</td>
    <td>:</td>
    <td>'.$tgl_nodin_ke_KON.'</td>
    </tr>
    <tr>
    <td>Tgl Kirim Nota Dinas Ke Pengadaan</td>
    <td>:</td>
    <td>'.$tgl_nodin_ke_PENGADAAN.'</td>
    </tr>
    
    </table>';

 
    $subject = "RAB";
    $this->email->subject("RAB");
    $this->email->message($pesan_kesan);
    $notif['tanggal_second'] = date('d-m-Y H:i:s');
    $notif['jenis_pelanggan'] = "KOLEKTIF";
    $notif['jenis_transaksi'] = "-";
    $notif['daya_baru'] = "-";
    $notif['waktu'] = $tanggal;
    $notif['nama'] = $data_pelanggan->NAMA_PEL;
    $notif['nama_rayon'] = $data_pelanggan->NAMA_RYN;
    $notif['alamat'] = $data_pelanggan->ALAMAT_PEL;
    $notif['keterangan'] = "Sudah RAB";
    $this->m_notifikasi->insert_notif($notif);
    if ($this->email->send()) {
      echo "string";
    } else {
      $log = error_get_last();
      $_SESSION['log']="<div class='alert alert-warning alert-dismissable'>
      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" .$log ."Pengiriman email gagal
      </div>";
    }
    redirect('kolektif/editdata/' .$nopel);
  }elseif ($jenis_pesan == "Pelaksanaan") {
    date_default_timezone_set('Asia/Jakarta');
    $tanggal = date('d-m-Y');
    $this->email->subject("Data Pelaksanaan");

    $pesan_kesan =
    '<h2>Pelanggan KOLEKTIF <small> dengan :</small></h2>

    <table >

    <tr>
    <td>Nama</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_PEL .'</td>
    </tr>
    <tr>
    <td>Alamat</td>
    <td>:</td>
    <td>'.$data_pelanggan->ALAMAT_PEL .'</td>
    </tr>
    <tr>
    <td>Rayon</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_RYN .'</td>
    </tr>
    <tr>
    <td>Daya Baru</td>
    <td>:</td>
    <td>'.$data_pelangga->DAYA_BARU .'</td>
    </tr>
    <tr>
    <td>Jenis Transaksi</td>
    <td>:</td>
    <td>'.$data_pelanggan->JNS_TRANSAKSI.'</td>
    </tr>
    </table>

    <p><b>TELAH DILAKUKAN PELAKSANAAN</b> pada </p>

    <table >

    <tr>
    <td>Tanggal</td>
    <td>:</td>
    <td>'.$tanggal.'</td>
    </tr>
    <tr>
    <td>No. SPK</td>
    <td>:</td>
    <td>'.$data_pelanggan->NO_SPK.'</td>
    </tr>
    <tr>
    <td>Vendor</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_VENDORPELAK.'</td>
    </tr>
    
    </table>';

    $this->email->message($pesan_kesan);
    $notif['tanggal_second'] = date('d-m-Y H:i:s');
    $notif['jenis_pelanggan'] = "KOLEKTIF";
    $notif['jenis_transaksi'] = "-";
    $notif['daya_baru'] = "-";
    $notif['waktu'] = $tanggal;
    $notif['nama'] = $data_pelanggan->NAMA_PEL;
    $notif['nama_rayon'] = $data_pelanggan->NAMA_RYN;
    $notif['alamat'] = $data_pelanggan->ALAMAT_PEL;
    $notif['keterangan'] = "Sudah Pelaksanaan";
    $this->m_notifikasi->insert_notif($notif);
    if ($this->email->send()) {
      echo "string";
    } else {
      $log = error_get_last();
      $_SESSION['log']="<div class='alert alert-warning alert-dismissable'>
      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" .$log ."Pengiriman email gagal
      </div>";
    }
    redirect('kolektif/editdata/' .$nopel);
  }elseif ($jenis_pesan == "Nyala") {
    date_default_timezone_set('Asia/Jakarta');
    $tanggal = date('d-m-Y');
    $this->email->subject("Data Nyala");

    $pesan_kesan =
    '<h2>Pelanggan TM <small> dengan :</small></h2>

    <table >

    <tr>
    <td>Nama</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_PEL .'</td>
    </tr>
    <tr>
    <td>Alamat</td>
    <td>:</td>
    <td>'.$data_pelanggan->ALAMAT_PEL .'</td>
    </tr>
    <tr>
    <td>Rayon</td>
    <td>:</td>
    <td>'.$data_pelanggan->NAMA_RYN .'</td>
    </tr>
    <tr>
    <td>Daya Baru</td>
    <td>:</td>
    <td>'.$data_pelanggan->DAYA_BARU .'</td>
    </tr>
    <tr>
    <td>Jenis Transaksi</td>
    <td>:</td>
    <td>'.$data_pelanggan->JNS_TRANSAKSI.'</td>
    </tr>
    </table>

    <p><b>TELAH NYALA </b> pada <b>'.$tanggal.'</b></p>';

    $this->email->message($pesan_kesan);
    $notif['tanggal_second'] = date('d-m-Y H:i:s');
    $notif['jenis_pelanggan'] = "KOLEKTIF";
    $notif['jenis_transaksi'] = "-";
    $notif['daya_baru'] = "-";
    $notif['waktu'] = $tanggal;
    $notif['nama'] = $data_pelanggan->NAMA_PEL;
    $notif['nama_rayon'] = $data_pelanggan->NAMA_RYN;
    $notif['alamat'] = $data_pelanggan->ALAMAT_PEL;
    $notif['keterangan'] = "Sudah Nyala";
    $this->m_notifikasi->insert_notif($notif);
    if ($this->email->send()) {
      echo "sukses";
    } else {
      $log = error_get_last();
      $_SESSION['log']="<div class='alert alert-warning alert-dismissable'>
      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" .$log ."Pengiriman email gagal
      </div>";
    }
    redirect('kolektif/editdata/' .$nopel);
  }else{
    $_SESSION['log']="<div class='alert alert-warning alert-dismissable'>
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button> Jenis pesan tidak ditemukan
    </div>";
    redirect('kolektif/editdata/' .$nopel);
  }
}
}

//INI BUAT NGAMBIL DATA EMAIL SEMUA ADMIN
// function cek(){
//     $query = $this->m_user->userfromdb();
//     $tujuan = "";
//     foreach ($query as $key) {
//        $tujuan .= $key->EMAIL .", ";
//    }  
//    echo $tujuan;
// }

}
?>
