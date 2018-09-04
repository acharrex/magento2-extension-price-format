<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\PriceFormat\Plugin\Framework;

use Magento\Framework\Currency;
use Shopigo\PriceFormat\Helper\Data as DataHelper;

class CurrencyPlugin
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\App\ScopeResolverInterface
     */
    protected $scopeResolver;

    /**
     * Initialize dependencies
     *
     * @param DataHelper $dataHelper
     * @param \Magento\Framework\App\ScopeResolverInterface $scopeResolver
     */
    public function __construct(
        DataHelper $dataHelper,
        \Magento\Framework\App\ScopeResolverInterface $scopeResolver
    ) {
        $this->dataHelper = $dataHelper;
        $this->scopeResolver = $scopeResolver;
    }

    /**
     * After to currency plugin method in order to modify price format
     *
     * @param Currency $subject
     * @param \Closure $closure
     * @param int|float $value
     * @param array $options
     * @return string
     */
    public function aroundToCurrency(
        Currency $subject,
        \Closure $closure,
        $value = null,
        array $options = array()
    ) {
        if (!$this->dataHelper->isEnabled()) {
            $result = $closure($value, $options);
            return $result;
        }

        // Force the usage of the en_US locale in order to avoid formatting problem
        $options['locale'] = 'en_US';

        $result = $closure($value, $options);

        if (!empty($options['currency'])) {
            $currencyCode = $options['currency'];
        } else {
            $currencyCode = $this->scopeResolver->getScope()->getCurrentCurrencyCode();
        }

        if (!empty($currencyCode)) {
            return $this->dataHelper->postProcess($result, $currencyCode);
        }
        return $result;
    }
}
