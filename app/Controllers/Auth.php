<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if ($this->isLoggedIn()) {
            return redirect()->to('/dashboard');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            // Validation rules
            $rules = [
                'email' => 'required|valid_email',
                'password' => 'required|min_length[8]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            $userModel = new UserModel();
            $user = $userModel->where('email', $email)->first();

            if (!$user) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid email or password');
            }

            if (!password_verify($password, $user['password'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid email or password');
            }

            if ($user['status'] === 'blocked') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Your account has been blocked. Please contact support.');
            }

            // Set session data
            $this->session->set([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'logged_in' => true
            ]);

            // Redirect based on role
            if ($user['role'] === 'admin') {
                return redirect()->to('/admin')->with('success', 'Welcome back, ' . $user['username'] . '!');
            }

            return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $user['username'] . '!');
        }

        return view('auth/login', $this->data);
    }

    public function register()
    {
        // Check if already logged in
        if ($this->isLoggedIn()) {
            return redirect()->to('/dashboard');
        }

        // Handle POST request (case-insensitive)
        if (strtolower($this->request->getMethod()) === 'post') {
            
            // Validation rules (no username required)
            $rules = [
                'first_name' => 'required|min_length[2]|max_length[100]',
                'last_name' => 'required|min_length[2]|max_length[100]',
                'date_of_birth' => 'required|valid_date',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[8]',
                'password_confirm' => 'required|matches[password]'
            ];

            $messages = [
                'email' => [
                    'is_unique' => 'This email is already registered.'
                ],
                'password_confirm' => [
                    'matches' => 'Passwords do not match.'
                ]
            ];

            // Validate input
            if (!$this->validate($rules, $messages)) {
                log_message('error', 'Validation failed: ' . json_encode($this->validator->getErrors()));
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $userModel = new UserModel();
            
            // Generate username from email (before @)
            $email = trim($this->request->getPost('email'));
            $baseUsername = explode('@', $email)[0];
            $username = $baseUsername;
            $counter = 1;
            
            // Ensure username is unique
            while ($userModel->where('username', $username)->first()) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            $data = [
                'username' => $username,
                'email' => $email,
                'password' => $this->request->getPost('password'),
                'first_name' => trim($this->request->getPost('first_name')),
                'last_name' => trim($this->request->getPost('last_name')),
                'date_of_birth' => $this->request->getPost('date_of_birth'),
                'role' => 'user',
                'status' => 'active',
            ];

            log_message('info', 'Attempting to insert user: ' . $data['email']);

            try {
                $insertResult = $userModel->insert($data);
                
                if ($insertResult) {
                    $userId = $userModel->getInsertID();
                    log_message('info', 'User created successfully with ID: ' . $userId);
                    
                    // Set session data
                    $this->session->set([
                        'user_id' => $userId,
                        'username' => $data['username'],
                        'role' => 'user',
                        'logged_in' => true
                    ]);

                    log_message('info', 'Session set, redirecting to list creation');
                    
                    return redirect()->to('/dashboard/list/create')
                        ->with('success', 'Welcome to Lijstje.nl, ' . $data['first_name'] . '! Now create your first list.');
                }

                // If insert failed, get model errors
                $errors = $userModel->errors();
                log_message('error', 'User insert failed: ' . json_encode($errors));
                
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $errors);
                    
            } catch (\Exception $e) {
                log_message('error', 'Registration exception: ' . $e->getMessage());
                log_message('error', 'Stack trace: ' . $e->getTraceAsString());
                
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'An error occurred during registration. Please try again. Error: ' . $e->getMessage());
            }
        }

        // Show registration form
        return view('auth/register', $this->data);
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('../')->with('success', 'Logged out successfully');
    }
}
