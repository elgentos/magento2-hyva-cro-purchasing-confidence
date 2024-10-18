<?php

/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */

declare(strict_types=1);

namespace Elgentos\HyvaCROPurchasingConfidence\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ReturnPercentageViewModel implements ArgumentInterface
{
    public function __construct(private readonly ScopeConfigInterface $scopeConfig)
    {
    }

    public function getReturnPercentageThreshold(): int
    {
        return (int) $this->scopeConfig->getValue('hyva_cro/purchasing_confidence/return_percentage_threshold');
    }
}
