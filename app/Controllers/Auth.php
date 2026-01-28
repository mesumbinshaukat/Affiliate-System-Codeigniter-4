<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ListModel;
use App\Models\CategoryModel;

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
                'gender' => 'permit_empty|in_list[male,female,other]',
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

            $gender = $this->request->getPost('gender');
            $data = [
                'username' => $username,
                'email' => $email,
                'password' => $this->request->getPost('password'),
                'first_name' => trim($this->request->getPost('first_name')),
                'last_name' => trim($this->request->getPost('last_name')),
                'date_of_birth' => $this->request->getPost('date_of_birth'),
                'gender' => !empty($gender) ? $gender : null,
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

                    // Create default list for new user
                    $listModel = new ListModel();
                    $defaultListTitle = $data['first_name'] . ' - Default List';
                    $slug = url_title($defaultListTitle, '-', true);
                    
                    // Check if slug exists and make it unique
                    $existingSlug = $listModel->where('slug', $slug)->first();
                    if ($existingSlug) {
                        $slug = $slug . '-' . time();
                    }
                    
                    // Get first available category or use NULL
                    $categoryModel = new CategoryModel();
                    $firstCategory = $categoryModel->first();
                    $categoryId = $firstCategory ? $firstCategory['id'] : null;
                    
                    $listData = [
                        'user_id' => $userId,
                        'category_id' => $categoryId,
                        'title' => $defaultListTitle,
                        'slug' => $slug,
                        'description' => '',
                        'status' => 'published',
                    ];
                    
                    if ($listModel->insert($listData)) {
                        $listId = $listModel->getInsertID();
                        log_message('info', 'Default list created for user ' . $userId . ' with list ID: ' . $listId);
                        
                        return redirect()->to('/dashboard/list/edit/' . $listId . '?tab=products')
                            ->with('success', 'Welkom bij Maakjelijstje.nl, ' . $data['first_name'] . '! Maak nu uw eerste lijst.');
                    } else {
                        log_message('error', 'Failed to create default list for user ' . $userId . '. Errors: ' . json_encode($listModel->errors()));
                        return redirect()->to('/dashboard')
                            ->with('success', 'Welkom bij Maakjelijstje.nl, ' . $data['first_name'] . '! Maak nu uw eerste lijst.');
                    }
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
