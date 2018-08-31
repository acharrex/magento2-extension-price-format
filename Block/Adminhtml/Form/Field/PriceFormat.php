<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\PriceFormat\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Zend\I18n\Translator\Plural\Symbol;

class PriceFormat extends AbstractFieldArray
{
    /**
     * @var Currency
     */
    protected $currencyRenderer;

    /**
     * @var SymbolPosition
     */
    protected $symbolPositionRenderer;

    /**
     * Retrieve currency column renderer
     *
     * @return Currency
     */
    protected function getCurrencyRenderer()
    {
        if (!$this->currencyRenderer) {
            $this->currencyRenderer = $this->getLayout()->createBlock(
                \Shopigo\PriceFormat\Block\Adminhtml\Form\Field\Currency::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->currencyRenderer->setExtraParams('style="width: 150px;"');
            $this->currencyRenderer->setClass('locale_select');
        }
        return $this->currencyRenderer;
    }

    /**
     * Retrieve symbol position column renderer
     *
     * @return SymbolPosition
     */
    protected function getSymbolPositionRenderer()
    {
        if (!$this->symbolPositionRenderer) {
            $this->symbolPositionRenderer = $this->getLayout()->createBlock(
                'Shopigo\PriceFormat\Block\Adminhtml\Form\Field\SymbolPosition',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->symbolPositionRenderer->setExtraParams('style="width: 100px;"');
            $this->symbolPositionRenderer->setClass('format_type_select');
        }
        return $this->symbolPositionRenderer;
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'currency',
            [
                'label'    => __('Currency'),
                'renderer' => $this->getCurrencyRenderer(),
            ]
        );

        $this->addColumn(
            'group',
            [
                'label'    => __('Thousand separator'),
            ]
        );

        $this->addColumn(
            'decimal',
            [
                'label'    => __('Decimal separator'),
            ]
        );

        $this->addColumn(
            'position',
            [
                'label'    => __('Symbol position'),
                'renderer' => $this->getSymbolPositionRenderer(),
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Format');
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->getCurrencyRenderer()->calcOptionHash($row->getData('currency'))] =
            'selected="selected"';
        $optionExtraAttr['option_' . $this->getSymbolPositionRenderer()->calcOptionHash($row->getData('position'))] =
            'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
}
