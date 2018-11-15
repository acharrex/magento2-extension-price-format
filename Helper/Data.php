<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\PriceFormat\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Math\Random as MathRandom;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Config paths
     */
    const XML_PATH_ENABLED        = 'shopigo_priceformat/general/enabled';
    const XML_PATH_CURRENY_FORMAT = 'shopigo_priceformat/general/formats';

    /**
     * @var MathRandom
     */
    protected $mathRandom;

    /**
     * @param Context $context
     * @param MathRandom $mathRandom
     */
    public function __construct(
        Context $context,
        MathRandom $mathRandom
    ) {
        $this->mathRandom = $mathRandom;
        parent::__construct($context);
    }

    /**
     * Generate a storable representation of a value
     *
     * @param int|float|string|array $value
     * @return string
     */
    protected function serializeValue($value)
    {
        if (is_array($value)) {
            return serialize($value);
        } else {
            return '';
        }
    }

    /**
     * Create a value from a storable representation
     *
     * @param int|float|string $value
     * @return array
     */
    protected function unserializeValue($value)
    {
        if (is_string($value) && !empty($value)) {
            return unserialize($value);
        } else {
            return [];
        }
    }

    /**
     * Check whether value is in form retrieved by _encodeArrayFieldValue()
     *
     * @param string|array $value
     * @return bool
     */
    protected function isEncodedArrayFieldValue($value)
    {
        if (!is_array($value)) {
            return false;
        }
        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row)
                || !array_key_exists('currency', $row)
                || !array_key_exists('group', $row)
                || !array_key_exists('decimal', $row)
                || !array_key_exists('position', $row)
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Encode value to be used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function encodeArrayFieldValue(array $value)
    {
        $result = [];
        foreach ($value as $row) {
            $resultId = $this->mathRandom->getUniqueHash('_');
            $result[$resultId] = $row;
        }
        return $result;
    }

    /**
     * Decode value from used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function decodeArrayFieldValue(array $value)
    {
        $result = [];
        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row)
                || !array_key_exists('currency', $row)
                || !array_key_exists('group', $row)
                || !array_key_exists('decimal', $row)
                || !array_key_exists('position', $row)
            ) {
                continue;
            }
            $result[] = $row;
        }
        return $result;
    }

    /**
     * Check if enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        if (!$this->isModuleOutputEnabled()) {
            return false;
        }
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORES
        );
    }

    /**
     * Retrieve price format from config
     *
     * @param string $currencyCode
     * @return string|null
     */
    public function getConfigValue($currencyCode)
    {
        if (empty($currencyCode)) {
            return null;
        }

        $value = $this->scopeConfig->getValue(self::XML_PATH_CURRENY_FORMAT);
        if (empty($value)) {
            return null;
        }

        $value = $this->unserializeValue($value);
        if ($this->isEncodedArrayFieldValue($value)) {
            $value = $this->decodeArrayFieldValue($value);
        }

        $result = null;
        foreach ($value as $row) {
            if ($row['currency'] == $currencyCode) {
                $result = [
                    'group'    => $row['group'],
                    'decimal'  => $row['decimal'],
                    'position' => (int)$row['position']
                ];
                break;
            }
        }
        return $result;
    }

    /**
     * Make value readable by \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param string|array $value
     * @return array
     */
    public function makeArrayFieldValue($value)
    {
        $value = $this->unserializeValue($value);
        if (!$this->isEncodedArrayFieldValue($value)) {
            $value = $this->encodeArrayFieldValue($value);
        }
        return $value;
    }

    /**
     * Make value ready for store
     *
     * @param string|array $value
     * @return string
     */
    public function makeStorableArrayFieldValue($value)
    {
        if ($this->isEncodedArrayFieldValue($value)) {
            $value = $this->decodeArrayFieldValue($value);
        }
        $value = $this->serializeValue($value);
        return $value;
    }

    /**
     * Price post process
     *
     * @param string $price
     * @param string $currencyCode
     * @return string
     */
    public function postProcess($price, $currencyCode)
    {
        $currencyOptions = $this->getConfigValue($currencyCode);
        if (is_null($currencyOptions)) {
            return $price;
        }

        // Mapped symbols
        $map = ['decimal' => '.', 'group' => ','];

        // Replace each symbol by their code (e.g. "," will be replaced by "group")
        $price = strtr($price, array_flip($map));

        // Replace all codes by their symbol (e.g. "group" will be replaced by ",")
        $price = strtr($price, $currencyOptions);

        return $price;
    }
}
