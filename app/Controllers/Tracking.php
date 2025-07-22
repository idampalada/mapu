<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AsetModel;

class Tracking extends Controller
{
    public function getTracking()
    {
        $nopol = $this->request->getGet('nopol');

        log_message('error', '🔍 REQUEST DITERIMA DENGAN NOPOL: ' . $nopol);
        if (!$nopol) {
            log_message('error', '❌ Tidak ada nopol yang dikirim.');
            return $this->response->setJSON(['error' => 'NoPolisi tidak dikirim'])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $asetModel = new AsetModel();
        $kendaraan = $asetModel
            ->where('REPLACE(UPPER(no_polisi), \' \', \'\')', strtoupper(str_replace(' ', '', $nopol)))
            ->first();

        if (!$kendaraan) {
            log_message('error', '❌ Kendaraan tidak ditemukan di database lokal: ' . $nopol);
            return $this->response->setJSON(['error' => 'Data tidak ditemukan di database lokal'])->setStatusCode(404);
        }

        log_message('error', '✅ Kendaraan ditemukan di database: ' . json_encode($kendaraan));

        try {
            $client = \Config\Services::curlrequest();

            $loginResponse = $client->post('https://apigps.ndpteknologi.com/auth/login', [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode([
                    'email' => 'admin@ndpteknologi.com',
                    'password' => 'pwlan123',
                ])
            ]);

            if ($loginResponse->getStatusCode() !== 200) {
                log_message('error', '❌ Login API gagal. Status: ' . $loginResponse->getStatusCode());
                return $this->response->setJSON(['error' => 'Gagal login ke API GPS'])->setStatusCode(500);
            }

            $loginData = json_decode($loginResponse->getBody(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', '❌ JSON Login Response tidak valid: ' . json_last_error_msg());
                return $this->response->setJSON(['error' => 'Respons login tidak bisa dibaca'])->setStatusCode(500);
            }

            log_message('error', '✅ Response Login API: ' . json_encode($loginData));

            if (!isset($loginData['accessToken'])) {
                log_message('error', '❌ Token tidak ditemukan dalam response login API.');
                return $this->response->setJSON(['error' => 'Token API tidak ditemukan'])->setStatusCode(500);
            }

            $token = $loginData['accessToken'];

            $deviceResponse = $client->get('https://apigps.ndpteknologi.com/devices', [
                'headers' => ['Authorization' => 'Bearer ' . $token]
            ]);

            $devices = json_decode($deviceResponse->getBody(), true);

            if (!is_array($devices)) {
                log_message('error', '❌ Respons device bukan array atau tidak bisa diparse.');
                return $this->response->setJSON(['error' => 'Data perangkat tidak valid'])->setStatusCode(500);
            }

            log_message('error', '✅ Jumlah data device dari API: ' . count($devices));

            $apiMatch = null;
            foreach ($devices as $item) {
                $apiNopol = substr(preg_replace('/[^A-Z0-9]/i', '', strtoupper($item['nopol'])), 0, 8);
                $dbNopol  = substr(preg_replace('/[^A-Z0-9]/i', '', strtoupper($kendaraan['no_polisi'])), 0, 8);
                
                if ($apiNopol === $dbNopol) {
                    $apiMatch = $item;
                    break;
                }
            }

            if (!$apiMatch) {
                log_message('error', '❌ Data GPS tidak ditemukan untuk nopol: ' . $nopol);
                return $this->response->setJSON(['error' => 'Data GPS tidak ditemukan untuk kendaraan ini'])->setStatusCode(404);
            }

            log_message('error', '✅ Data Tracking Ditemukan: ' . json_encode($apiMatch));
            return $this->response->setJSON($apiMatch);

        } catch (\Exception $e) {
            log_message('error', '❌ EXCEPTION: ' . $e->getMessage());
            file_put_contents(WRITEPATH . 'logs/tracking-'.date('Y-m-d').'.log', json_encode($apiMatch, JSON_PRETTY_PRINT));
            return $this->response->setJSON(['error' => 'Terjadi kesalahan saat memproses tracking'])->setStatusCode(500);
        }
    }
}
