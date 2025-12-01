<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $request;
    protected $helpers = ['url', 'form', 'text'];
    protected $session;
    protected $data = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->session = \Config\Services::session();
        
        // Load common data
        $this->data['user'] = $this->getUser();
        $this->data['isLoggedIn'] = $this->isLoggedIn();
    }

    protected function isLoggedIn()
    {
        return $this->session->has('user_id');
    }

    protected function getUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $userModel = new \App\Models\UserModel();
        return $userModel->find($this->session->get('user_id'));
    }

    protected function isAdmin()
    {
        $user = $this->getUser();
        return $user && $user['role'] === 'admin';
    }

    protected function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Please login to continue');
        }
        return null;
    }

    protected function requireAdmin()
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/')->with('error', 'Access denied');
        }
        return null;
    }
}
