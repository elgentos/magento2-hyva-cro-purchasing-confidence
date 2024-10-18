<?php

/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */

declare(strict_types=1);

namespace Elgentos\HyvaCROPurchasingConfidence\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ResourceConnection;

class UpdateAverageReturnPercentage
{
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly WriterInterface $configWriter
    ) {
    }

    public function execute(): static
    {
        $this->configWriter->save('hyva_cro/purchasing_confidence/average_return_percentage', $this->calculateAverageReturnPercentage());

        return $this;
    }

    public function calculateAverageReturnPercentage(): float
    {
        $salesThreshold = $this->scopeConfig->getValue('hyva_cro/purchasing_confidence/sales_threshold');

        // Get tables names
        $salesTable = $this->resourceConnection->getTableName('sales_order_item');
        $returnsTable = $this->resourceConnection->getTableName('sales_creditmemo_item');

        //Get connection
        $connection = $this->resourceConnection->getConnection();

        $subSelectSales = $connection->select()
            ->from(
                ['so' => $salesTable],
                [
                    'product_id',
                    'total_sales' => new \Zend_Db_Expr('COUNT(item_id)')
                ]
            )
            ->group('product_id');

        $subSelectReturns = $connection->select()
            ->from(
                ['sci' => $returnsTable],
                [
                    'product_id',
                    'total_returns' => new \Zend_Db_Expr('COUNT(entity_id)')
                ]
            )
            ->group('product_id');

        $averageReturnPercentage = $connection->select()
            ->from(
                ['sst' => $subSelectSales],
                [
                    'average_return_percentage' => new \Zend_Db_Expr('AVG((trt.total_returns / sst.total_sales) * 100)')
                ]
            )
            ->joinLeft(
                ['trt' => $subSelectReturns],
                'sst.product_id = trt.product_id',
                []
            )
            ->where('sst.total_sales >= ?', $salesThreshold)
            ->query()
            ->fetchColumn();

        return (float) $averageReturnPercentage;
    }
}
