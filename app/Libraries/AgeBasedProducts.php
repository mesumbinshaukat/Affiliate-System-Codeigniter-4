<?php

namespace App\Libraries;

class AgeBasedProducts
{
    private $productsFile;
    private $productsData = null;

    public function __construct()
    {
        $this->productsFile = FCPATH . 'age_based_products.json';
    }

    /**
     * Load products from JSON file
     */
    private function loadProducts()
    {
        if ($this->productsData !== null) {
            return;
        }

        if (!file_exists($this->productsFile)) {
            $this->productsData = ['products_by_age_range' => []];
            return;
        }

        $json = file_get_contents($this->productsFile);
        $this->productsData = json_decode($json, true) ?? ['products_by_age_range' => []];
    }

    /**
     * Get age range for user age
     */
    private function getAgeRange($age)
    {
        if ($age === null) {
            return null;
        }

        if ($age >= 18 && $age < 25) {
            return '18-25';
        } elseif ($age >= 25 && $age < 35) {
            return '25-35';
        } elseif ($age >= 35 && $age < 50) {
            return '35-50';
        } elseif ($age >= 50 && $age < 65) {
            return '50-65';
        } elseif ($age >= 65) {
            return '65+';
        }

        return null;
    }

    /**
     * Get random products for user age
     * @param int $age User age
     * @param int $limit Number of products to return
     * @return array Products array
     */
    public function getProductsForAge($age = null, $limit = 6)
    {
        $this->loadProducts();

        if ($age === null) {
            // Return random products from all ranges
            return $this->getRandomProducts($limit);
        }

        $ageRange = $this->getAgeRange($age);

        if ($ageRange === null) {
            // Age out of range, return random products
            return $this->getRandomProducts($limit);
        }

        $rangeProducts = $this->productsData['products_by_age_range'][$ageRange] ?? [];

        if (empty($rangeProducts)) {
            // No products for this range, try adjacent ranges
            return $this->getProductsFromAdjacentRanges($ageRange, $limit);
        }

        // Shuffle and return random products from this range
        shuffle($rangeProducts);
        return array_slice($rangeProducts, 0, $limit);
    }

    /**
     * Get random products from all ranges
     */
    private function getRandomProducts($limit = 6)
    {
        $this->loadProducts();

        $allProducts = [];
        foreach ($this->productsData['products_by_age_range'] as $products) {
            $allProducts = array_merge($allProducts, $products);
        }

        if (empty($allProducts)) {
            return [];
        }

        shuffle($allProducts);
        return array_slice($allProducts, 0, $limit);
    }

    /**
     * Get products from adjacent age ranges if primary range is empty
     */
    private function getProductsFromAdjacentRanges($primaryRange, $limit)
    {
        $ageRanges = ['18-25', '25-35', '35-50', '50-65', '65+'];
        $primaryIndex = array_search($primaryRange, $ageRanges);

        $products = [];

        // Try adjacent ranges in order of proximity
        for ($i = 1; $i < count($ageRanges); $i++) {
            // Try next range
            if ($primaryIndex + $i < count($ageRanges)) {
                $adjacentRange = $ageRanges[$primaryIndex + $i];
                $rangeProducts = $this->productsData['products_by_age_range'][$adjacentRange] ?? [];
                $products = array_merge($products, $rangeProducts);
            }

            // Try previous range
            if ($primaryIndex - $i >= 0) {
                $adjacentRange = $ageRanges[$primaryIndex - $i];
                $rangeProducts = $this->productsData['products_by_age_range'][$adjacentRange] ?? [];
                $products = array_merge($products, $rangeProducts);
            }

            if (count($products) >= $limit) {
                break;
            }
        }

        if (empty($products)) {
            return $this->getRandomProducts($limit);
        }

        shuffle($products);
        return array_slice($products, 0, $limit);
    }

    /**
     * Get all age ranges with product counts
     */
    public function getAgeRangeStats()
    {
        $this->loadProducts();

        $stats = [];
        foreach ($this->productsData['products_by_age_range'] as $range => $products) {
            $stats[$range] = count($products);
        }

        return $stats;
    }

    /**
     * Check if products file exists and has data
     */
    public function hasProducts()
    {
        $this->loadProducts();
        return !empty($this->productsData['products_by_age_range']);
    }
}
