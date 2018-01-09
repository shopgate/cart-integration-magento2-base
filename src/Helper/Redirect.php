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

namespace Shopgate\Base\Helper;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\Model\Context as LegacyContext;
use Shopgate\Base\Api\Config\CoreInterface;
use Shopgate\Base\Api\Config\SgCoreInterface;

class Redirect
{
    /** @var LegacyContext */
    private $legacyContext;
    /** @var SgCoreInterface */
    private $sgConfig;
    /** @var Context */
    private $context;
    /** @var CoreInterface */
    private $coreConfig;

    /**
     * @param LegacyContext   $legacyContext
     * @param CoreInterface   $coreConfig
     * @param SgCoreInterface $sgConfig
     * @param Context         $context
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        LegacyContext $legacyContext,
        CoreInterface $coreConfig,
        SgCoreInterface $sgConfig,
        Context $context
    ) {
        $this->legacyContext = $legacyContext;
        $this->sgConfig      = $sgConfig;
        $this->context       = $context;
        $this->coreConfig    = $coreConfig;
    }

    /**
     * Redirect true if
     * 1) Not admin page request
     * 2) Not an ajax request
     * 3) SG Config is valid (active, api_key, shop_number, customer_number)
     * 4) SG Module enabled for this store
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isAllowed()
    {
        /** @var \Magento\Framework\App\Request\Http $req */
        $req = $this->context->getRequest();

        if ($this->legacyContext->getAppState()->getAreaCode() !== FrontNameResolver::AREA_CODE
            && !$req->isAjax()
            && $this->sgConfig->isValid()
        ) {
            return true;
        }

        return false;
    }

    /**
     * Checks if setting for JavaScript type redirect is set
     * in core_config_data table
     *
     * @return bool
     */
    public function isTypeJavaScript()
    {
        $redirectType = $this->coreConfig->getConfigByPath(SgCoreInterface::PATH_REDIRECT_TYPE);

        return $redirectType->getValue() === SgCoreInterface::VALUE_REDIRECT_JS;
    }
}
