<?php
require_once APPPATH . 'models/Manajemen_penyewa_model.php';
require_once APPPATH . 'helpers/auth_helper.php';
require_once APPPATH . 'helpers/flash_helper.php';

/** Controller CRUD penyewa pada sisi admin (MVC + PRG). */
class Manajemen_penyewa_controller extends Controller
{
    private Manajemen_penyewa_model $model;
    public function __construct() { $this->model = new Manajemen_penyewa_model(); }
    private function token(): string { if (empty($_SESSION['csrf_penyewa_admin'])) $_SESSION['csrf_penyewa_admin'] = bin2hex(random_bytes(32)); return $_SESSION['csrf_penyewa_admin']; }
    private function checkPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Metode tidak diizinkan.'); }
        if (!hash_equals($this->token(), (string)($_POST['_token'] ?? ''))) { http_response_code(403); exit('Token keamanan tidak valid. Muat ulang halaman.'); }
    }
    public function index(): void
    {
        require_admin_login();
        $this->view('admin/penyewa_view', ['username'=>$_SESSION['admin_username'], 'penyewa'=>$this->model->getAll(), 'flash'=>get_flash_message(), 'mode'=>null, 'item'=>null, 'error'=>null, 'token'=>$this->token()]);
    }
    public function tambah(): void
    {
        require_admin_login();
        if ($_SERVER['REQUEST_METHOD'] === 'GET') { $this->render('tambah', []); return; }
        $this->checkPost();
        $d = $this->input(); $error = $this->validate($d, true);
        if ($error) { $this->render('tambah', $d, $error); return; }
        if ($this->model->find($d['user_plg'])) { $this->render('tambah', $d, 'Username penyewa sudah digunakan.'); return; }
        $files = $this->uploadBoth();
        if (!$files['ok']) { $this->render('tambah', $d, $files['error']); return; }
        $d['ft_ktp']=$files['ktp']; $d['ft_sim_a']=$files['sim']; $d['sandi']=password_hash($d['password'], PASSWORD_DEFAULT);
        try { $ok=$this->model->create($d); } catch (Throwable $e) { $this->removeFiles([$d['ft_ktp'],$d['ft_sim_a']]); throw $e; }
        if (!$ok) { $this->removeFiles([$d['ft_ktp'],$d['ft_sim_a']]); $this->render('tambah',$d,'Data penyewa gagal disimpan.'); return; }
        set_flash_message('success','Data penyewa berhasil ditambahkan.'); header('Location: '.site_url('admin/penyewa')); exit;
    }
    public function ubah(string $user): void
    {
        require_admin_login(); $old=$this->model->find($user);
        if (!$old) { set_flash_message('danger','Data penyewa tidak ditemukan.'); header('Location: '.site_url('admin/penyewa')); exit; }
        if ($_SERVER['REQUEST_METHOD']==='GET') { $this->render('ubah',$old); return; }
        $this->checkPost(); $d=$this->input(); $error=$this->validate($d,false);
        if ($error) { $this->render('ubah',array_merge($old,$d),$error); return; }
        $files=$this->uploadBoth(); if (!$files['ok']) { $this->render('ubah',array_merge($old,$d),$files['error']); return; }
        $d['ft_ktp']=$files['ktp'] ?? ($old['ft_ktp'] ?? null); $d['ft_sim_a']=$files['sim'] ?? ($old['ft_sim_a'] ?? null);
        $d['sandi']=($d['password']!=='') ? password_hash($d['password'],PASSWORD_DEFAULT) : null;
        try { $ok=$this->model->update($user,$d); } catch (Throwable $e) { $this->removeFiles([$files['ktp'],$files['sim']]); throw $e; }
        if (!$ok) { $this->removeFiles([$files['ktp'],$files['sim']]); $this->render('ubah',array_merge($old,$d),'Data penyewa gagal diperbarui.'); return; }
        if ($files['ktp'] && !empty($old['ft_ktp'])) $this->removeFiles([$old['ft_ktp']]);
        if ($files['sim'] && !empty($old['ft_sim_a'])) $this->removeFiles([$old['ft_sim_a']]);
        set_flash_message('success','Data penyewa berhasil diperbarui.'); header('Location: '.site_url('admin/penyewa')); exit;
    }
    public function hapus(string $user): void
    {
        require_admin_login(); $this->checkPost(); $old=$this->model->find($user);
        if (!$old) { set_flash_message('danger','Data penyewa tidak ditemukan.'); header('Location: '.site_url('admin/penyewa')); exit; }
        try { $ok=$this->model->delete($user); } catch (Throwable $e) { set_flash_message('danger','Data tidak dapat dihapus karena masih memiliki transaksi terkait.'); header('Location: '.site_url('admin/penyewa')); exit; }
        if ($ok) $this->removeFiles([$old['ft_ktp'],$old['ft_sim_a']]);
        set_flash_message($ok?'success':'danger',$ok?'Data penyewa berhasil dihapus.':'Data penyewa gagal dihapus.'); header('Location: '.site_url('admin/penyewa')); exit;
    }
    private function input(): array { return ['user_plg'=>trim((string)($_POST['user_plg']??'')), 'nik'=>trim((string)($_POST['nik']??'')), 'nm_lngkp'=>trim((string)($_POST['nm_lngkp']??'')), 'no_hp'=>trim((string)($_POST['no_hp']??'')), 'almt_lngkp'=>trim((string)($_POST['almt_lngkp']??'')), 'password'=>(string)($_POST['password']??'')]; }
    private function validate(array $d,bool $new): ?string
    {
        if ($new && ($d['user_plg']==='' || strlen($d['user_plg'])>50 || preg_match('/[^a-zA-Z0-9_.-]/',$d['user_plg']))) return 'Username wajib diisi (maksimal 50 karakter; gunakan huruf, angka, titik, garis bawah, atau minus).';
        if (!preg_match('/^[0-9]{16}$/',$d['nik'])) return 'NIK harus tepat 16 digit angka.';
        if ($d['nm_lngkp']==='' || mb_strlen($d['nm_lngkp'])>100) return 'Nama lengkap wajib diisi dan maksimal 100 karakter.';
        if ($d['no_hp']==='' || strlen($d['no_hp'])>15 || !preg_match('/^[0-9+(). -]+$/',$d['no_hp'])) return 'Nomor HP tidak valid (maksimal 15 karakter).';
        if ($d['almt_lngkp']==='' || mb_strlen($d['almt_lngkp'])>255) return 'Alamat wajib diisi dan maksimal 255 karakter.';
        if (($new && $d['password']==='') || ($d['password']!=='' && (strlen($d['password'])<8 || strlen($d['password'])>72))) return 'Password wajib diisi saat tambah dan minimal 8 karakter (maksimal 72 byte).';
        return null;
    }
    private function uploadBoth(): array { $a=$this->upload('ft_ktp'); if (!$a['ok']) return ['ok'=>false,'error'=>$a['error']]; $b=$this->upload('ft_sim_a'); if (!$b['ok']) { $this->removeFiles([$a['name']]); return ['ok'=>false,'error'=>$b['error']]; } return ['ok'=>true,'ktp'=>$a['name'],'sim'=>$b['name']]; }
    private function upload(string $field): array
    {
        if (empty($_FILES[$field]) || $_FILES[$field]['error']===UPLOAD_ERR_NO_FILE) return ['ok'=>true,'name'=>null];
        $f=$_FILES[$field]; if ($f['error']!==UPLOAD_ERR_OK || $f['size']<1 || $f['size']>3*1024*1024 || !is_uploaded_file($f['tmp_name'])) return ['ok'=>false,'error'=>'Foto '.$field.' gagal diunggah atau melebihi 3 MB.'];
        $mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']); $types=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
        if (!isset($types[$mime]) || @getimagesize($f['tmp_name'])===false) return ['ok'=>false,'error'=>'Foto KTP dan SIM A harus berupa gambar JPG, PNG, atau WEBP yang valid.'];
        $dir=dirname(APPPATH).'/uploads/penyewa/'; if (!is_dir($dir) && !mkdir($dir,0755,true)) return ['ok'=>false,'error'=>'Folder upload tidak dapat dibuat.'];
        $name=$field.'_'.date('YmdHis').'_'.bin2hex(random_bytes(8)).'.'.$types[$mime];
        if (!move_uploaded_file($f['tmp_name'],$dir.$name)) return ['ok'=>false,'error'=>'File gagal disimpan.']; return ['ok'=>true,'name'=>$name];
    }
    private function removeFiles(array $names): void { $dir=dirname(APPPATH).'/uploads/penyewa/'; foreach($names as $n) if (is_string($n) && preg_match('/^(ft_ktp|ft_sim_a)_\d{14}_[a-f0-9]{16}\.(jpg|png|webp)$/',$n) && is_file($dir.$n)) @unlink($dir.$n); }
    private function render(string $mode,array $item,?string $error=null): void { $this->view('admin/penyewa_view',['username'=>$_SESSION['admin_username'],'penyewa'=>$this->model->getAll(),'flash'=>null,'mode'=>$mode,'item'=>$item,'error'=>$error,'token'=>$this->token()]); }
}
