<?php

namespace Myth\Auth\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Session\Session;
use Myth\Auth\Config\Auth as AuthConfig;
use Myth\Auth\Entities\User;
use Myth\Auth\Models\UserModel;

class AuthController extends Controller
{
    /**
     * Analysis assist; remove after CodeIgniter 4.3 release.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    protected $auth;

    /**
     * @var AuthConfig
     */
    protected $config;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Most services in this controller require
        // the session to be started - so fire it up!
        $this->session = service('session');

        $this->config = config('Auth');
        $this->auth = service('authentication');
    }

    // --------------------------------------------------------------------
    // Login/out
    // --------------------------------------------------------------------
    /**
     * Displays the login form, or redirects
     * the user to their destination/home if
     * they are already logged in.
     *
     * @return RedirectResponse|string
     */
    public function login()
    {
        // No need to show a login form if the user
        // is already logged in.
        if ($this->auth->check()) {
            $redirectURL = session('redirect_url') ?? site_url($this->config->landingRoute);
            unset($_SESSION['redirect_url']);

            return redirect()
                ->to($redirectURL);
        }

        // Set a return URL if none is specified.
        $_SESSION['redirect_url'] = session('redirect_url') ?? previous_url();

        // Display the login view.
        return $this->_render($this->config->views['login'], ['config' => $this->config]);
    }

    /**
     * Attempts to verify the user's credentials
     * through a POST request.
     *
     * @return RedirectResponse
     */
    public function attemptLogin()
    {
        $rules = [
            'login' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Email atau Username harus diisi'
                ]
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Kata Sandi harus diisi'
                ]
            ],
        ];

        if ($this->config->validFields === ['email']) {
            $rules['login'] .= '|valid_email';
        }

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $login = $this->request->getPost('login');
        $password = $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');

        // Determine credential type
        $type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $user = $builder->where($type, $login)
            ->limit(1)
            ->get()
            ->getRowArray();

        // Try to log them in...
        if (!$this->auth->attempt([$type => $login, 'password' => $password], $remember)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $this->auth->error() ?? lang('Auth.badAttempt'));
        }

        // Is the user being forced to reset their password?
        if ($this->auth->user()->force_pass_reset === true) {
            $url = route_to('reset-password') . '?token=' . $this->auth->user()->reset_hash;

            return redirect()
                ->to($url)
                ->withCookies();
        }

        $redirectURL = session('homepage') ?? site_url($this->config->landingRoute);
        unset($_SESSION['homepage']);

        return redirect()
            ->to($redirectURL)
            ->withCookies()
            ->with('message', lang('Auth.loginSuccess'));
    }

    /**
     * Log the user out.
     *
     * @return RedirectResponse
     */
    public function logout()
    {
        if ($this->auth->check()) {
            $this->auth->logout();
        }

        return redirect()->to(site_url('/'));
    }

    // --------------------------------------------------------------------
    // Register
    // --------------------------------------------------------------------
    /**
     * Displays the user registration page.
     *
     * @return RedirectResponse|string
     */

    //  REGISTER ASLI
    // public function register()
    // {
    //     // check if already logged in.
    //     if ($this->auth->check()) {
    //         return redirect()->back();
    //     }

    //     // Check if registration is allowed
    //     if (!$this->config->allowRegistration) {
    //         return redirect()
    //             ->back()
    //             ->withInput()
    //             ->with('error', lang('Auth.registerDisabled'));
    //     }

    //     return $this->_render($this->config->views['register'], ['config' => $this->config]);
    // }

    public function register()
    {
        if ($this->request->isAJAX()) {
            // Check if registration is allowed
            if (!$this->config->allowRegistration) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Auth.registerDisabled')
                ]);
            }
    
            return $this->_render($this->config->views['register'], ['config' => $this->config]);
        }
    
        // check if already logged in.
        if ($this->auth->check()) {
            return redirect()->back();
        }
    
        // Check if registration is allowed
        if (!$this->config->allowRegistration) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', lang('Auth.registerDisabled'));
        }
    
        return $this->_render($this->config->views['register'], ['config' => $this->config]);
    }

    /**
     * Attempt to register a new user.
     *
     * @return RedirectResponse
     */
    // public function attemptRegister()
    // {
    //     // Check if registration is allowed
    //     if (!$this->config->allowRegistration) {
    //         return redirect()
    //             ->back()
    //             ->withInput()
    //             ->with('error', lang('Auth.registerDisabled'));
    //     }

    //     $users = model(UserModel::class);

    //     // Validate basics first since some password rules rely on these fields
    //     $rules = [
    //         'fullname' => [
    //             'rules' => 'required|min_length[4]',
    //             'errors' => [
    //                 'required' => 'Fullname harus diisi.',
    //                 'min_length' => 'Fullname harus memiliki minimal 4 karakter.'
    //             ]
    //         ],
    //         'username' => [
    //             'rules' => 'required|alpha_numeric_space|min_length[4]|max_length[30]|is_unique[users.username]',
    //             'errors' => [
    //                 'required' => 'Username harus diisi.',
    //                 'alpha_numeric_space' => 'Username hanya boleh berisi huruf, angka, dan spasi.',
    //                 'min_length' => 'Username harus memiliki minimal 4 karakter.',
    //                 'max_length' => 'Username tidak boleh lebih dari 30 karakter.',
    //                 // 'is_unique' => 'Username sudah terdaftar.'
    //                 'is_unique' => 'Username sudah digunakan.'
    //             ]
    //         ],
    //         'email' => [
    //             'rules' => 'required|valid_email|regex_match[/@pu\.go\.id$/]|is_unique[users.email,id,{id}]',
    //             'errors' => [
    //                 'required' => 'Email harus diisi.',
    //                 'valid_email' => 'Alamat email tidak valid.',
    //                 'regex_match' => 'Email harus menggunakan domain @pu.go.id.',
    //                 'is_unique' => 'Email sudah terdaftar.'
    //             ]
    //         ],
    //         'unit_organisasi' => [
    //             'rules' => 'required',
    //             'errors' => ['required' => 'Unit Organisasi harus dipilih.']
    //         ],
    //         'unit_kerja' => [
    //             'rules' => 'required',
    //             'errors' => ['required' => 'Unit Kerja harus dipilih.']
    //         ],
    //         'password' => [
    //             'rules' => 'required|strong_password',
    //             'errors' => [
    //                 'required' => 'Kata Sandi harus diisi.',
    //                 'strong_password' => 'Kata Sandi harus mengandung karakter khusus dan angka.'
    //             ]
    //         ],
    //         'pass_confirm' => [
    //             'rules' => 'required|matches[password]',
    //             'errors' => [
    //                 'required' => 'Konfirmasi Kata Sandi harus diisi.',
    //                 'matches' => 'Konfirmasi Kata Sandi tidak cocok dengan Kata Sandi.'
    //             ]
    //         ]
    //     ];

    //     if (!$this->validate($rules)) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'errors' => $this->validator->getErrors()
    //         ]);
    //     }

    //     // Validate passwords since they can only be validated properly here
    //     $rules = [
    //         'password' => 'required|strong_password',
    //         'pass_confirm' => 'required|matches[password]',
    //     ];

    //     if (!$this->validate($rules)) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'errors' => $this->validator->getErrors()
    //         ]);
    //     }

    //     $allowedPostFields = array_merge(
    //         ['password', 'fullname', 'unit_organisasi', 'unit_kerja'],
    //         $this->config->validFields,
    //         $this->config->personalFields
    //     );
    //     $user = new User($this->request->getPost($allowedPostFields));

    //     $this->config->requireActivation === null ? $user->activate() : $user->generateActivateHash();

    //     // Ensure default group gets assigned if set
    //     if (!empty($this->config->defaultUserGroup)) {
    //         $users = $users->withGroup($this->config->defaultUserGroup);
    //     }

    //     if (!$users->save($user)) {
    //         return redirect()
    //             ->back()
    //             ->withInput()
    //             ->with('errors', $users->errors());
    //     }

    //     if ($this->config->requireActivation !== null) {
    //         $activator = service('activator');
    //         $sent = $activator->send($user);

    //         if (!$sent) {
    //             return $this->response->setJSON([
    //                 'success' => false,
    //                 'message' => $activator->error() ?? lang('Auth.unknownError')
    //             ]);
    //         }

    //         // Success!
    //         return $this->response->setJSON([
    //             'success' => true,
    //             'message' => lang('Auth.activationSuccess')
    //         ]);
    //     }

    //     // Success!
    //     return $this->response->setJSON([
    //         'success' => true,
    //         'message' => lang('Auth.registerSuccess')
    //     ]);
    // }

    private function getRoleId($roleName)
    {
        $db = \Config\Database::connect();
        $role = $db->table('auth_groups')
            ->where('name', $roleName)
            ->get()
            ->getRow();
            
        if (!$role) {
            throw new \Exception('Role tidak ditemukan');
        }
        
        return $role->id;
    }

    public function attemptRegister()
    {
        // Check if registration is allowed
        if (!$this->config->allowRegistration) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Auth.registerDisabled')
            ]);
        }

        $users = model(UserModel::class);
        $db = \Config\Database::connect();

        $email = $this->request->getPost('email');
        $username = $this->request->getPost('username');
        
        $existingUser = $db->table('users')
            ->groupStart()
                ->where('email', $email)
                ->orWhere('username', $username)
            ->groupEnd()
            ->get()
            ->getRow();

        if ($existingUser) {
            $db->transStart();
            
            try {
                $db->table('auth_groups_users')
                    ->where('user_id', $existingUser->id)
                    ->delete();

                $db->table('users')
                    ->where('id', $existingUser->id)
                    ->delete();

                $db->transComplete();

                if ($db->transStatus() === false) {
                    throw new \Exception('Gagal menghapus data pengguna lama');
                }
            } catch (\Exception $e) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus data lama: ' . $e->getMessage()
                ]);
            }
        }

        $rules = [
            'fullname' => [
                'rules' => 'required|min_length[4]',
                'errors' => [
                    'required' => 'Fullname harus diisi.',
                    'min_length' => 'Fullname harus memiliki minimal 4 karakter.'
                ]
            ],
            'username' => [
                'rules' => 'required|alpha_numeric_space|min_length[4]|max_length[30]',
                'errors' => [
                    'required' => 'Username harus diisi.',
                    'alpha_numeric_space' => 'Username hanya boleh berisi huruf, angka, dan spasi.',
                    'min_length' => 'Username harus memiliki minimal 4 karakter.',
                    'max_length' => 'Username tidak boleh lebih dari 30 karakter.'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|regex_match[/@pu\.go\.id$/]',
                'errors' => [
                    'required' => 'Email harus diisi.',
                    'valid_email' => 'Alamat email tidak valid.',
                    'regex_match' => 'Email harus menggunakan domain @pu.go.id.'
                ]
            ],
            'unit_organisasi' => [
                'rules' => 'required',
                'errors' => ['required' => 'Unit Organisasi harus dipilih.']
            ],
            'unit_kerja' => [
                'rules' => 'required',
                'errors' => ['required' => 'Unit Kerja harus dipilih.']
            ],
            'role' => [
                'rules' => 'required|in_list[user,admin,admin_gedungutama,admin_pusdatin,admin_binamarga,admin_ciptakarya,admin_sda,admin_gedungg,admin_heritage,admin_auditorium]',
                'errors' => [
                    'required' => 'Role harus dipilih.',
                    'in_list' => 'Role yang dipilih tidak valid.'
                ]
            ],
            'password' => [
                'rules' => 'required|strong_password',
                'errors' => [
                    'required' => 'Kata Sandi harus diisi.',
                    'strong_password' => 'Kata Sandi harus mengandung karakter khusus dan angka.'
                ]
            ],
            'pass_confirm' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Konfirmasi Kata Sandi harus diisi.',
                    'matches' => 'Konfirmasi Kata Sandi tidak cocok dengan Kata Sandi.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        $db->transStart();

        try {
            $allowedPostFields = array_merge(
                ['password', 'fullname', 'unit_organisasi', 'unit_kerja'],
                $this->config->validFields,
                $this->config->personalFields
            );

            $user = new User($this->request->getPost($allowedPostFields));

            if ($this->config->requireActivation !== null) {
                $user->generateActivateHash();
            } else {
                $user->activate();
            }

            if (!$users->save($user)) {
                throw new \Exception(implode("\n", $users->errors()));
            }

            $userId = $users->getInsertID();

            $roleId = $this->getRoleId($this->request->getPost('role'));
            $db->table('auth_groups_users')->insert([
                'user_id' => $userId,
                'group_id' => $roleId
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan data pengguna');
            }

            if ($this->config->requireActivation !== null) {
                $activator = service('activator');
                $sent = $activator->send($user);

                if (!$sent) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => $activator->error() ?? lang('Auth.unknownError')
                    ]);
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $this->config->requireActivation !== null ? 
                    lang('Auth.activationSuccess') : lang('Auth.registerSuccess')
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // --------------------------------------------------------------------
    // Forgot Password
    // --------------------------------------------------------------------
    /**
     * Displays the forgot password form.
     *
     * @return RedirectResponse|string
     */
    public function forgotPassword()
    {
        if ($this->config->activeResetter === null) {
            return redirect()
                ->route('login')
                ->with('error', lang('Auth.forgotDisabled'));
        }

        return $this->_render($this->config->views['forgot'], ['config' => $this->config]);
    }

    /**
     * Attempts to find a user account with that password
     * and send password reset instructions to them.
     *
     * @return RedirectResponse
     */
    public function attemptForgot()
    {
        if ($this->config->activeResetter === null) {
            return redirect()
                ->route('login')
                ->with('error', lang('Auth.forgotDisabled'));
        }

        $rules = [
            'email' => [
                'label' => lang('Auth.emailAddress'),
                'rules' => 'required|valid_email|regex_match[/@pu\.go\.id$/]',
                'errors' => [
                    'required' => 'Email harus diisi.',
                    // 'valid_email' => 'Format email tidak valid.',
                    'regex_match' => 'Email harus menggunakan domain @pu.go.id'
                ]
            ],
        ];

        // if (!$this->validate($rules)) {
        //     return redirect()
        //         ->back()
        //         ->withInput()
        //         ->with('errors', $this->validator->getErrors());
        // }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $this->validator->getErrors()['email']
            ]);
        }

        $users = model(UserModel::class);
        $user = $users->where('email', $this->request->getPost('email'))->first();

        // if (null === $user) {
        //     return redirect()
        //         ->back()
        //         ->with('error', lang('Auth.forgotNoUser'));
        // }

        if (null === $user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Auth.forgotNoUser')
            ]);
        }

        // Save the reset hash /
        $user->generateResetHash();
        $users->save($user);

        $resetter = service('resetter');
        $sent = $resetter->send($user);

        // if (!$sent) {
        //     return redirect()
        //         ->back()
        //         ->withInput()
        //         ->with('error', $resetter->error() ?? lang('Auth.unknownError'));
        // }

        // return redirect()
        //     ->route('reset-password')
        //     ->with('message', lang('Auth.forgotEmailSent'));

        // BUAT NAMPILIN MODAL SUKSES NGIRIM EMAIL RESETPASSWORD
        if (!$sent) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $resetter->error() ?? lang('Auth.unknownError')
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => lang('Auth.forgotEmailSent')
        ]);
    }

    /**
     * Displays the Reset Password form.
     *
     * @return RedirectResponse|string
     */
    public function resetPassword()
    {
        if ($this->config->activeResetter === null) {
            return redirect()
                ->route('login')
                ->with('error', lang('Auth.forgotDisabled'));
        }

        $token = $this->request->getGet('token');

        return $this->_render($this->config->views['reset'], [
            'config' => $this->config,
            'token' => $token,
        ]);
    }

    /**
     * Verifies the code with the email and saves the new password,
     * if they all pass validation.
     *
     * @return RedirectResponse
     */
    public function attemptReset()
    {
        if ($this->config->activeResetter === null) {
            return redirect()
                ->route('login')
                ->with('error', lang('Auth.forgotDisabled'));
        }

        $users = model(UserModel::class);

        // First things first - log the reset attempt.
        $users->logResetAttempt(
            $this->request->getPost('email'),
            $this->request->getPost('token'),
            $this->request->getIPAddress(),
            (string) $this->request->getUserAgent()
        );

        // $rules = [
        //     'token' => 'required',
        //     'email' => 'required|valid_email',
        //     'password' => 'required|strong_password',
        //     'pass_confirm' => 'required|matches[password]',
        // ];

        $rules = [
            'token' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Token harus diisi.'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|regex_match[/@pu\.go\.id$/]',
                'errors' => [
                    'required' => 'Email harus diisi.',
                    'valid_email' => 'Alamat email tidak valid.',
                    'regex_match' => 'Email harus menggunakan domain @pu.go.id.'
                ]
            ],
            'password' => [
                'rules' => 'required|strong_password',
                'errors' => [
                    'required' => 'Kata Sandi harus diisi.',
                    'strong_password' => 'Kata Sandi harus mengandung karakter khusus dan angka.'
                ]
            ],
            'pass_confirm' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Konfirmasi Kata Sandi harus diisi.',
                    'matches' => 'Konfirmasi Kata Sandi tidak cocok dengan Kata Sandi.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $token = $this->request->getPost('token');

        $user = $users->where('email', $this->request->getPost('email'))
            ->where('reset_hash', $this->request->getPost('token'))
            ->first();

        if (null === $user) {
            return redirect()
                ->back()
                ->with('error', lang('Auth.forgotNoUser'));
        }

        // Reset token still valid?
        if (!empty($user->reset_expires) && time() > $user->reset_expires->getTimestamp()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', lang('Auth.resetTokenExpired'));
        }

        // Success! Save the new password, and cleanup the reset hash.
        $user->password = $this->request->getPost('password');
        $user->reset_hash = null;
        $user->reset_at = date('Y-m-d H:i:s');
        $user->reset_expires = null;
        $user->force_pass_reset = false;
        $users->save($user);

        // return redirect()
        //     ->route('login')
        //     ->with('message', lang('Auth.resetSuccess'));

        if ($users->save($user)) {
            session()->setFlashdata('reset_success', true);
        } else {
            session()->setFlashdata('reset_failed', 'Gagal mereset password. Silakan coba lagi.');
        }
        return redirect()->back();
    }

    /**
     * Activate account.
     *
     * @return mixed
     */
    public function activateAccount()
    {
        $users = model(UserModel::class);

        // First things first - log the activation attempt.
        $users->logActivationAttempt(
            $this->request->getGet('token'),
            $this->request->getIPAddress(),
            (string) $this->request->getUserAgent()
        );

        $throttler = service('throttler');

        if ($throttler->check(md5($this->request->getIPAddress()), 2, MINUTE) === false) {
            return service('response')
                ->setStatusCode(429)
                ->setBody(lang('Auth.tooManyRequests', [$throttler->getTokentime()]));
        }

        $user = $users->where('activate_hash', $this->request->getGet('token'))
            ->where('active', 0)
            ->first();

        if (null === $user) {
            return redirect()
                ->route('login')
                ->with('error', lang('Auth.activationNoUser'));
        }

        $user->activate();

        $users->save($user);

        return redirect()
            ->route('login')
            ->with('message', lang('Auth.registerSuccess'));
    }

    /**
     * Resend activation account.
     *
     * @return mixed
     */
    public function resendActivateAccount()
    {
        if ($this->config->requireActivation === null) {
            return redirect()
                ->route('login');
        }

        $throttler = service('throttler');

        if ($throttler->check(md5($this->request->getIPAddress()), 2, MINUTE) === false) {
            return service('response')
                ->setStatusCode(429)
                ->setBody(lang('Auth.tooManyRequests', [$throttler->getTokentime()]));
        }

        $login = urldecode($this->request->getGet('login'));
        $type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $users = model(UserModel::class);

        $user = $users->where($type, $login)
            ->where('active', 0)
            ->first();

        if (null === $user) {
            return redirect()
                ->route('login')
                ->with('error', lang('Auth.activationNoUser'));
        }

        $activator = service('activator');
        $sent = $activator->send($user);

        if (!$sent) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $activator->error() ?? lang('Auth.unknownError'));
        }

        // Success!
        return redirect()
            ->route('login')
            ->with('message', lang('Auth.activationSuccess'));
    }

    /**
     * Render the view.
     *
     * @return string
     */
    protected function _render(string $view, array $data = [])
    {
        return view($view, $data);
    }
}