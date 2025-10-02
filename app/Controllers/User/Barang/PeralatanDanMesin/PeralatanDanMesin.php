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
    return redirect()->to('user/barang/peralatandanmesin/alatbengkelukur/kelompokalatbengkelukur');
}

public function alatbengkelbermesin()
{
    return redirect()->to('user/barang/peralatandanmesin/alatbengkelukur/kelompokalatbengkelukur/ALAT BENGKEL BERMESIN');
}

public function alatbengkeltakbermesin()
{
    return redirect()->to('user/barang/peralatandanmesin/alatbengkelukur/kelompokalatbengkelukur/ALAT BENGKEL TAK BERMESIN');
}

public function alatukur()
{
    return redirect()->to('user/barang/peralatandanmesin/alatbengkelukur/kelompokalatbengkelukur/ALAT UKUR');
}
    // ================== 3.04 ALAT PERTANIAN ==================
public function alatpertanian()
{
    return redirect()->to('user/barang/peralatandanmesin/alatpertanian/kelompokalatpertanian');
}

public function alatpengolahan()
{
    return redirect()->to('user/barang/peralatandanmesin/alatpertanian/kelompokalatpertanian/ALAT PENGOLAHAN');
}

    // ================== 3.05 ALAT KANTOR & RUMAH TANGGA ==================
public function alatkantorrt()
{
    return redirect()->to('user/barang/peralatandanmesin/alatkantorrt/kelompokalatkantorrt');
}

public function alatkantor()
{
    return redirect()->to('user/barang/peralatandanmesin/alatkantorrt/kelompokalatkantorrt/ALAT KANTOR');
}

public function alatrumahTangga()
{
    return redirect()->to('user/barang/peralatandanmesin/alatkantorrt/kelompokalatkantorrt/ALAT RUMAH TANGGA');
}

    // ================== 3.06 ALAT STUDIO, KOMUNIKASI DAN PEMANCAR ==================
    public function alatstudiokomunikasi()
{
    return redirect()->to('user/barang/peralatandanmesin/alatstudiokomunikasipemancar/kelompokalatstudiokomunikasipemancar');
}
public function alatstudiokomunikasipemancar()
{
    return redirect()->to('user/barang/peralatandanmesin/alatstudiokomunikasipemancar/kelompokalatstudiokomunikasipemancar');
}

public function alatstudio()
{
    return redirect()->to('user/barang/peralatandanmesin/alatstudiokomunikasipemancar/kelompokalatstudiokomunikasipemancar/ALAT STUDIO');
}

public function alatkomunikasi()
{
    return redirect()->to('user/barang/peralatandanmesin/alatstudiokomunikasipemancar/kelompokalatstudiokomunikasipemancar/ALAT KOMUNIKASI');
}

public function peralatanpemancar()
{
    return redirect()->to('user/barang/peralatandanmesin/alatstudiokomunikasipemancar/kelompokalatstudiokomunikasipemancar/PERALATAN PEMANCAR');
}

public function peralatankomunikasiNavigasi()
{
    return redirect()->to('user/barang/peralatandanmesin/alatstudiokomunikasipemancar/kelompokalatstudiokomunikasipemancar/PERALATAN KOMUNIKASI NAVIGASI');
}

// ================== 3.07 ALAT KEDOKTERAN DAN KESEHATAN ==================
    public function alatkedokterankesehatan()
    {
        return redirect()->to('user/barang/peralatandanmesin/alatkedokterankesehatan/kelompokalatkedokterankesehatan');
    }

    public function alatkedokteran()
    {
        return redirect()->to('user/barang/peralatandanmesin/alatkedokterankesehatan/kelompokalatkedokterankesehatan/ALAT KEDOKTERAN');
    }

    public function alatkesehatanumum()
    {
        return redirect()->to('user/barang/peralatandanmesin/alatkedokterankesehatan/kelompokalatkedokterankesehatan/ALAT KESEHATAN UMUM');
    }
    // ================== 3.08 ALAT LABORATORIUM ==================
    public function alatlaboratorium()
    {
        return redirect()->to('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium');
    }

    public function unitalatlaboratorium()
    {
        return redirect()->to('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium/UNIT ALAT LABORATORIUM');
    }

    public function unitalatlabkimiapelajar()
    {
        return redirect()->to('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium/UNIT ALAT LABORATORIUM KIMIA PELAJAR');
    }

    public function alatlabfisikanuklir()
    {
        return redirect()->to('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium/ALAT LABORATORIUM FISIKA NUKLIR/ELEKTRONIKA');
    }

    public function alatproteksiRadiasi()
    {
        return redirect()->to('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium/ALAT PROTEKSI RADIASI/PROTEKSI LINGKUNGAN');
    }

    public function radiationApplication()
    {
        return redirect()->to('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium/RADIATION APPLICATION & NON DESTRUCTIVE TESTING LABORATORY');
    }

    public function alatlablingkunganhidup()
    {
        return redirect()->to('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium/ALAT LABORATORIUM LINGKUNGAN HIDUP');
    }

    public function peralatanlabhydrodinamica()
    {
        return redirect()->to('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium/PERALATAN LABORATORIUM HYDRODINAMICA');
    }

    public function alatlabstandarisasikalibrasi()
    {
        return redirect()->to('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium/ALAT LABORATORIUM STANDARISASI KALIBRASI & INSTRUMENTASI');
    }

    // ================== 3.09 ALAT PERSENJATAAN ==================
    public function alatpersenjataan()
    {
        return redirect()->to('user/barang/peralatandanmesin/alatpersenjataan/kelompokalatpersenjataan');
    }

    public function senjataapi()
    {
        return redirect()->to('user/barang/peralatandanmesin/alatpersenjataan/kelompokalatpersenjataan/SENJATA API');
    }

    public function persenjataannonsenjataapi()
    {
        return redirect()->to('user/barang/peralatandanmesin/alatpersenjataan/kelompokalatpersenjataan/PERSENJATAAN NON SENJATA API');
    }

    public function senjatasinar()
    {
        return redirect()->to('user/barang/peralatandanmesin/alatpersenjataan/kelompokalatpersenjataan/SENJATA SINAR');
    }

    public function alatkhususkepolisian()
    {
        return redirect()->to('user/barang/peralatandanmesin/alatpersenjataan/kelompokalatpersenjataan/ALAT KHUSUS KEPOLISIAN');
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
    return redirect()->to('user/barang/peralatandanmesin/alateksplorasi/kelompokalateksplorasi');
}

