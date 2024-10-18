<?php

/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */

declare(strict_types=1);

namespace Elgentos\HyvaCROPurchasingConfidence\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\Product\Action as ProductAction;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class UpdateProductReturnPercentage
{
    public function __construct(
        private readonly CollectionFactory $productCollectionFactory,
        private readonly ProductAction $productAction,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly ResourceConnection $resourceConnection
    ) {
    }

    public function execute()
    {
        $groupedReturnPercentages = [];

        foreach($this->getReturnPercentages() as $product => $percentage) {
            if (!array_key_exists($percentage, $groupedReturnPercentages)) {
                $groupedReturnPercentages[$percentage] = [];
            }
            $groupedReturnPercentages[$percentage][] = $product;
        }

        foreach ($groupedReturnPercentages as $returnPercentage => $productIds) {
            $this->productAction->updateAttributes($productIds, ['return_percentage' => $returnPercentage], 0);
        }

        return $this;
    }

    public function getReturnPercentages()
    {
        $salesThreshold = $this->scopeConfig->getValue('hyva_cro/purchasing_confidence/return_percentage_threshold');
        $connection = $this->resourceConnection->getConnection();

        $salesTableName = $connection->getTableName('sales_order_item');
        $returnsTableName = $connection->getTableName('sales_creditmemo_item');
        $productTableName = $connection->getTableName('catalog_product_entity');

        // Start select statement
        $select = $connection->select()
            ->from(['sales' => $salesTableName], 'sales.product_id')
            ->joinLeft(
                ['returns' => $returnsTableName],
                'sales.product_id = returns.product_id',
                []
            )
            ->joinInner(
                ['products' => $productTableName],
                'sales.product_id = products.entity_id',
                []
            )
            ->columns(
                [
                    'return_percentage' => new \Zend_Db_Expr(
                        'ROUND((COUNT(returns.entity_id) / COUNT(sales.item_id)) * 100)'
                    )
                ]
            )
            ->group('sales.product_id')
            ->having('COUNT(sales.item_id) > ?', $salesThreshold)
            ->order('return_percentage DESC');

        return $connection->fetchPairs($select);
    }
}
