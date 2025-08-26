<?php
namespace App\Controllers\User;

use App\Controllers\BaseController;
use Myth\Auth\Models\UserModel;

class Profile extends BaseController
{
    public function index()
    {
        $user = user();

        if (!$user) {
            return redirect()->to('login');
        }

        return view('user/profile', ['user' => $user]);
    }

    public function update()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'username'         => 'required|min_length[3]|max_length[50]',
            'fullname'         => 'required|min_length[3]|max_length[100]',
            'email'            => [
                'label' => 'Email',
                'rules' => 'required|valid_email|regex_match[/@pu\.go\.id$/]',
                'errors' => [
                    'regex_match' => 'Email harus menggunakan domain @pu.go.id'
                ]
            ],
            'unit_organisasi'  => 'required',
            'unit_kerja'       => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        $userModel = new UserModel();
        $userId = user_id();
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to('login')->with('error', 'User tidak ditemukan.');
        }

        $data = [
            'username'         => $this->request->getPost('username'),
            'fullname'         => $this->request->getPost('fullname'),
            'email'            => $this->request->getPost('email'),
            'unit_organisasi'  => $this->request->getPost('unit_organisasi'),
            'unit_kerja'       => $this->request->getPost('unit_kerja'),
        ];

        // Gunakan entity untuk update
        $user->fill($data);
        $userModel->save($user); // Akan otomatis update berdasarkan primary key

        return redirect()->back()->with('message', 'Profil berhasil diperbarui.');
    }
}
