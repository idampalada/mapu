<?php

namespace App\Controllers\User\Barang\PeralatanDanMesin;

use App\Controllers\BaseController;

class PeralatanDanMesin extends BaseController
{
    // Method untuk halaman utama Peralatan dan Mesin
    public function index()
    {
        return view('user/barang/peralatandanmesin/peralatandanmesin');
    }

    // ================== 3.01 ALAT BESAR ==================
    public function alatbesar()
    {
        return view('user/barang/peralatandanmesin/alatbesar/kelompokalatbesar');
    }


    // ================== 3.02 ALAT ANGKUTAN ==================
    public function alatangkutan()
{
    return redirect()->to('user/barang/peralatandanmesin/alatangkutan/kelompokalatangkutan');
}

public function alatangkutandaratbermotor()
{
    return redirect()->to('user/barang/peralatandanmesin/alatangkutan/kelompokalatangkutan/ALAT ANGKUTAN DARAT BERMOTOR');
}

public function alatangkutandarattakbermotor()
{
    return redirect()->to('user/barang/peralatandanmesin/alatangkutan/kelompokalatangkutan/ALAT ANGKUTAN DARAT TAK BERMOTOR');
}

public function alatangkutanapungbermotor()
{
    return redirect()->to('user/barang/peralatandanmesin/alatangkutan/kelompokalatangkutan/ALAT ANGKUTAN APUNG BERMOTOR');
}

public function alatangkutanapungtakbermotor()
{
    return redirect()->to('user/barang/peralatandanmesin/alatangkutan/kelompokalatangkutan/ALAT ANGKUTAN APUNG TAK BERMOTOR');
}

public function alatangkutanbermotorudara()
{
    return redirect()->to('user/barang/peralatandanmesin/alatangkutan/kelompokalatangkutan/ALAT ANGKUTAN BERMOTOR UDARA');
}

    // ================== 3.03 ALAT BENGKEL DAN ALAT UKUR ==================
    public function alatbengkelukur()
    {
        return view('user/barang/peralatandanmesin/alatbengkelukur');
    }

    public function alatbengkelbermesin()
    {
        return view('user/barang/peralatandanmesin/alatbengkelukur/bengkelbermesin');
    }

    public function alatbengkeltakbermesin()
    {
        return view('user/barang/peralatandanmesin/alatbengkelukur/bengkeltakbermesin');
    }

    public function alatukur()
    {
        return view('user/barang/peralatandanmesin/alatbengkelukur/alatukur');
    }

    // ================== 3.04 ALAT PERTANIAN ==================
    public function alatpertanian()
    {
        return view('user/barang/peralatandanmesin/alatpertanian');
    }

    public function alatpengolahan()
    {
        return view('user/barang/peralatandanmesin/alatpertanian/alatpengolahan');
    }

    // ================== 3.05 ALAT KANTOR & RUMAH TANGGA ==================
    public function alatkantorrt()
    {
        return view('user/barang/peralatandanmesin/alatkantorrt');
    }

    public function alatkantor()
    {
        return view('user/barang/peralatandanmesin/alatkantorrt/alatkantor');
    }

    public function alatrumahTangga()
    {
        return view('user/barang/peralatandanmesin/alatkantorrt/alatrumahTangga');
    }

    // ================== 3.06 ALAT STUDIO, KOMUNIKASI DAN PEMANCAR ==================
    public function alatstudiokomunikasi()
    {
        return view('user/barang/peralatandanmesin/alatstudiokomunikasi');
    }

    public function alatstudio()
    {
        return view('user/barang/peralatandanmesin/alatstudiokomunikasi/alatstudio');
    }

    public function alatkomunikasi()
    {
        return view('user/barang/peralatandanmesin/alatstudiokomunikasi/alatkomunikasi');
    }

    public function peralatanpemancar()
    {
        return view('user/barang/peralatandanmesin/alatstudiokomunikasi/peralatanpemancar');
    }

