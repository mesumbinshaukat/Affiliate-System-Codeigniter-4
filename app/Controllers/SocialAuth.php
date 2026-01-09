<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ListModel;
use App\Models\CategoryModel;
use Config\SocialAuth as SocialAuthConfig;
use Hybridauth\Hybridauth;
use Hybridauth\Exception\Exception as HybridauthException;

class SocialAuth extends BaseController
{
    protected $socialAuthConfig;
    protected $userModel;

    public function __construct()
    {
        $this->socialAuthConfig = new SocialAuthConfig();
        $this->userModel = new UserModel();
    }

    /**
     * Redirect to social provider for authentication
     * 
     * @param string $provider Provider name (facebook, google)
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function login(string $provider)
    {
        try {
            // Validate provider
            $provider = strtolower($provider);
            if (!in_array($provider, ['facebook', 'google'])) {
                log_message('error', 'Invalid social provider attempted: ' . $provider);
                return redirect()->to('/login')->with('error', 'Invalid social login provider.');
            }

            // Check if provider is enabled
            if (!$this->socialAuthConfig->isProviderEnabled($provider)) {
                log_message('error', 'Disabled social provider attempted: ' . $provider);
                return redirect()->to('/login')->with('error', ucfirst($provider) . ' login is currently disabled.');
            }

            // Store intended redirect URL in session
            $intendedUrl = $this->request->getGet('redirect') ?? '/dashboard';
            $this->session->set('social_auth_redirect', $intendedUrl);

            // Store provider in session for callback
            $this->session->set('social_auth_provider', $provider);

            // Initialize Hybridauth
            $hybridauth = new Hybridauth($this->socialAuthConfig->config);

            // Authenticate with provider
            $adapter = $hybridauth->authenticate(ucfirst($provider));

            // This line won't be reached as authenticate() redirects
            // But we keep it for safety
            return redirect()->to('/login');

        } catch (HybridauthException $e) {
            log_message('error', 'Hybridauth error during ' . $provider . ' login: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->to('/login')->with('error', 'Unable to connect to ' . ucfirst($provider) . '. Please try again later.');
        } catch (\Exception $e) {
            log_message('error', 'Unexpected error during ' . $provider . ' login: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->to('/login')->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    /**
     * Handle OAuth callback from provider
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function callback()
    {
        try {
            // Get provider from session
            $provider = $this->session->get('social_auth_provider');
            
            if (!$provider) {
                log_message('error', 'Social auth callback without provider in session');
                return redirect()->to('/login')->with('error', 'Authentication session expired. Please try again.');
            }

            // Check for OAuth errors in URL
            if ($this->request->getGet('error')) {
                $error = $this->request->getGet('error');
                $errorDescription = $this->request->getGet('error_description') ?? 'Unknown error';
                
                log_message('error', 'OAuth error from provider: ' . $error . ' - ' . $errorDescription);
                
                // User cancelled authentication
                if ($error === 'access_denied') {
                    return redirect()->to('/login')->with('error', 'You cancelled the login process.');
                }
                
                return redirect()->to('/login')->with('error', 'Authentication failed: ' . $errorDescription);
            }

            // Initialize Hybridauth
            $hybridauth = new Hybridauth($this->socialAuthConfig->config);

            // Get adapter for provider
            $adapter = $hybridauth->getAdapter(ucfirst($provider));

            // Check if authenticated
            if (!$adapter->isConnected()) {
                log_message('error', 'Social auth callback but adapter not connected for provider: ' . $provider);
                return redirect()->to('/login')->with('error', 'Authentication failed. Please try again.');
            }

            // Get user profile from provider
            $userProfile = $adapter->getUserProfile();

            // Validate required data
            if (empty($userProfile->identifier)) {
                log_message('error', 'Social auth: No identifier returned from ' . $provider);
                $adapter->disconnect();
                return redirect()->to('/login')->with('error', 'Unable to retrieve your profile information. Please try again.');
            }

            // Convert profile to array
            $profileData = [
                'identifier' => $userProfile->identifier,
                'email' => $userProfile->email ?? null,
                'firstName' => $userProfile->firstName ?? '',
                'lastName' => $userProfile->lastName ?? '',
                'displayName' => $userProfile->displayName ?? '',
                'photoURL' => $userProfile->photoURL ?? null,
                'emailVerified' => $userProfile->emailVerified ?? false,
                'access_token' => $adapter->getAccessToken()['access_token'] ?? null,
            ];

            log_message('info', 'Social auth profile retrieved for ' . $provider . ': ' . $profileData['identifier']);

            // Disconnect adapter
            $adapter->disconnect();

            // Create or update user
            $user = $this->userModel->createOrUpdateFromSocial($profileData, $provider);

            if (!$user) {
                log_message('error', 'Failed to create/update user from social auth: ' . $provider);
                return redirect()->to('/login')->with('error', 'Unable to create your account. Please try again or register manually.');
            }

            // Check if user is blocked
            if ($user['status'] === 'blocked') {
                log_message('warning', 'Blocked user attempted social login: ' . $user['id']);
                return redirect()->to('/login')->with('error', 'Your account has been blocked. Please contact support.');
            }

            // Set session data
            $this->session->set([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'logged_in' => true,
            ]);

            log_message('info', 'User logged in via ' . $provider . ': ' . $user['id']);

            // Check if this is a new user (just created)
            $isNewUser = !$this->userModel->where('id', $user['id'])
                                          ->where('created_at <', date('Y-m-d H:i:s', strtotime('-10 seconds')))
                                          ->first();

            // Create default list for new users
            if ($isNewUser) {
                $this->createDefaultListForUser($user);
            }

            // Get intended redirect URL
            $redirectUrl = $this->session->get('social_auth_redirect') ?? '/dashboard';
            $this->session->remove('social_auth_redirect');
            $this->session->remove('social_auth_provider');

            // Redirect based on role
            if ($user['role'] === 'admin') {
                return redirect()->to('/admin')->with('success', 'Welcome back, ' . $user['username'] . '!');
            }

            // For new users, redirect to list creation
            if ($isNewUser) {
                return redirect()->to($redirectUrl)->with('success', 'Welkom bij Lijstje.nl, ' . $user['first_name'] . '! Your account has been created.');
            }

            return redirect()->to($redirectUrl)->with('success', 'Welcome back, ' . $user['username'] . '!');

        } catch (HybridauthException $e) {
            log_message('error', 'Hybridauth error during callback: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            // Clean up session
            $this->session->remove('social_auth_redirect');
            $this->session->remove('social_auth_provider');
            
            return redirect()->to('/login')->with('error', 'Authentication failed. Please try again.');
        } catch (\Exception $e) {
            log_message('error', 'Unexpected error during social auth callback: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            // Clean up session
            $this->session->remove('social_auth_redirect');
            $this->session->remove('social_auth_provider');
            
            return redirect()->to('/login')->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    /**
     * Create default list for new user
     * 
     * @param array $user User data
     * @return void
     */
    private function createDefaultListForUser(array $user): void
    {
        try {
            $listModel = new ListModel();
            $categoryModel = new CategoryModel();

            $defaultListTitle = $user['first_name'] . ' - Default List';
            $slug = url_title($defaultListTitle, '-', true);
            
            // Ensure unique slug
            $existingSlug = $listModel->where('slug', $slug)->first();
            if ($existingSlug) {
                $slug = $slug . '-' . time();
            }
            
            // Get first available category
            $firstCategory = $categoryModel->first();
            $categoryId = $firstCategory ? $firstCategory['id'] : null;
            
            $listData = [
                'user_id' => $user['id'],
                'category_id' => $categoryId,
                'title' => $defaultListTitle,
                'slug' => $slug,
                'description' => '',
                'status' => 'published',
            ];
            
            if ($listModel->insert($listData)) {
                log_message('info', 'Default list created for social auth user: ' . $user['id']);
            } else {
                log_message('error', 'Failed to create default list for social auth user: ' . $user['id']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error creating default list for social auth user: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect social provider from user account
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function disconnect()
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/login');
        }

        try {
            $userId = $this->session->get('user_id');
            $user = $this->userModel->find($userId);

            if (!$user || empty($user['provider'])) {
                return redirect()->back()->with('error', 'No social account connected.');
            }

            // Check if user has a password (can login without social)
            if (empty($user['password']) || strlen($user['password']) < 10) {
                return redirect()->back()->with('error', 'Please set a password before disconnecting your social account.');
            }

            // Remove social auth data
            $this->userModel->update($userId, [
                'provider' => null,
                'provider_id' => null,
                'provider_token' => null,
            ]);

            log_message('info', 'User disconnected social auth: ' . $userId);

            return redirect()->back()->with('success', 'Social account disconnected successfully.');

        } catch (\Exception $e) {
            log_message('error', 'Error disconnecting social auth: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to disconnect social account. Please try again.');
        }
    }
}
