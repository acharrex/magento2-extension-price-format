<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\PriceFormat\Plugin\Framework\Locale;

use Magento\Framework\Locale\Format;
use Shopigo\PriceFormat\Helper\Data as DataHelper;

class FormatPlugin
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
     * Set currency options
     *
     * @param Format $subject
     * @param \Closure $closure
     * @param string $localeCode Locale code
     * @param string $currencyCode Currency code
     * @return array
     */
    public function aroundGetPriceFormat(
        Format $subject,
        \Closure $closure,
        $localeCode = null,
        $currencyCode = null
    ) {
        $result = $closure($localeCode, $currencyCode);

        if (!$this->dataHelper->isEnabled()) {
            return $result;
        }

        if (!$currencyCode) {
            $currencyCode = $this->scopeResolver->getScope()->getCurrentCurrencyCode();
        }

        if (!empty($currencyCode)) {
            $customOptions = $this->dataHelper->getConfigValue($currencyCode);
            if (!is_null($customOptions)) {
                $result = [
                    'groupSymbol'   => $customOptions['group'],
                    'decimalSymbol' => $customOptions['decimal'],
                    'position'      => $customOptions['position']
                ] + $result;
            }
        }
        return $result;
    }
}
