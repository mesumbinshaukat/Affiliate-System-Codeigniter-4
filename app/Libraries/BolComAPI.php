<?php

namespace App\Libraries;

class BolComAPI
{
    private $clientId;
    private $clientSecret;
    private $affiliateId;
    private $apiEndpoint = 'https://api.bol.com/marketing/catalog/v1';
    private $accessToken;
    private $tokenExpiry;

    public function __construct()
    {
        $this->clientId = getenv('BOL_CLIENT_ID');
        $this->clientSecret = getenv('BOL_CLIENT_SECRET');
        $this->affiliateId = getenv('BOL_AFFILIATE_ID');
    }

    /**
     * Get OAuth access token using correct Bol.com authentication
     * Based on official documentation: https://api.bol.com/marketing/docs/catalog-api/
     */
    private function getAccessToken()
    {
        // Check if token is still valid (cache for ~4 minutes)
        if ($this->accessToken && $this->tokenExpiry > time()) {
            return $this->accessToken;
        }

        // Prepare Basic Auth credentials
        $credentials = base64_encode($this->clientId . ':' . $this->clientSecret);
        
        $ch = curl_init('https://login.bol.com/token?grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . $credentials,
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ''); // Empty body

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200) {
            $data = json_decode($response, true);
            if (isset($data['access_token'])) {
                $this->accessToken = $data['access_token'];
                // Token expires in ~299 seconds (5 minutes), cache for 4 minutes
                $this->tokenExpiry = time() + ($data['expires_in'] ?? 240) - 60;
                return $this->accessToken;
            }
        }

        $errorMsg = "Failed to get Bol.com access token (HTTP $httpCode)";
        if ($curlError) {
            $errorMsg .= ": $curlError";
        }
        if ($response) {
            $errorMsg .= " - Response: " . substr($response, 0, 200);
        }
        
        throw new \Exception($errorMsg);
    }

    /**
     * Search products using Marketing Catalog API v1
     * Endpoint: GET /products/search
     * Docs: https://api.bol.com/marketing/docs/catalog-api/
     */
    public function searchProducts($query, $limit = 20, $offset = 0, $countryCode = 'NL')
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

            // Build URL with correct parameters for Marketing Catalog API v1
            $url = $this->apiEndpoint . '/products/search?' . http_build_query([
                'q' => $query,
                'limit' => min($limit, 100), // Max 100 per request
                'offset' => $offset,
                'country-code' => $countryCode,
            ]);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Accept: application/json',
                'Accept-Language: nl',
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            
            // Check rate limit headers
            $rateLimitRemaining = curl_getinfo($ch, CURLINFO_HEADER_OUT);
            
            curl_close($ch);

            if ($httpCode === 200) {
                $data = json_decode($response, true);
                return [
                    'success' => true,
                    'products' => $this->formatProducts($data['products'] ?? []),
                    'total' => $data['totalResultSize'] ?? 0,
                    'paging' => $data['paging'] ?? null,
                ];
            }

            // Handle specific error codes
            $errorMsg = "API request failed (HTTP $httpCode)";
            if ($httpCode === 401) {
                $errorMsg = 'Unauthorized - Check API credentials';
            } elseif ($httpCode === 429) {
                $errorMsg = 'Rate limit exceeded - Please wait before retrying';
            } elseif ($httpCode === 403) {
                $errorMsg = 'Forbidden - Check if affiliate account is active';
            }
            
            if ($curlError) {
                $errorMsg .= ": $curlError";
            }

            return [
                'success' => false,
                'message' => $errorMsg,
                'products' => [],
                'http_code' => $httpCode,
                'response' => $response ? substr($response, 0, 500) : null
            ];
        } catch (\Exception $e) {
            if (function_exists('log_message')) {
                log_message('error', 'Bol.com API Error: ' . $e->getMessage());
            }
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
            if (function_exists('log_message')) {
                log_message('error', 'Bol.com API Error: ' . $e->getMessage());
            }
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
     * Format single product from Marketing Catalog API v1 response
     */
    private function formatProduct($product)
    {
        // Extract product ID (bolProductId is the main identifier)
        $productId = $product['bolProductId'] ?? $product['id'] ?? '';
        
        // Extract best price from offerData
        $price = 0;
        if (isset($product['offerData']['bestOffer']['price'])) {
            $price = $product['offerData']['bestOffer']['price'];
        } elseif (isset($product['offerData']['offers'][0]['price'])) {
            $price = $product['offerData']['offers'][0]['price'];
        }
        
        // Extract image URL
        $imageUrl = '';
        if (isset($product['images'][0]['url'])) {
            $imageUrl = $product['images'][0]['url'];
        } elseif (isset($product['imageUrl'])) {
            $imageUrl = $product['imageUrl'];
        }
        
        // Extract product URL
        $productUrl = '';
        if (isset($product['urls'])) {
            foreach ($product['urls'] as $url) {
                if ($url['key'] === 'DESKTOP') {
                    $productUrl = $url['value'];
                    break;
                }
            }
        }
        
        return [
            'external_id' => $productId,
            'title' => $product['title'] ?? '',
            'description' => $product['shortDescription'] ?? $product['summary'] ?? '',
            'image_url' => $imageUrl,
            'price' => $price,
            'affiliate_url' => $this->generateAffiliateUrl($productId, $productUrl),
            'ean' => $product['ean'] ?? '',
            'source' => 'bol.com',
            'rating' => $product['rating'] ?? null,
            'specs_tag' => $product['specsTag'] ?? null,
        ];
    }

    /**
     * Generate affiliate URL using Bol.com Partner Platform format
     * Format: https://partner.bol.com/click/click?p=1&t=url&s={AFFILIATE_ID}&url={PRODUCT_URL}&f=TXL&name={NAME}
     */
    public function generateAffiliateUrl($productId, $productUrl = '')
    {
        // If no product URL provided, construct it
        if (empty($productUrl)) {
            $productUrl = "https://www.bol.com/nl/p/{$productId}/";
        }
        
        // If no affiliate ID, return direct product URL
        if (empty($this->affiliateId)) {
            return $productUrl;
        }

        // Generate proper affiliate tracking URL
        return "https://partner.bol.com/click/click?" . http_build_query([
            'p' => 1,
            't' => 'url',
            's' => $this->affiliateId,
            'url' => $productUrl,
            'f' => 'TXL',
            'name' => 'affiliate_link'
        ]);
    }
}
