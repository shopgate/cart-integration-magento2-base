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

use Magento\Framework\App\Config\Value;
use Magento\Framework\App\RequestInterface;
use Shopgate\Base\Api\Config\CoreInterface;
use Shopgate\Base\Api\Config\SgCoreInterface;

class Config
{

    /** @var CoreInterface */
    private $coreConfig;
    /** @var SgCoreInterface */
    private $sgCoreConfig;
    /** @var RequestInterface */
    private $request;

    /**
     * @param CoreInterface   $coreConfig
     * @param SgCoreInterface $sgCore
     */
    public function __construct(CoreInterface $coreConfig, SgCoreInterface $sgCore, RequestInterface $request)
    {
        $this->coreConfig   = $coreConfig;
        $this->sgCoreConfig = $sgCore;
        $this->request      = $request;
    }

    /**
     * Loads all core_config_data paths with "undefined" in them
     * so that we can load up the configuration properly
     *
     * @return array $list - list of undefined keys and core_config_paths to them
     */
    public function loadUndefinedConfigPaths(): array
    {
        $list       = [];
        $collection = $this->coreConfig->getCollectionByPath(SgCoreInterface::PATH_UNDEFINED . '%');
        /** @var Value $item */
        foreach ($collection as $item) {
            $path       = explode('/', $item->getPath());
            $key        = array_pop($path);
            $list[$key] = $item->getPath();
        }

        return $list;
    }

    /**
     * Adds an undefined path for it to save this new setting
     * value to the config table
     *
     * @param string $propertyName - a key to create a path for
     *
     * @return array - [sg_config_key => core_config_path]
     */
    public function getNewSettingPath($propertyName): array
    {
        return [$propertyName => SgCoreInterface::PATH_UNDEFINED . $propertyName];
    }

    /**
     * The Gods will forever hate me for using request interface here
     *
     * @return string
     */
    public function getShopNumber(): string
    {
        $shopNumber = $this->request->getParam('shop_number');
        $item       = $this->sgCoreConfig->getShopNumberCollection($shopNumber)->getFirstItem();

        return $item->getData('value') ? : '';
    }
}
