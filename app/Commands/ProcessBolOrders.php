<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\BolComAPI;
use App\Models\SaleModel;
use App\Models\ListProductModel;
use App\Controllers\Tracker;

class ProcessBolOrders extends BaseCommand
{
    protected $group = 'Affiliate';
    protected $name = 'affiliate:process-orders';
    protected $description = 'Process Bol.com order reports and auto-claim purchased items';

    public function run(array $params)
    {
        CLI::write('Processing Bol.com order reports...', 'yellow');

        $bolApi = new BolComAPI();
        $saleModel = new SaleModel();
        $listProductModel = new ListProductModel();

        // Fetch recent orders from Bol.com API (last 30 days)
        $startDate = date('Y-m-d', strtotime('-30 days'));
        $endDate = date('Y-m-d');

        try {
            $orders = $bolApi->getOrderReports($startDate, $endDate);

            if (empty($orders)) {
                CLI::write('No orders found in the specified period.', 'green');
                return;
            }

            $claimedCount = 0;
            $processedCount = 0;

            foreach ($orders as $order) {
                $processedCount++;
                $subId = $order['sub_id'] ?? null;

                if (!$subId) {
                    continue;
                }

                // Try to decode subId to get list_product_id
                $decoded = Tracker::decodeSubId($subId);

                if (!$decoded) {
                    // Legacy format or manual claim - skip
                    continue;
                }

                $listId = $decoded['list_id'];
                $listProductId = $decoded['list_product_id'];

                // Check if this list_product exists and is not already claimed
                $listProduct = $listProductModel->find($listProductId);

                if (!$listProduct || $listProduct['list_id'] != $listId) {
                    CLI::write("  - Invalid list_product_id: {$listProductId}", 'red');
                    continue;
                }

                if (!empty($listProduct['claimed_at'])) {
                    // Already claimed
                    continue;
                }

                // Claim the product
                if ($listProductModel->claimProduct($listProductId, $subId)) {
                    $claimedCount++;
                    CLI::write("  ✓ Claimed product {$listProductId} from list {$listId} (subId: {$subId})", 'green');

                    // Log for analytics
                    log_message('info', sprintf(
                        'Product auto-claimed from order: list_id=%d, list_product_id=%d, subId=%s, order_id=%s',
                        $listId,
                        $listProductId,
                        $subId,
                        $order['order_id'] ?? 'unknown'
                    ));
                }
            }

            CLI::write("\nProcessing complete:", 'yellow');
            CLI::write("  - Orders processed: {$processedCount}", 'white');
            CLI::write("  - Items claimed: {$claimedCount}", 'green');

        } catch (\Exception $e) {
            CLI::error('Error processing orders: ' . $e->getMessage());
            log_message('error', 'ProcessBolOrders command failed: ' . $e->getMessage());
        }
    }
}
