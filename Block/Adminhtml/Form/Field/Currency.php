<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\PriceFormat\Block\Adminhtml\Form\Field;

use Magento\Framework\Locale\ListsInterface as LocaleLists;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select as HtmlSelect;

class Currency extends HtmlSelect
{
    /**
     * @var LocaleLists
     */
    protected $localeLists;

    /**
     * Currencies cache
     *
     * @var array
     */
    protected $currencies;

    /**
     * Construct
     *
     * @param Context $context
     * @param LocaleLists $localeLists
     * @param array $data
     */
    public function __construct(
        Context $context,
        LocaleLists $localeLists,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->localeLists = $localeLists;
    }

    /**
     * Retrieve allowed currencies
     *
     * @param string $currencyCode
     * @return array|string
     */
    protected function getCurrencies($currencyCode = null)
    {
        if ($this->currencies === null) {
            $this->currencies = [];

            foreach ($this->localeLists->getOptionCurrencies() as $currency) {
                $this->currencies[$currency['value']] = $currency['label'];
            }
        }
        if ($currencyCode !== null) {
            return isset($this->currencies[$currencyCode]) ? $this->currencies[$currencyCode] : null;
        }
        return $this->currencies;
    }

    /**
     * Set input name
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->getCurrencies() as $code => $label) {
                $this->addOption($code, addslashes($label));
            }
        }
        return parent::_toHtml();
    }
}
