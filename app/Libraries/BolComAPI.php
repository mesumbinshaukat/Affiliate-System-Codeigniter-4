<?php

namespace App\Libraries;

class BolComAPI
{
    private $clientId;
    private $clientSecret;
    private $affiliateId;
    private $apiEndpoint = 'https://api.bol.com/catalog/v4';
    private $accessToken;
    private $tokenExpiry;

    public function __construct()
    {
        $this->clientId = getenv('BOL_CLIENT_ID');
        $this->clientSecret = getenv('BOL_CLIENT_SECRET');
        $this->affiliateId = getenv('BOL_AFFILIATE_ID');
    }

    /**
     * Get OAuth access token
     */
    private function getAccessToken()
    {
        // Check if token is still valid
        if ($this->accessToken && $this->tokenExpiry > time()) {
            return $this->accessToken;
        }

        $ch = curl_init('https://login.bol.com/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'client_credentials',
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $data = json_decode($response, true);
            $this->accessToken = $data['access_token'];
            $this->tokenExpiry = time() + $data['expires_in'] - 60; // Refresh 60 seconds before expiry
            return $this->accessToken;
        }

        throw new \Exception('Failed to get Bol.com access token');
    }

    /**
     * Search products
     */
    public function searchProducts($query, $limit = 20, $offset = 0)
    {
        if (empty($this->clientId) || empty($this->clientSecret)) {
            return [
                'success' => false,
                'message' => 'Bol.com API credentials not configured',
                'products' => []
            ];
        }

        try {
            $token = $this->getAccessToken();

            $url = $this->apiEndpoint . '/search?' . http_build_query([
                'q' => $query,
                'limit' => $limit,
                'offset' => $offset,
            ]);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Accept: application/vnd.bol.com+json; version=4',
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $data = json_decode($response, true);
                return [
                    'success' => true,
                    'products' => $this->formatProducts($data['products'] ?? []),
                    'total' => $data['totalResultSize'] ?? 0,
                ];
            }

            return [
                'success' => false,
                'message' => 'API request failed',
                'products' => []
            ];
        } catch (\Exception $e) {
            log_message('error', 'Bol.com API Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'products' => []
            ];
        }
    }

    /**
     * Get product details
     */
    public function getProduct($productId)
    {
        if (empty($this->clientId) || empty($this->clientSecret)) {
            return null;
        }

        try {
            $token = $this->getAccessToken();

            $url = $this->apiEndpoint . '/products/' . $productId;

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Accept: application/vnd.bol.com+json; version=4',
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $data = json_decode($response, true);
                return $this->formatProduct($data);
            }

            return null;
        } catch (\Exception $e) {
            log_message('error', 'Bol.com API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Format products array
     */
    private function formatProducts($products)
    {
        $formatted = [];

        foreach ($products as $product) {
            $formatted[] = $this->formatProduct($product);
        }

        return $formatted;
    }

    /**
     * Format single product
     */
    private function formatProduct($product)
    {
        return [
            'external_id' => $product['id'] ?? '',
            'title' => $product['title'] ?? '',
            'description' => $product['summary'] ?? '',
            'image_url' => $product['images'][0]['url'] ?? '',
            'price' => $product['offerData']['offers'][0]['price'] ?? 0,
            'affiliate_url' => $this->generateAffiliateUrl($product['id'] ?? ''),
            'ean' => $product['ean'] ?? '',
            'source' => 'bol.com',
        ];
    }

    /**
     * Generate affiliate URL
     */
    public function generateAffiliateUrl($productId)
    {
        if (empty($this->affiliateId)) {
            return "https://www.bol.com/nl/p/{$productId}";
        }

        return "https://www.bol.com/nl/p/{$productId}/?Referrer=ADVNLPPce4ba9f7e5e0b4f6f8c8d8e8f8g8h8i8j8k8l8m8n8o8p8q8r8s8t8u8v8w8x8y8z";
    }
}
