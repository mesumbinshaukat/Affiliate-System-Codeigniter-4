<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\BolComAPI;
use App\Models\SalesModel;

class FetchBolReports extends BaseCommand
{
    protected $group       = 'Affiliate';
    protected $name        = 'fetch:bol-reports';
    protected $description = 'Fetch Bol.com commission reports and store sales data';
    protected $usage       = 'php spark fetch:bol-reports [options]';
    protected $arguments   = [];
    protected $options     = [
        '--days' => 'Number of days to fetch (default: 30)',
        '--start-date' => 'Start date in YYYY-MM-DD format (overrides --days)',
        '--end-date' => 'End date in YYYY-MM-DD format (default: today)',
        '--dry-run' => 'Show what would be done without saving',
    ];

    public function run(array $params)
    {
        try {
            // Parse options
            $days = (int) ($params['days'] ?? 30);
            $startDate = $params['start-date'] ?? null;
            $endDate = $params['end-date'] ?? date('Y-m-d');
            $dryRun = isset($params['dry-run']);

            // Calculate date range
            if (empty($startDate)) {
                $startDate = date('Y-m-d', strtotime("-{$days} days"));
            }

            // Validate dates
            if (!$this->isValidDate($startDate) || !$this->isValidDate($endDate)) {
                CLI::error('Invalid date format. Use YYYY-MM-DD');
                return;
            }

            if (strtotime($startDate) > strtotime($endDate)) {
                CLI::error('Start date must be before end date');
                return;
            }

            CLI::write('Fetching Bol.com reports...', 'green');
            CLI::write("Date range: {$startDate} to {$endDate}", 'yellow');
            if ($dryRun) {
                CLI::write('[DRY RUN MODE - No data will be saved]', 'red');
            }
            CLI::newLine();

            // Initialize API and models
            $bolApi = new BolComAPI();
            $salesModel = new SalesModel();

            // Fetch commission report
            CLI::write('Fetching commission report...', 'cyan');
            $commissionResult = $bolApi->getCommissionReport($startDate, $endDate);

            if (!$commissionResult['success']) {
                CLI::error('Failed to fetch commission report: ' . $commissionResult['message']);
                return;
            }

            $commissions = $commissionResult['data'] ?? [];
            $commissionCount = count($commissions);
            CLI::write("Found {$commissionCount} commission records", 'green');

            // Process commission data
            $processed = 0;
            $skipped = 0;
            $errors = 0;

            foreach ($commissions as $commission) {
                try {
                    // Extract required fields
                    $orderId = $commission['orderId'] ?? null;
                    $subId = $commission['subId'] ?? null;
                    $commissionAmount = (float) ($commission['commission'] ?? 0);
                    $status = $commission['status'] ?? 'pending';

                    if (empty($orderId) || empty($subId)) {
                        CLI::write("  ⚠ Skipping: Missing orderId or subId", 'yellow');
                        $skipped++;
                        continue;
                    }

                    // Check if order already exists
                    if ($salesModel->orderExists($orderId)) {
                        CLI::write("  ℹ Order {$orderId} already exists, skipping", 'blue');
                        $skipped++;
                        continue;
                    }

                    // Extract user_id and list_id from subId
                    $ids = $salesModel->extractIdsFromSubId($subId);
                    if (!$ids) {
                        CLI::write("  ⚠ Invalid subId format: {$subId}", 'yellow');
                        $skipped++;
                        continue;
                    }

                    // Prepare sale data
                    $saleData = [
                        'sub_id' => $subId,
                        'order_id' => $orderId,
                        'commission' => $commissionAmount,
                        'status' => $status,
                        'user_id' => $ids['user_id'],
                        'list_id' => $ids['list_id'],
                    ];

                    // Add optional fields
                    if (isset($commission['productId'])) {
                        $saleData['product_id'] = $commission['productId'];
                    }
                    if (isset($commission['quantity'])) {
                        $saleData['quantity'] = (int) $commission['quantity'];
                    }
                    if (isset($commission['priceInclVat'])) {
                        $saleData['revenue_excl_vat'] = (float) $commission['priceInclVat'];
                    }

                    // Insert or update
                    if (!$dryRun) {
                        if ($salesModel->insert($saleData)) {
                            CLI::write("  ✓ Processed order {$orderId} (commission: €{$commissionAmount})", 'green');
                            $processed++;
                        } else {
                            CLI::write("  ✗ Failed to insert order {$orderId}", 'red');
                            $errors++;
                        }
                    } else {
                        CLI::write("  [DRY] Would process order {$orderId} (commission: €{$commissionAmount})", 'cyan');
                        $processed++;
                    }

                } catch (\Exception $e) {
                    CLI::write("  ✗ Error processing commission: " . $e->getMessage(), 'red');
                    $errors++;
                }
            }

            // Summary
            CLI::newLine();
            CLI::write('=== Report Summary ===', 'green');
            CLI::write("Total records: {$commissionCount}", 'white');
            CLI::write("Processed: {$processed}", 'green');
            CLI::write("Skipped: {$skipped}", 'yellow');
            CLI::write("Errors: {$errors}", $errors > 0 ? 'red' : 'green');

            if ($dryRun) {
                CLI::newLine();
                CLI::write('[DRY RUN COMPLETE - No data was saved]', 'red');
            }

            // Log summary
            if (function_exists('log_message')) {
                log_message('info', "Bol.com reports fetched: {$processed} processed, {$skipped} skipped, {$errors} errors");
            }

            CLI::newLine();
            CLI::write('Done!', 'green');

        } catch (\Exception $e) {
            CLI::error('Command error: ' . $e->getMessage());
            if (function_exists('log_message')) {
                log_message('error', 'FetchBolReports command error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Validate date format
     */
    private function isValidDate($date)
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) && strtotime($date) !== false;
    }
}
