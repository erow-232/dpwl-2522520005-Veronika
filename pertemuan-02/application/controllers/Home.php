<?php
class Home extends Controller
{
    public function index(): void
    {
        $data = [
            'judul' => 'Fondasi MVC DPWL',
            'pesan' => 'Request telah melewati front controller, Router, Controller, dan View.'
        ];
        $this->view('home/index', $data);
    }

    public function info(string $topik = 'mvc'): void
    {
        $this->view('home/info', ['topik' => $topik]);
    }

    public function mahasiswa(string $nim = '2522520005'): void
    {
        $data = [
            'title' => 'Detail Mahasiswa',
            'nim'   => $nim,
            'nama'  => 'Veronika Helena Patricia',
            'kelas' => 'SI3A'
        ];
        $this->view('home/mahasiswa', $data);
    }
}