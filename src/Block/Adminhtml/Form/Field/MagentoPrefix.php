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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Magento\Store\Model\ScopeInterface;

/**
 * @codeCoverageIgnore
 */
class MagentoPrefix extends Select
{
    private const PREFIX_CONFIG_PATH = 'customer/address/prefix_options';
    private const PREFIX_DELIMITER   = ';';

    /** @var Context */
    private $context;

    /**
     * @param Context               $context
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->context            = $context;
    }

    /**
     * @param string $value
     *
     * @return ShopgatePrefix
     */
    public function setInputName($value): MagentoPrefix
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->setName($value);
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
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
     * @throws LocalizedException
     */
    public function createOptions(): array
    {
        $list               = [];
        $scopePrefix        = $this->getPrefixesForScope();
        $configuredPrefixes = explode(static::PREFIX_DELIMITER, $scopePrefix);

        if (!is_array($configuredPrefixes)) {
            return $list;
        }

        foreach ($configuredPrefixes as $configuredPrefix) {
            $list[$configuredPrefix] = $configuredPrefix;
        }

        return $list;
    }

    /**
     * @return array
     */
    private function getPrefixesForScope(): string
    {
        if ($this->getRequest()->getParam(ScopeInterface::SCOPE_WEBSITE) !== null) {
            return $this->context->getScopeConfig()->getValue(
                static::PREFIX_CONFIG_PATH,
                ScopeInterface::SCOPE_WEBSITE,
                $this->getRequest()->getParam(ScopeInterface::SCOPE_WEBSITE)
            );
        }
        if ($this->getRequest()->getParam(ScopeInterface::SCOPE_STORE) !== null) {
            return $this->context->getScopeConfig()->getValue(
                static::PREFIX_CONFIG_PATH,
                ScopeInterface::SCOPE_STORE,
                $this->getRequest()->getParam('store')
            );
        }

        return $this->context->getScopeConfig()->getValue(static::PREFIX_CONFIG_PATH);
    }
}
