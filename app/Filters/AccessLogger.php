<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\I18n\Time;

class AccessLogger implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('url');

        $ip = $request->getIPAddress();
        $agent = service('request')->getUserAgent();
        $url = current_url();
        $time = date('Y-m-d H:i:s');

        // Ambil lokasi dari IP (gunakan API ip-api.com)
        $lokasi = @file_get_contents("http://ip-api.com/json/$ip");
        $lokasi = $lokasi ? json_decode($lokasi, true) : null;

        // Simpan ke database
        $db = \Config\Database::connect();
        $db->table('log_akses')->insert([
            'ip' => $ip,
            'lokasi' => json_encode($lokasi),
            'url' => $url,
            'user_agent' => $agent->getAgentString(),
            'created_at' => $time
        ]);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu aksi setelah
    }
}
