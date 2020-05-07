<?php

/**
 * Copyright Shopgate Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    Shopgate Inc, 804 Congress Ave, Austin, Texas 78701 <interfaces@shopgate.com>
 * @copyright Shopgate Inc
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace Shopgate\Base\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

/**
 * @codeCoverageIgnore
 */
class PrefixMap extends AbstractFieldArray
{
    public const INPUT_ID_PREFIX_SHOPGATE = 'prefix_shopgate';
    public const INPUT_ID_PREFIX_MAGENTO  = 'prefix_magento';

    /** @var MagentoPrefix */
    protected $magentoPrefix;
    /** @var ShopgatePrefix */
    protected $shopgatePrefix;

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    protected function _prepareToRender(): void // phpcs:ignore
    {
        $this->addColumn(
            self::INPUT_ID_PREFIX_SHOPGATE,
            ['label' => __('Shopgate Prefix'), 'renderer' => $this->getShopgatePrefixRenderer()]
        );
        $this->addColumn(
            self::INPUT_ID_PREFIX_MAGENTO,
            ['label' => __('Magento Prefix'), 'renderer' => $this->getMagentoPrefixRenderer()]
        );
        $this->_addAfter = false;
    }

    /**
     * @return MagentoPrefix
     * @throws LocalizedException
     */
    private function getMagentoPrefixRenderer(): MagentoPrefix
    {
        if (!$this->magentoPrefix) {
            $this->magentoPrefix = $this->getLayout()->createBlock(
                MagentoPrefix::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->magentoPrefix;
    }

    /**
     * @return ShopgatePrefix
     * @throws LocalizedException
     */
    private function getShopgatePrefixRenderer(): ShopgatePrefix
    {
        if (!$this->shopgatePrefix) {
            $this->shopgatePrefix = $this->getLayout()->createBlock(
                ShopgatePrefix::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->shopgatePrefix;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void // phpcs:ignore
    {
        $options         = [];
        $customAttribute = $row->getData(self::INPUT_ID_PREFIX_SHOPGATE);

        if ($customAttribute) {
            $key           = 'option_' . $this->getShopgatePrefixRenderer()->calcOptionHash($customAttribute);
            $options[$key] = 'selected="selected"';

            $options['option_' . $this->getMagentoPrefixRenderer()->calcOptionHash(
                $row->getData(self::INPUT_ID_PREFIX_MAGENTO)
            )] = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }
}
