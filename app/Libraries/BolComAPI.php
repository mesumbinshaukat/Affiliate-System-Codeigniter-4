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
     * 
     * @param string $query Search term
     * @param int $limit Number of results per page (max 50)
     * @param int $offset Offset for pagination
     * @param string $countryCode Country code (NL or BE)
     * @return array
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

            // Calculate page number from offset (API uses page-based pagination)
            $pageSize = min($limit, 50); // Max 50 per page
            $page = floor($offset / $pageSize) + 1;

            // Build URL with correct Marketing Catalog API v1 parameters
            $params = [
                'search-term' => $query,
                'country-code' => $countryCode,
                'page-size' => $pageSize,
                'page' => $page,
                'include-offer' => 'true',
                'include-image' => 'true',
                'include-rating' => 'true',
                'sort' => 'RELEVANCE'
            ];
            
            $url = $this->apiEndpoint . '/products/search?' . http_build_query($params);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Accept: application/json',
                'Accept-Language: nl',
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($httpCode === 200) {
                $data = json_decode($response, true);
                
                // Marketing Catalog API v1 returns 'results' array, not 'products'
                $results = $data['results'] ?? [];
                $totalResults = $data['totalResults'] ?? 0;
                
                // Log if no results for debugging
                if ($totalResults === 0 && function_exists('log_message')) {
                    log_message('info', "Bol.com API: No results for query '$query' in $countryCode");
                }
                
                return [
                    'success' => true,
                    'products' => $this->formatProducts($results),
                    'total' => $totalResults,
                    'page' => $page,
                    'page_size' => $pageSize,
                ];
            }

            // Handle specific error codes
            $errorMsg = "API request failed (HTTP $httpCode)";
            if ($httpCode === 401) {
                $errorMsg = 'Unauthorized - Check API credentials';
            } elseif ($httpCode === 406) {
                $errorMsg = 'Not Acceptable - Check Accept-Language header';
            } elseif ($httpCode === 429) {
                $errorMsg = 'Rate limit exceeded - Please wait before retrying';
            } elseif ($httpCode === 403) {
                $errorMsg = 'Forbidden - Check if affiliate account is active';
            } elseif ($httpCode === 400) {
                $errorMsg = 'Bad Request - Check parameters';
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
     * v1 structure: results[].product, results[].offer, results[].image, results[].rating
     */
    private function formatProduct($result)
    {
        // v1 API returns flat structure at root level
        // Fields: ean, bolProductId, url, title, description, image{url}, rating, offer{price}
        
        // Extract product ID (bolProductId is the main identifier in v1)
        $productId = $result['bolProductId'] ?? $result['id'] ?? '';
        
        // Extract EAN
        $ean = $result['ean'] ?? '';
        
        // Extract title and description
        $title = $result['title'] ?? '';
        $description = $result['description'] ?? $result['shortDescription'] ?? $result['summary'] ?? '';
        
        // Strip HTML tags from description
        $description = strip_tags($description);
        
        // Extract price from offer object (v1 structure)
        $price = 0;
        if (isset($result['offer']['price'])) {
            $price = $result['offer']['price'];
        }
        
        // Extract image URL from image object (v1 structure)
        $imageUrl = '';
        if (isset($result['image']['url'])) {
            $imageUrl = $result['image']['url'];
        }
        
        // Extract product URL (singular 'url' field at root level)
        $productUrl = $result['url'] ?? '';
        
        // If no URL found, construct from product ID
        if (empty($productUrl) && !empty($productId)) {
            $productUrl = "https://www.bol.com/nl/nl/p/{$productId}/";
        }
        
        // Extract rating (v1 structure - at root level)
        $ratingValue = $result['rating'] ?? null;
        
        return [
            'external_id' => $productId,
            'title' => $title,
            'description' => $description,
            'image_url' => $imageUrl,
            'price' => $price,
            'affiliate_url' => $this->generateAffiliateUrl($productId, $productUrl),
            'ean' => $ean,
            'source' => 'bol.com',
            'rating' => $ratingValue,
            'specs_tag' => $result['specsTag'] ?? null,
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
