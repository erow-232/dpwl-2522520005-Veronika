<?php
/** Model CRUD data penyewa untuk area admin. */
class Manajemen_penyewa_model
{
    private mysqli $db;
    public function __construct() { global $db; $this->db = $db; }
    public function getAll(): array
    {
        $sql = "SELECT user_plg, nik, nm_lngkp, no_hp, almt_lngkp, ft_ktp, ft_sim_a FROM t_penyewa ORDER BY nm_lngkp ASC";
        $result = $this->db->query($sql);
        if (!$result) throw new RuntimeException('Data penyewa gagal diambil.');
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function find(string $user): ?array
    {
        $stmt = $this->db->prepare('SELECT user_plg, sandi, nik, nm_lngkp, no_hp, almt_lngkp, ft_ktp, ft_sim_a FROM t_penyewa WHERE user_plg = ? LIMIT 1');
        if (!$stmt) throw new RuntimeException('Query data penyewa gagal disiapkan.');
        $stmt->bind_param('s', $user); $stmt->execute(); $row = $stmt->get_result()->fetch_assoc(); $stmt->close();
        return $row ?: null;
    }
    public function create(array $d): bool
    {
        $stmt = $this->db->prepare('INSERT INTO t_penyewa (user_plg, sandi, nik, nm_lngkp, no_hp, almt_lngkp, ft_ktp, ft_sim_a) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        if (!$stmt) throw new RuntimeException('Query tambah penyewa gagal disiapkan.');
        $stmt->bind_param('ssssssss', $d['user_plg'], $d['sandi'], $d['nik'], $d['nm_lngkp'], $d['no_hp'], $d['almt_lngkp'], $d['ft_ktp'], $d['ft_sim_a']);
        $ok = $stmt->execute(); $stmt->close(); return $ok;
    }
    public function update(string $key, array $d): bool
    {
        if ($d['sandi'] !== null) {
            $stmt = $this->db->prepare('UPDATE t_penyewa SET nik=?, nm_lngkp=?, no_hp=?, almt_lngkp=?, ft_ktp=?, ft_sim_a=?, sandi=? WHERE user_plg=?');
            if (!$stmt) throw new RuntimeException('Query ubah penyewa gagal disiapkan.');
            $stmt->bind_param('ssssssss', $d['nik'], $d['nm_lngkp'], $d['no_hp'], $d['almt_lngkp'], $d['ft_ktp'], $d['ft_sim_a'], $d['sandi'], $key);
        } else {
            $stmt = $this->db->prepare('UPDATE t_penyewa SET nik=?, nm_lngkp=?, no_hp=?, almt_lngkp=?, ft_ktp=?, ft_sim_a=? WHERE user_plg=?');
            if (!$stmt) throw new RuntimeException('Query ubah penyewa gagal disiapkan.');
            $stmt->bind_param('sssssss', $d['nik'], $d['nm_lngkp'], $d['no_hp'], $d['almt_lngkp'], $d['ft_ktp'], $d['ft_sim_a'], $key);
        }
        $ok = $stmt->execute(); $stmt->close(); return $ok;
    }
    public function delete(string $user): bool
    {
        $stmt = $this->db->prepare('DELETE FROM t_penyewa WHERE user_plg=?');
        if (!$stmt) throw new RuntimeException('Query hapus penyewa gagal disiapkan.');
        $stmt->bind_param('s', $user); $ok = $stmt->execute(); $stmt->close(); return $ok;
    }
}
