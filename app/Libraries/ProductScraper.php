<?php

namespace App\Libraries;

class ProductScraper
{
    public function scrapeFallback(string $url): array
    {
        $response = $this->fetchHtml($url);
        if (!$response['success']) {
            return $response;
        }

        $html = $response['html'];

        $product = $this->extractFromJsonLd($html);
        if (!$product) {
            $product = $this->extractFromMeta($html);
        }

        if (!$product) {
            return [
                'success' => false,
                'reason' => 'Kon geen productinformatie vinden in de HTML-structuur.',
            ];
        }

        $product['source'] = parse_url($url, PHP_URL_HOST) ?: 'scraper';
        $product['affiliate_url'] = $url;

        return [
            'success' => true,
            'data' => $product,
        ];
    }

    private function fetchHtml(string $url): array
    {
        $direct = $this->requestHtml($url);
        if ($direct['success']) {
            return $direct;
        }

        $blockingStatuses = [0, 401, 403, 429, 503];
        if (in_array($direct['status'], $blockingStatuses, true)) {
            $proxyUrl = 'https://r.jina.ai/http://' . preg_replace('#^https?://#', '', $url);
            $proxy = $this->requestHtml($proxyUrl, [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: nl,en;q=0.9',
            ]);

            if ($proxy['success']) {
                $proxy['via_proxy'] = true;
                return $proxy;
            }

            return [
                'success' => false,
                'reason' => $proxy['error'] ?: 'Proxy HTTP-status ' . $proxy['status'],
            ];
        }

        return [
            'success' => false,
            'reason' => $direct['error'] ?: 'HTTP-status ' . $direct['status'],
        ];
    }

    private function requestHtml(string $url, array $additionalHeaders = []): array
    {
        $ch = curl_init($url);
        $headers = array_merge([
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: nl,en;q=0.9',
        ], $additionalHeaders);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_ENCODING => '',
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36',
        ]);

        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($body === false || $status >= 400) {
            return [
                'success' => false,
                'status' => $status,
                'error' => $error,
            ];
        }

        return [
            'success' => true,
            'html' => $body,
            'status' => $status,
        ];
    }

    private function extractFromJsonLd(string $html): ?array
    {
        if (!preg_match_all('/<script[^>]+type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $matches)) {
            return null;
        }

        foreach ($matches[1] as $rawJson) {
            $rawJson = trim(html_entity_decode($rawJson, ENT_QUOTES | ENT_HTML5));
            $data = json_decode($rawJson, true);
            if (!$data) {
                continue;
            }

            $candidateList = $this->flattenJsonLd($data);
            foreach ($candidateList as $candidate) {
                if (($candidate['@type'] ?? '') === 'Product') {
                    return [
                        'title' => $candidate['name'] ?? '',
                        'description' => $candidate['description'] ?? '',
                        'image' => $this->normalizeImageField($candidate['image'] ?? ''),
                        'price' => $this->extractPriceFromJsonLd($candidate),
                    ];
                }
            }
        }

        return null;
    }

    private function flattenJsonLd($data): array
    {
        $result = [];
        if (isset($data['@graph']) && is_array($data['@graph'])) {
            foreach ($data['@graph'] as $item) {
                $result = array_merge($result, $this->flattenJsonLd($item));
            }
            return $result;
        }

        if (isset($data['@type']) && $data['@type'] === 'Product') {
            $result[] = $data;
        }

        if (is_array($data)) {
            foreach ($data as $value) {
                if (is_array($value)) {
                    $result = array_merge($result, $this->flattenJsonLd($value));
                }
            }
        }

        return $result;
    }

    private function normalizeImageField($image)
    {
        if (is_array($image)) {
            return $image[0] ?? '';
        }
        return $image;
    }

    private function extractPriceFromJsonLd(array $data): float
    {
        if (!empty($data['offers'])) {
            $offers = $data['offers'];
            if (isset($offers['price'])) {
                return (float) $offers['price'];
            }
            if (isset($offers[0]['price'])) {
                return (float) $offers[0]['price'];
            }
        }

        return 0.0;
    }

    private function extractFromMeta(string $html): ?array
    {
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        if (!$doc->loadHTML($html)) {
            libxml_clear_errors();
            return null;
        }
        libxml_clear_errors();

        $xpath = new \DOMXPath($doc);

        $getMeta = function (array $queries) use ($xpath) {
            foreach ($queries as $query) {
                $nodeList = $xpath->query($query);
                if ($nodeList && $nodeList->length > 0) {
                    $content = $nodeList->item(0)->getAttribute('content');
                    if (!empty($content)) {
                        return trim($content);
                    }
                }
            }
            return '';
        };

        $title = $getMeta([
            '//meta[@property="og:title"]',
            '//meta[@name="twitter:title"]',
            '//title',
        ]);

        $description = $getMeta([
            '//meta[@property="og:description"]',
            '//meta[@name="description"]',
        ]);

        $image = $getMeta([
            '//meta[@property="og:image"]',
            '//meta[@name="twitter:image"]',
        ]);

        $price = $getMeta([
            '//meta[@itemprop="price"]',
            '//span[@itemprop="price"]',
        ]);

        if (empty($title) && empty($description)) {
            return null;
        }

        return [
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'price' => $this->normalizePrice($price),
        ];
    }

    private function normalizePrice(?string $price): float
    {
        if (!$price) {
            return 0.0;
        }
        $normalized = preg_replace('/[^\d,\.]/', '', $price);
        $normalized = str_replace(',', '.', $normalized);
        return (float) $normalized;
    }
}
