<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class SocialAuth extends BaseConfig
{
    /**
     * Hybridauth Configuration
     * 
     * @var array
     */
    public array $config = [];

    public function __construct()
    {
        parent::__construct();

        // Force HTTPS for callback URL in production
        $callbackUrl = base_url('auth/social/callback');
        if (strpos(base_url(), 'https://') === 0) {
            $callbackUrl = str_replace('http://', 'https://', $callbackUrl);
        }

        $this->config = [
            'callback' => $callbackUrl,
            
            'providers' => [
                'Facebook' => [
                    'enabled' => true,
                    'keys' => [
                        'id' => getenv('FACEBOOK_APP_ID'),
                        'secret' => getenv('FACEBOOK_APP_SECRET'),
                    ],
                    'scope' => 'email, public_profile',
                    'trustForwarded' => false,
                ],
                
                'Google' => [
                    'enabled' => true,
                    'keys' => [
                        'id' => getenv('GOOGLE_CLIENT_ID'),
                        'secret' => getenv('GOOGLE_CLIENT_SECRET'),
                    ],
                    'scope' => 'openid profile email',
                    'access_type' => 'online',
                    'approval_prompt' => 'auto',
                    'trustForwarded' => true,
                ],
            ],
            
            'debug_mode' => ENVIRONMENT === 'development',
            'debug_file' => WRITEPATH . 'logs/hybridauth.log',
        ];
    }

    /**
     * Get provider configuration
     * 
     * @param string $provider Provider name (facebook, google)
     * @return array|null Provider config or null if not found
     */
    public function getProviderConfig(string $provider): ?array
    {
        $providerKey = ucfirst(strtolower($provider));
        return $this->config['providers'][$providerKey] ?? null;
    }

    /**
     * Check if provider is enabled
     * 
     * @param string $provider Provider name
     * @return bool
     */
    public function isProviderEnabled(string $provider): bool
    {
        $config = $this->getProviderConfig($provider);
        return $config && ($config['enabled'] ?? false);
    }

    /**
     * Get list of enabled providers
     * 
     * @return array
     */
    public function getEnabledProviders(): array
    {
        $enabled = [];
        foreach ($this->config['providers'] as $name => $config) {
            if ($config['enabled'] ?? false) {
                $enabled[] = strtolower($name);
            }
        }
        return $enabled;
    }
}
