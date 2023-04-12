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
class GenderMap extends AbstractFieldArray
{
    public const INPUT_ID_GENDER_SHOPGATE = 'gender_shopgate';
    public const INPUT_ID_GENDER_MAGENTO  = 'gender_magento';

    /** @var MagentoGender */
    protected $magentoGender;
    /** @var ShopgateGender */
    protected $shopgateGender;

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    protected function _prepareToRender(): void // phpcs:ignore
    {
        $this->addColumn(
            self::INPUT_ID_GENDER_SHOPGATE,
            ['label' => __('Shopgate Gender'), 'renderer' => $this->getShopgateGenderRenderer()]
        );
        $this->addColumn(
            self::INPUT_ID_GENDER_MAGENTO,
            ['label' => __('Magento Gender'), 'renderer' => $this->getMagentoGenderRenderer()]
        );
        $this->_addAfter = false;
    }

    /**
     * @return ShopgateGender
     * @throws LocalizedException
     */
    private function getShopgateGenderRenderer(): ShopgateGender
    {
        if (!$this->shopgateGender) {
            $this->shopgateGender = $this->getLayout()->createBlock(
                ShopgateGender::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->shopgateGender;
    }

    /**
     * @return MagentoGender
     * @throws LocalizedException
     */
    private function getMagentoGenderRenderer(): MagentoGender
    {
        if (!$this->magentoGender) {
            $this->magentoGender = $this->getLayout()->createBlock(
                MagentoGender::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->magentoGender;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void // phpcs:ignore
    {
        $options         = [];
        $customAttribute = $row->getData(self::INPUT_ID_GENDER_SHOPGATE);

        if ($customAttribute) {
            $key           = 'option_' . $this->getShopgateGenderRenderer()->calcOptionHash($customAttribute);
            $options[$key] = 'selected="selected"';

            $options['option_' . $this->getMagentoGenderRenderer()->calcOptionHash(
                $row->getData(self::INPUT_ID_GENDER_MAGENTO)
            )] = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }
}