    public function peralatankomunikasiNavigasi()
    {
        return view('user/barang/peralatandanmesin/alatstudiokomunikasi/peralatankomunikasiNavigasi');
    }

    // ================== 3.07 ALAT KEDOKTERAN DAN KESEHATAN ==================
    public function alatkedokterankesehatan()
    {
        return view('user/barang/peralatandanmesin/alatkedokterankesehatan');
    }

    public function alatkedokteran()
    {
        return view('user/barang/peralatandanmesin/alatkedokterankesehatan/alatkedokteran');
    }

    public function alatkesehatanumum()
    {
        return view('user/barang/peralatandanmesin/alatkedokterankesehatan/alatkesehatanumum');
    }

    // ================== 3.08 ALAT LABORATORIUM ==================
    public function alatlaboratorium()
    {
        return view('user/barang/peralatandanmesin/alatlaboratorium');
    }

    public function unitalatlaboratorium()
    {
        return view('user/barang/peralatandanmesin/alatlaboratorium/unitalatlaboratorium');
    }

    public function unitalatlabkimiapelajar()
    {
        return view('user/barang/peralatandanmesin/alatlaboratorium/unitalatlabkimiapelajar');
    }

    public function alatlabfisikanuklir()
    {
        return view('user/barang/peralatandanmesin/alatlaboratorium/alatlabfisikanuklir');
    }

    public function alatproteksiRadiasi()
    {
        return view('user/barang/peralatandanmesin/alatlaboratorium/alatproteksiRadiasi');
    }

    public function radiationApplication()
    {
        return view('user/barang/peralatandanmesin/alatlaboratorium/radiationApplication');
    }

    public function alatlablingkunganhidup()
    {
        return view('user/barang/peralatandanmesin/alatlaboratorium/alatlablingkunganhidup');
    }

    public function peralatanlabhydrodinamica()
    {
        return view('user/barang/peralatandanmesin/alatlaboratorium/peralatanlabhydrodinamica');
    }

    public function alatlabstandarisasikalibrasi()
    {
        return view('user/barang/peralatandanmesin/alatlaboratorium/alatlabstandarisasikalibrasi');
    }

    // ================== 3.09 ALAT PERSENJATAAN ==================
    public function alatpersenjataan()
    {
        return view('user/barang/peralatandanmesin/alatpersenjataan');
    }

    public function senjataapi()
    {
        return view('user/barang/peralatandanmesin/alatpersenjataan/senjataapi');
    }

    public function persenjataannonsenjataapi()
    {
        return view('user/barang/peralatandanmesin/alatpersenjataan/persenjataannonsenjataapi');
    }

    public function senjatasinar()
    {
        return view('user/barang/peralatandanmesin/alatpersenjataan/senjatasinar');
    }

    public function alatkhususkepolisian()
    {
        return view('user/barang/peralatandanmesin/alatpersenjataan/alatkhususkepolisian');
    }

    // ================== 3.10 KOMPUTER ==================
    public function komputer()
{
    return redirect()->to('user/barang/peralatandanmesin/komputer/kelompokkomputer');
}

public function komputerunit()
{
    return redirect()->to('user/barang/peralatandanmesin/komputer/kelompokkomputer/KOMPUTER UNIT');
}

public function peralatankomputer()
{
    return redirect()->to('user/barang/peralatandanmesin/komputer/kelompokkomputer/PERALATAN KOMPUTER');
}

    // ================== 3.11 ALAT EKSPLORASI ==================
    public function alateksplorasi()
    {
        return view('user/barang/peralatandanmesin/alateksplorasi');
    }

    public function alateksplorasitopografi()
    {
        return view('user/barang/peralatandanmesin/alateksplorasi/alateksplorasitopografi');
    }

    public function alateksplorasigeofisika()
    {
        return view('user/barang/peralatandanmesin/alateksplorasi/alateksplorasigeofisika');
    }

