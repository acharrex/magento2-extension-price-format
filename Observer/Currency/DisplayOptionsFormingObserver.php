<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\PriceFormat\Observer\Currency;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Shopigo\PriceFormat\Helper\Data as DataHelper;

class DisplayOptionsFormingObserver implements ObserverInterface
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * Initialize dependencies
     *
     * @param DataHelper $dataHelper
     */
    public function __construct(
        DataHelper $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * Set currency options
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        if (!$this->dataHelper->isEnabled()) {
            return $this;
        }

        $event = $observer->getEvent();

        $currencyCode = $event->getBaseCode();
        if (!empty($currencyCode)) {
            $customOptions = $this->dataHelper->getConfigValue($currencyCode);
            if (!is_null($customOptions)) {
                $options = $event->getCurrencyOptions();
                $options->setData($customOptions + $options->getData());
            }
        }

        return $this;
    }
}
