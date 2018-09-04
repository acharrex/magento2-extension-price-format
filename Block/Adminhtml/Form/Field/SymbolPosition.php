<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\PriceFormat\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select as HtmlSelect;

class SymbolPosition extends HtmlSelect
{
    /**
     * Positions cache
     *
     * @var array
     */
    protected $positions;

    /**
     * Retrieve allowed positions
     *
     * @param int $position
     * @return array|string
     */
    protected function getPositions($position = null)
    {
        if ($this->positions === null) {
            $this->positions = [
                \Zend_Currency::STANDARD => __('Default'),
                \Zend_Currency::LEFT     => __('Left'),
                \Zend_Currency::RIGHT    => __('Right'),
            ];
        }
        if ($position !== null) {
            return isset($this->positions[$position]) ? $this->positions[$position] : null;
        }
        return $this->positions;
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
            foreach ($this->getPositions() as $code => $label) {
                $this->addOption($code, addslashes($label));
            }
        }
        return parent::_toHtml();
    }
}
