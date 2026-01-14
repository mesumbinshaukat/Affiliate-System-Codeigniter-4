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
    public function login(string $provider = '')
    {
        try {
            // Get referrer to redirect back after error (register or login page)
            $referrer = $this->request->getServer('HTTP_REFERER');
            $redirectPage = (strpos($referrer, '/register') !== false) ? '/register' : '/login';
            
            // Log raw request data
            log_message('info', 'Social auth login - Raw provider param: [' . $provider . ']');
            log_message('info', 'Social auth login - URI: ' . $this->request->getUri());
            log_message('info', 'Social auth login - Method: ' . $this->request->getMethod());
            log_message('info', 'Social auth login - Referrer: ' . ($referrer ?? 'NULL'));
            
            // Validate provider
            $provider = strtolower(trim($provider));
            
            if (empty($provider)) {
                log_message('error', 'Empty provider parameter received');
                return redirect()->to($redirectPage)->with('error', 'Invalid social login provider.');
            }
            
            if (!in_array($provider, ['facebook', 'google'])) {
                log_message('error', 'Invalid social provider attempted: [' . $provider . '] - Length: ' . strlen($provider));
                return redirect()->to($redirectPage)->with('error', 'Invalid social login provider.');
            }

            // Check if provider is enabled
            if (!$this->socialAuthConfig->isProviderEnabled($provider)) {
                log_message('error', 'Disabled social provider attempted: ' . $provider);
                return redirect()->to($redirectPage)->with('error', ucfirst($provider) . ' login is currently disabled.');
            }

            // Store intended redirect URL in session
            $intendedUrl = $this->request->getGet('redirect') ?? '/dashboard';
            
            // Store provider and redirect in session
            $this->session->set([
                'social_auth_provider' => $provider,
                'social_auth_redirect' => $intendedUrl,
                'social_auth_started' => time()
            ]);
            
            // Get session ID for tracking
            $sessionId = session_id();
            
            log_message('info', 'Starting OAuth flow - Provider: ' . $provider . ' | Session ID: ' . $sessionId);
            log_message('info', 'Session data stored: ' . json_encode([
                'provider' => $this->session->get('social_auth_provider'),
                'redirect' => $this->session->get('social_auth_redirect'),
                'started' => $this->session->get('social_auth_started')
            ]));
            log_message('info', 'Callback URL configured: ' . $this->socialAuthConfig->config['callback']);

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
            // Get session ID for debugging
            $sessionId = session_id();
            
            // Get provider from session
            $provider = $this->session->get('social_auth_provider');
            
            // Log callback details for debugging
            log_message('info', 'OAuth callback received - Session ID: ' . $sessionId);
            log_message('info', 'OAuth callback - Session provider: ' . ($provider ?? 'NULL'));
            log_message('info', 'OAuth callback - Session data: ' . json_encode([
                'provider' => $this->session->get('social_auth_provider'),
                'redirect' => $this->session->get('social_auth_redirect'),
                'started' => $this->session->get('social_auth_started'),
                'session_keys' => array_keys($_SESSION ?? [])
            ]));
            log_message('info', 'OAuth callback params: ' . json_encode([
                'state' => $this->request->getGet('state'),
                'code' => $this->request->getGet('code') ? 'present' : 'missing',
                'error' => $this->request->getGet('error'),
                'scope' => $this->request->getGet('scope')
            ]));
            
            // If provider not in session, try to detect from Hybridauth storage
            if (!$provider) {
                log_message('warning', 'Provider not in session, attempting recovery from Hybridauth storage');
                
                // Try both Google and Facebook to see which one has active session
                $hybridauth = new Hybridauth($this->socialAuthConfig->config);
                
                foreach (['Google', 'Facebook'] as $testProvider) {
                    try {
                        $adapter = $hybridauth->getAdapter($testProvider);
                        if ($adapter->isConnected()) {
                            $provider = strtolower($testProvider);
                            log_message('info', 'Provider recovered from Hybridauth: ' . $provider);
                            // Store in session for next time
                            $this->session->set('social_auth_provider', $provider);
                            break;
                        }
                    } catch (\Exception $e) {
                        // Continue checking other providers
                        continue;
                    }
                }
            }
            
            if (!$provider) {
                log_message('error', 'Social auth callback without provider. Session lost. Cookie data: ' . json_encode($_COOKIE ?? []));
                return redirect()->to('/register')->with('error', 'Authentication session expired. Please try registering with Google again.');
            }

            // Check for OAuth errors in URL
            if ($this->request->getGet('error')) {
                $error = $this->request->getGet('error');
                $errorDescription = $this->request->getGet('error_description') ?? 'Unknown error';
                
                log_message('error', 'OAuth error from provider: ' . $error . ' - ' . $errorDescription);
                
                // Get redirect page from session or default to register
                $redirectPage = $this->session->get('social_auth_redirect') ?? '/register';
                if (strpos($redirectPage, '/dashboard') !== false || strpos($redirectPage, '/list') !== false) {
                    $redirectPage = '/register'; // Default to register if coming from dashboard/list
                }
                
                // User cancelled authentication
                if ($error === 'access_denied') {
                    return redirect()->to($redirectPage)->with('error', 'You cancelled the login process.');
                }
                
                return redirect()->to($redirectPage)->with('error', 'Authentication failed: ' . $errorDescription);
            }

            // Initialize Hybridauth
            $hybridauth = new Hybridauth($this->socialAuthConfig->config);

            // Authenticate with provider - this completes the OAuth callback flow
            log_message('info', 'Authenticating with provider: ' . $provider);
            $adapter = $hybridauth->authenticate(ucfirst($provider));

            // Get user profile from provider
            log_message('info', 'Fetching user profile from provider: ' . $provider);
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

            // Log profile data for debugging
            log_message('info', 'Creating/updating user from social auth. Email: ' . ($profileData['email'] ?? 'NULL') . ' | Provider: ' . $provider);
            
            // Create or update user
            $user = $this->userModel->createOrUpdateFromSocial($profileData, $provider);

            if (!$user) {
                log_message('error', 'Failed to create/update user from social auth: ' . $provider . ' | Profile: ' . json_encode($profileData));
                return redirect()->to('/register')->with('error', 'Unable to create your account. Please try again or register manually.');
            }

            // Log successful user creation/update
            log_message('info', 'User successfully created/updated from social auth. User ID: ' . $user['id'] . ' | Email: ' . ($user['email'] ?? 'NULL') . ' | Provider: ' . $user['provider']);

            // Check if user is blocked
            if ($user['status'] === 'blocked') {
                log_message('warning', 'Blocked user attempted social login: ' . $user['id']);
                return redirect()->to('/register')->with('error', 'Your account has been blocked. Please contact support.');
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
