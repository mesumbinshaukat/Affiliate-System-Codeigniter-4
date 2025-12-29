<?php

namespace App\Controllers;

use App\Libraries\AffiliateTracker;

class Tracker extends BaseController
{
    public function redirect($productId)
    {
        $listId = $this->request->getGet('list');
        $listProductId = $this->request->getGet('lp');
        $subId = $this->request->getGet('subId');

        // Generate subId from list and list_product if not provided
        if (!$subId && $listId && $listProductId) {
            $subId = $this->encodeSubId($listId, $listProductId);
        }

        try {
            $tracker = new AffiliateTracker();
            $affiliateUrl = $tracker->trackAndRedirect($productId, $listId, $subId);

            return redirect()->to($affiliateUrl);
        } catch (\Exception $e) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    private function encodeSubId($listId, $listProductId)
    {
        // Encode list_id and list_product_id into subId for Bol.com tracking
        // Format: L{listId}P{listProductId}
        return 'L' . $listId . 'P' . $listProductId;
    }

    public static function decodeSubId($subId)
    {
        // Decode subId back to list_id and list_product_id
        // Format: L{listId}P{listProductId}
        if (preg_match('/^L(\d+)P(\d+)$/', $subId, $matches)) {
            return [
                'list_id' => (int)$matches[1],
                'list_product_id' => (int)$matches[2],
            ];
        }
        return null;
    }
}
