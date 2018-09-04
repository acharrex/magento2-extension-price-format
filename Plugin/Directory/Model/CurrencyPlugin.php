<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\PriceFormat\Plugin\Directory\Model;

use Magento\Directory\Model\Currency;
use Shopigo\PriceFormat\Helper\Data as DataHelper;

class CurrencyPlugin
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
     * @param Currency $subject
     * @param float $price
     * @param array $options
     * @return string
     */
    public function beforeFormatTxt(Currency $subject, $price, $options = [])
    {
        if (!$this->dataHelper->isEnabled()) {
            return [$price, $options];
        }

        $currencyCode = $subject->getCode();
        if (!empty($currencyCode)) {
            $customOptions = $this->dataHelper->getConfigValue($currencyCode);
            if (!is_null($customOptions)) {
                $options = $customOptions + $options;
            }
        }
        return [$price, $options];
    }
}
