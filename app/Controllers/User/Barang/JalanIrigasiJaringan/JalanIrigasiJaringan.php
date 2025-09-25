<?php

namespace App\Controllers\User\Barang\JalanIrigasiJaringan;

use App\Controllers\BaseController;

class JalanIrigasiJaringan extends BaseController
{
    // Method untuk halaman utama Jalan, Irigasi dan Jaringan
    public function index()
    {
        return view('user/barang/jalanirigasijaringan/jalanirigasijaringan');
    }

    // ================== 5.01 JALAN DAN JEMBATAN ==================
    public function jalandanjembatan()
    {
        // Langsung redirect ke kelompok jalan dan jembatan tanpa view terpisah
        return redirect()->to('user/barang/jalanirigasijaringan/jalandanjembatan/kelompokjalandanjembatan');
    }

    // Redirect methods untuk backward compatibility
    public function jalan()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/jalandanjembatan/kelompokjalandanjembatan/JALAN');
    }

    public function jembatan()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/jalandanjembatan/kelompokjalandanjembatan/JEMBATAN');
    }

    // ================== 5.02 BANGUNAN AIR ==================
    public function bangunanair()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/bangunanair/kelompokbangunanair');
    }

    public function bangunanairrigasi()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/bangunanair/kelompokbangunanair/BANGUNAN AIR IRIGASI');
    }

    public function bangunanpengairanpasangsurut()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/bangunanair/kelompokbangunanair/BANGUNAN PENGAIRAN PASANG SURUT');
    }

    public function bangunanpengembanganrawa()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/bangunanair/kelompokbangunanair/BANGUNAN PENGEMBANGAN RAWA DAN POLDER');
    }

    public function bangunanpengamansungai()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/bangunanair/kelompokbangunanair/BANGUNAN PENGAMAN SUNGAI/PANTAI & PENANGGULAN BENCANA ALAM');
    }

    public function bangunanpengembangansumberair()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/bangunanair/kelompokbangunanair/BANGUNAN PENGEMBANGAN SUMBER AIR DAN AIR TANAH');
    }

    public function bangunanairbersih()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/bangunanair/kelompokbangunanair/BANGUNAN AIR BERSIH/AIR BAKU');
    }

    public function bangunanairkotor()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/bangunanair/kelompokbangunanair/BANGUNAN AIR KOTOR');
    }

    // ================== 5.03 INSTALASI ==================
    public function instalasi()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi');
    }

    public function instalasiairbersih()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi/INSTALASI AIR BERSIH/AIR BAKU');
    }

    public function instalasiairkotor()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi/INSTALASI AIR KOTOR');
    }

    public function instalasipengolahansampah()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi/INSTALASI PENGOLAHAN SAMPAH');
    }

    public function instalasipengolahanbahan()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi/INSTALASI PENGOLAHAN BAHAN BANGUNAN');
    }

    public function instalasipembangkitlistrik()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi/INSTALASI PEMBANGKIT LISTRIK');
    }

    public function instalasigardulistrik()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi/INSTALASI GARDU LISTRIK');
    }

    public function instalasipertahanan()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi/INSTALASI PERTAHANAN');
    }

    public function instalasigas()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi/INSTALASI GAS');
    }

    public function instalasipengaman()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi/INSTALASI PENGAMAN');
    }

    public function instalasiliain()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi/INSTALASI LAIN');
    }

    // ================== 5.04 JARINGAN ==================
    public function jaringan()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/jaringan/kelompokjaringan');
    }

    public function jaringanair()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/jaringan/kelompokjaringan/JARINGAN AIR MINUM');
    }

    public function jaringanlistrik()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/jaringan/kelompokjaringan/JARINGAN LISTRIK');
    }

    public function jaringantelepon()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/jaringan/kelompokjaringan/JARINGAN TELEPON');
    }

    public function jaringangas()
    {
        return redirect()->to('user/barang/jalanirigasijaringan/jaringan/kelompokjaringan/JARINGAN GAS');
    }
}