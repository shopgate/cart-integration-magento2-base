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

namespace Shopgate\Base\Model\Redirect\Route\Tags;

use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Shopgate\Base\Api\Config\CoreInterface;
use Shopgate\Base\Api\Config\SgCoreInterface;
use Shopgate_Helper_Redirect_TagsGenerator;

class Generic
{
    /** @var StoreManagerInterface */
    protected $storeManager;
    /** @var CoreInterface */
    protected $config;

    /**
     * @param CoreInterface         $config
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(CoreInterface $config, StoreManagerInterface $storeManager)
    {
        $this->config       = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * Generates a default set of tags
     *
     * @param string $pageTitle
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generate($pageTitle)
    {
        return [
            Shopgate_Helper_Redirect_TagsGenerator::SITE_PARAMETER_SITENAME       => $this->getSiteName(),
            Shopgate_Helper_Redirect_TagsGenerator::SITE_PARAMETER_DESKTOP_URL    => $this->getShopUrl(),
            Shopgate_Helper_Redirect_TagsGenerator::SITE_PARAMETER_MOBILE_WEB_URL => $this->getMobileUrl(),
            Shopgate_Helper_Redirect_TagsGenerator::SITE_PARAMETER_TITLE          => $pageTitle
        ];
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getSiteName()
    {
        return $this->storeManager->getWebsite()->getName();
    }

    /**
     * @return string
     */
    private function getShopUrl()
    {
        return $this->config->getConfigByPath(Store::XML_PATH_UNSECURE_BASE_URL)->getValue();
    }

    /**
     * todo-sg: this might need a fallback of some sort as merchant may not have defined it
     *
     * @return string
     */
    private function getMobileUrl()
    {
        $cname = $this->config->getConfigByPath(SgCoreInterface::PATH_CNAME)->getValue();

        return rtrim($cname, '/');
    }
}
