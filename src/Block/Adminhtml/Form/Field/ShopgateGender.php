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

declare(strict_types=1);

namespace Shopgate\Base\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use ShopgateCustomer;

/**
 * @codeCoverageIgnore
 */
class ShopgateGender extends Select
{
    /**
     * @param string $value
     *
     * @return ShopgateGender
     */
    public function setInputName($value): ShopgateGender
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->setName($value);
    }

    /**
     * @return string
     */
    public function _toHtml(): string // phpcs:ignore
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->createOptions());
        }

        return parent::_toHtml();
    }

    /**
     * @return array
     */
    public function createOptions(): array
    {
        return [
            ShopgateCustomer::MALE   => ['value' => ShopgateCustomer::MALE, 'label' => __('Male')],
            ShopgateCustomer::FEMALE => ['value' => ShopgateCustomer::FEMALE, 'label' => __('Female')],
            ShopgateCustomer::DIVERSE => ['value' => ShopgateCustomer::DIVERSE, 'label' => __('Not Specified')] // default magento translations
        ];
    }
}