public function alateksplorasitopografi()
{
    return redirect()->to('user/barang/peralatandanmesin/alateksplorasi/kelompokalateksplorasi/ALAT EKSPLORASI TOPOGRAFI');
}

public function alateksplorasigeofisika()
{
    return redirect()->to('user/barang/peralatandanmesin/alateksplorasi/kelompokalateksplorasi/ALAT EKSPLORASI GEOFISIKA');
}

// ================== 3.12 ALAT PENGEBORAN ==================
public function alatpengeboran()
{
    return redirect()->to('user/barang/peralatandanmesin/alatpengeboran/kelompokalatpengeboran');
}

public function alatpengeboranmesin()
{
    return redirect()->to('user/barang/peralatandanmesin/alatpengeboran/kelompokalatpengeboran/ALAT PENGEBORAN MESIN');
}

public function alatpengeborannonmesin()
{
    return redirect()->to('user/barang/peralatandanmesin/alatpengeboran/kelompokalatpengeboran/ALAT PENGEBORAN NON MESIN');
}
    // ================== 3.13 ALAT PRODUKSI, PENGOLAHAN DAN PEMURNIAN ==================
public function alatproduksipengolahan()
{
    return redirect()->to('user/barang/peralatandanmesin/alatproduksipengolahan/kelompokalatproduksipengolahan');
}

public function sumur()
{
    return redirect()->to('user/barang/peralatandanmesin/alatproduksipengolahan/kelompokalatproduksipengolahan/SUMUR');
}

public function produksi()
{
    return redirect()->to('user/barang/peralatandanmesin/alatproduksipengolahan/kelompokalatproduksipengolahan/PRODUKSI');
}

public function pengolahandanpemurnian()
{
    return redirect()->to('user/barang/peralatandanmesin/alatproduksipengolahan/kelompokalatproduksipengolahan/PENGOLAHAN DAN PEMURNIAN');
}

    // ================== 3.14 ALAT BANTU EKSPLORASI ==================
public function alatbantueksplorasi()
{
    return redirect()->to('user/barang/peralatandanmesin/alatbantueksplorasi/kelompokalatbantueksplorasi');
}

public function alatbantueksplorasi_detail()
{
    return redirect()->to('user/barang/peralatandanmesin/alatbantueksplorasi/kelompokalatbantueksplorasi/ALAT BANTU EKSPLORASI');
}

public function alatbantuproduksi()
{
    return redirect()->to('user/barang/peralatandanmesin/alatbantueksplorasi/kelompokalatbantueksplorasi/ALAT BANTU PRODUKSI');
}

    // ================== 3.15 ALAT KESELAMATAN KERJA ==================
public function alatkeselamatankerja()
{
    return redirect()->to('user/barang/peralatandanmesin/alatkeselamatankerja/kelompokalatkeselamatankerja');
}

public function alatdeteksi()
{
    return redirect()->to('user/barang/peralatandanmesin/alatkeselamatankerja/kelompokalatkeselamatankerja/ALAT DETEKSI');
}

public function alatpelindung()
{
    return redirect()->to('user/barang/peralatandanmesin/alatkeselamatankerja/kelompokalatkeselamatankerja/ALAT PELINDUNG');
}

public function alatsar()
{
    return redirect()->to('user/barang/peralatandanmesin/alatkeselamatankerja/kelompokalatkeselamatankerja/ALAT SAR');
}

public function alatkerjapenerbangan()
{
    return redirect()->to('user/barang/peralatandanmesin/alatkeselamatankerja/kelompokalatkeselamatankerja/ALAT KERJA PENERBANGAN');
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
    return redirect()->to('user/barang/peralatandanmesin/ramburambu/kelompokramburambu');
}

public function rambulalulintasdarat()
{
    return redirect()->to('user/barang/peralatandanmesin/ramburambu/kelompokramburambu/RAMBU-RAMBU LALU LINTAS DARAT');
}

public function rambulalulintasudara()
{
    return redirect()->to('user/barang/peralatandanmesin/ramburambu/kelompokramburambu/RAMBU-RAMBU LALU LINTAS UDARA');
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