<?php

namespace App\Controllers;

use App\Models\UserModel;

class User extends BaseController
{
    /**
     * Show user profile
     */
    public function profile()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $userId = $this->session->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to('/dashboard')->with('error', 'Gebruiker niet gevonden');
        }

        $this->data['user'] = $user;
        $this->data['age'] = $userModel->getAge($userId);

        return view('user/profile', $this->data);
    }

    /**
     * Update user profile
     */
    public function updateProfile()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->back();
        }

        $userId = $this->session->get('user_id');
        $userModel = new UserModel();

        // Validation rules
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name' => 'required|min_length[2]|max_length[100]',
            'date_of_birth' => 'required|valid_date',
            'gender' => 'permit_empty|in_list[male,female,other]',
            'bio' => 'permit_empty|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $gender = $this->request->getPost('gender');
        $data = [
            'first_name' => trim($this->request->getPost('first_name')),
            'last_name' => trim($this->request->getPost('last_name')),
            'date_of_birth' => $this->request->getPost('date_of_birth'),
            'gender' => !empty($gender) ? $gender : null,
            'bio' => trim($this->request->getPost('bio')),
        ];

        try {
            if ($userModel->update($userId, $data)) {
                return redirect()->to('/user/profile')
                    ->with('success', 'Profiel succesvol bijgewerkt');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Fout bij het bijwerken van profiel');
            }
        } catch (\Exception $e) {
            log_message('error', 'Profile update error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Er is een fout opgetreden');
        }
    }
}
