<?php

/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */

declare(strict_types=1);

namespace Elgentos\HyvaCROPurchasingConfidence\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;

class AverageReturnPercentage extends Field
{
    protected $_scopeConfig;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_scopeConfig = $scopeConfig;
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        // Get the config value
        $configValue = $this->_scopeConfig->getValue('hyva_cro/purchasing_confidence/average_return_percentage');

        return '<p>'. __('The average return percentage is: %1%', $configValue) .'</p>';
    }
}