    // ================== 3.12 ALAT PENGEBORAN ==================
    public function alatpengeboran()
    {
        return view('user/barang/peralatandanmesin/alatpengeboran');
    }

    public function alatpengeboran_mesin()
    {
        return view('user/barang/peralatandanmesin/alatpengeboran/alatpengeboran_mesin');
    }

    public function alatpengeboran_nonmesin()
    {
        return view('user/barang/peralatandanmesin/alatpengeboran/alatpengeboran_nonmesin');
    }

    // ================== 3.13 ALAT PRODUKSI, PENGOLAHAN DAN PEMURNIAN ==================
    public function alatproduksipengolahan()
    {
        return view('user/barang/peralatandanmesin/alatproduksipengolahan');
    }

    public function sumur()
    {
        return view('user/barang/peralatandanmesin/alatproduksipengolahan/sumur');
    }

    public function produksi()
    {
        return view('user/barang/peralatandanmesin/alatproduksipengolahan/produksi');
    }

    public function pengolahanpemurnian()
    {
        return view('user/barang/peralatandanmesin/alatproduksipengolahan/pengolahanpemurnian');
    }

    // ================== 3.14 ALAT BANTU EKSPLORASI ==================
    public function alatbantueksplorasi()
    {
        return view('user/barang/peralatandanmesin/alatbantueksplorasi');
    }

    public function alatbantueksplorasi_detail()
    {
        return view('user/barang/peralatandanmesin/alatbantueksplorasi/alatbantueksplorasi_detail');
    }

    public function alatbantuproduksi()
    {
        return view('user/barang/peralatandanmesin/alatbantueksplorasi/alatbantuproduksi');
    }

    // ================== 3.15 ALAT KESELAMATAN KERJA ==================
    public function alatkeselamatankerja()
    {
        return view('user/barang/peralatandanmesin/alatkeselamatankerja');
    }

    public function alatdeteksi()
    {
        return view('user/barang/peralatandanmesin/alatkeselamatankerja/alatdeteksi');
    }

    public function alatpelindung()
    {
        return view('user/barang/peralatandanmesin/alatkeselamatankerja/alatpelindung');
    }

    public function alatsar()
    {
        return view('user/barang/peralatandanmesin/alatkeselamatankerja/alatsar');
    }

    public function alatkerjaPenerbangan()
    {
        return view('user/barang/peralatandanmesin/alatkeselamatankerja/alatkerjaPenerbangan');
    }

    // ================== 3.16 ALAT PERAGA ==================
    public function alatperaga()
    {
        return view('user/barang/peralatandanmesin/alatperaga');
    }

    public function alatperagapelatihanpercontohan()
    {
        return view('user/barang/peralatandanmesin/alatperaga/alatperagapelatihanpercontohan');
    }

    // ================== 3.17 PERALATAN PROFESI/PRODUKSI ==================
    public function peralatanprofesiproduksi()
    {
        return view('user/barang/peralatandanmesin/peralatanprofesiproduksi');
    }

    public function unitperalatanprosesproduksi()
    {
        return view('user/barang/peralatandanmesin/peralatanprofesiproduksi/unitperalatanprosesproduksi');
    }

    // ================== 3.18 RAMBU-RAMBU ==================
    public function ramburambu()
    {
        return view('user/barang/peralatandanmesin/ramburambu');
    }

    public function rambulalulintas_darat()
    {
        return view('user/barang/peralatandanmesin/ramburambu/rambulalulintas_darat');
    }

    public function rambulalulintas_udara()
    {
        return view('user/barang/peralatandanmesin/ramburambu/rambulalulintas_udara');
    }

    // ================== 3.19 PERALATAN OLAHRAGA ==================
    public function peralatanolahraga()
    {
        return view('user/barang/peralatandanmesin/peralatanolahraga');
    }

    public function peralatanolahraga_detail()
    {
        return view('user/barang/peralatandanmesin/peralatanolahraga/peralatanolahraga_detail');
    }
}