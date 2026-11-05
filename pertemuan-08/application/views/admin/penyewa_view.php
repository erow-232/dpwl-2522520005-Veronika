<?php
require APPPATH . '/views/templates/header.php'; require APPPATH . '/views/templates/sidebar.php';
$penyewa=$penyewa??[]; $item=$item??[]; $mode=$mode??null; $isForm=in_array($mode,['tambah','ubah'],true); $edit=$mode==='ubah';
$esc=static fn($v)=>htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');
$action=$edit?site_url('admin/penyewa/ubah/'.rawurlencode((string)($item['user_plg']??''))):site_url('admin/penyewa/tambah');
?>
<div class="content-wrapper">
<section class="content-header"><h1>Manajemen Penyewa <small>Kelola data penyewa</small></h1><ol class="breadcrumb"><li><a href="<?= $esc(site_url('admin')) ?>">Dashboard</a></li><li class="active">Manajemen Penyewa</li></ol></section>
<section class="content">
<?php if(!empty($flash)): ?><div class="alert <?= ($flash['type']??'')==='success'?'alert-success':'alert-danger' ?> alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><?= $esc($flash['message']??'') ?></div><?php endif; ?>
<?php if($error): ?><div class="alert alert-danger"><?= $esc($error) ?></div><?php endif; ?>
<div class="box"><div class="box-header"><h3 class="box-title">Daftar Data Penyewa</h3><div class="box-tools"><a class="btn btn-primary btn-sm" href="<?= $esc(site_url('admin/penyewa/tambah')) ?>"><i class="fa fa-plus"></i> Tambah Penyewa</a></div></div>
<div class="box-body table-responsive"><table class="table table-bordered table-striped js-data-table"><thead><tr><th>No</th><th>Username</th><th>NIK</th><th>Nama Lengkap</th><th>No. HP</th><th>Alamat</th><th>Foto KTP</th><th>Foto SIM A</th><th class="no-sort">Aksi</th></tr></thead><tbody>
<?php foreach($penyewa as $i=>$p): ?><tr><td><?= $i+1 ?></td><td><?= $esc($p['user_plg']) ?></td><td><?= $esc($p['nik']) ?></td><td><?= $esc($p['nm_lngkp']) ?></td><td><?= $esc($p['no_hp']) ?></td><td><?= $esc($p['almt_lngkp']) ?></td>
<?php foreach(['ft_ktp'=>'KTP','ft_sim_a'=>'SIM A'] as $field=>$label): ?><td><?php if(!empty($p[$field])): ?><a target="_blank" rel="noopener" href="<?= $esc(base_url('uploads/penyewa/'.rawurlencode($p[$field]))) ?>">Lihat <?= $label ?></a><?php else: ?><span class="text-muted">Belum ada</span><?php endif; ?></td><?php endforeach; ?>
<td><a class="btn btn-warning btn-xs" href="<?= $esc(site_url('admin/penyewa/ubah/'.rawurlencode($p['user_plg']))) ?>"><i class="fa fa-edit"></i> Ubah</a> <form method="post" action="<?= $esc(site_url('admin/penyewa/hapus/'.rawurlencode($p['user_plg']))) ?>" style="display:inline" onsubmit="return confirm('Hapus data penyewa ini?');"><input type="hidden" name="_token" value="<?= $esc($token) ?>"><button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash"></i> Hapus</button></form></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<?php if($isForm): ?><div class="box box-primary"><div class="box-header with-border"><h3 class="box-title"><?= $edit?'Ubah':'Tambah' ?> Data Penyewa</h3></div><form method="post" enctype="multipart/form-data" action="<?= $esc($action) ?>"><input type="hidden" name="_token" value="<?= $esc($token) ?>"><div class="box-body">
<div class="form-group"><label>Username</label><input class="form-control" name="user_plg" maxlength="50" required value="<?= $esc($item['user_plg']??'') ?>" <?= $edit?'readonly':'' ?>></div>
<div class="form-group"><label>NIK (16 digit)</label><input class="form-control" name="nik" maxlength="16" pattern="[0-9]{16}" required value="<?= $esc($item['nik']??'') ?>"></div>
<div class="form-group"><label>Nama Lengkap</label><input class="form-control" name="nm_lngkp" maxlength="100" required value="<?= $esc($item['nm_lngkp']??'') ?>"></div>
<div class="form-group"><label>Nomor HP</label><input class="form-control" name="no_hp" maxlength="15" required value="<?= $esc($item['no_hp']??'') ?>"></div>
<div class="form-group"><label>Alamat Lengkap</label><textarea class="form-control" name="almt_lngkp" maxlength="255" required><?= $esc($item['almt_lngkp']??'') ?></textarea></div>
<div class="form-group"><label>Password <?= $edit?'(kosongkan jika tidak diubah)':'' ?></label><input class="form-control" type="password" name="password" minlength="8" maxlength="72" <?= $edit?'':'required' ?> autocomplete="new-password"></div>
<div class="form-group"><label>Foto KTP (JPG/PNG/WEBP, maks. 3 MB)</label><input type="file" name="ft_ktp" accept="image/jpeg,image/png,image/webp"><?php if(!empty($item['ft_ktp'])): ?><p class="help-block">Foto KTP saat ini: <?= $esc($item['ft_ktp']) ?></p><?php endif; ?></div>
<div class="form-group"><label>Foto SIM A (JPG/PNG/WEBP, maks. 3 MB)</label><input type="file" name="ft_sim_a" accept="image/jpeg,image/png,image/webp"><?php if(!empty($item['ft_sim_a'])): ?><p class="help-block">Foto SIM A saat ini: <?= $esc($item['ft_sim_a']) ?></p><?php endif; ?></div>
<p class="text-muted">Foto lama akan tetap digunakan jika tidak memilih file baru. Password disimpan dalam bentuk hash.</p></div><div class="box-footer"><button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Simpan</button> <a class="btn btn-default" href="<?= $esc(site_url('admin/penyewa')) ?>">Batal</a></div></form></div><?php endif; ?>
</section></div>
<?php require APPPATH . '/views/templates/footer.php'; ?>
