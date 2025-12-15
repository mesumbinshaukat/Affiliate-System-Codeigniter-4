<?php

namespace App\Controllers;

use App\Libraries\AffiliateTracker;

class Tracker extends BaseController
{
    public function redirect($productId)
    {
        $listId = $this->request->getGet('list');
        $subId = $this->request->getGet('subId');

        try {
            $tracker = new AffiliateTracker();
            $affiliateUrl = $tracker->trackAndRedirect($productId, $listId, $subId);

            return redirect()->to($affiliateUrl);
        } catch (\Exception $e) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }
}
