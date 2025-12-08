<?php

namespace App\Libraries;

use App\Models\ClickModel;
use App\Models\ProductModel;

class AffiliateTracker
{
    protected $clickModel;
    protected $productModel;

    public function __construct()
    {
        $this->clickModel = new ClickModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Track a click and redirect to affiliate URL
     */
    public function trackAndRedirect($productId, $listId = null)
    {
        // Get product
        $product = $this->productModel->find($productId);

        if (!$product) {
            throw new \Exception('Product not found');
        }

        // Get list owner's user ID for commission attribution
        $userId = null;
        if ($listId) {
            $listModel = new \App\Models\ListModel();
            $list = $listModel->find($listId);
            if ($list) {
                $userId = $list['user_id']; // Attribute click to list owner
            }
        }

        // Log the click
        $this->clickModel->logClick($productId, $listId, $userId);

        // Return affiliate URL for redirect
        return $product['affiliate_url'];
    }

    /**
     * Generate tracking URL
     */
    public function generateTrackingUrl($productId, $listId = null)
    {
        $baseUrl = base_url('out/' . $productId);

        if ($listId) {
            $baseUrl .= '?list=' . $listId;
        }

        return $baseUrl;
    }

    /**
     * Get click statistics
     */
    public function getStats($startDate = null, $endDate = null)
    {
        return $this->clickModel->getClickStats($startDate, $endDate);
    }

    /**
     * Get list click count
     */
    public function getListClicks($listId)
    {
        return $this->clickModel->getListClicks($listId);
    }
}
