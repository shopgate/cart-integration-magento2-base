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

namespace Shopgate\Base\Helper\Initializer;

use Shopgate\Base\Api\CronInterface;
use Shopgate\Base\Api\ExportInterface;
use Shopgate\Base\Api\ImportInterface;
use Shopgate\Base\Api\SettingsInterface;
use Shopgate\Base\Model\Config as MainConfig;

/**
 * The main purpose of this class is to initialize classes and pass them along.
 * This hack is needed due to the non-rewritable final constructor on ShopgatePlugin lib class
 */
class Forwarder
{
    /** @var MainConfig $mainConfig */
    private $mainConfig;
    /** @var Config */
    private $configInitializer;
    /** @var SettingsInterface */
    private $settingsInterface;
    /** @var ExportInterface */
    private $exportInterface;
    /** @var ImportInterface */
    private $importInterface;
    /** @var CronInterface */
    private $cronInterface;

    /**
     * @param Config            $configInitializer
     * @param MainConfig        $mainConfig
     * @param SettingsInterface $settingsInterface
     * @param ExportInterface   $exportInterface
     * @param ImportInterface   $importInterface
     * @param CronInterface     $cronInterface
     *
     * @@codeCoverageIgnore
     */
    public function __construct(
        Config $configInitializer,
        MainConfig $mainConfig,
        SettingsInterface $settingsInterface,
        ExportInterface $exportInterface,
        ImportInterface $importInterface,
        CronInterface $cronInterface
    ) {
        $this->mainConfig        = $mainConfig;
        $this->configInitializer = $configInitializer;
        $this->settingsInterface = $settingsInterface;
        $this->exportInterface   = $exportInterface;
        $this->importInterface   = $importInterface;
        $this->cronInterface     = $cronInterface;
    }

    /**
     * @return MainConfig
     */
    public function getMainConfig()
    {
        return $this->mainConfig;
    }

    /**
     * @return Config
     */
    public function getConfigInitializer()
    {
        return $this->configInitializer;
    }

    /**
     * @return SettingsInterface
     */
    public function getSettingsInterface()
    {
        return $this->settingsInterface;
    }

    /**
     * @return ExportInterface
     */
    public function getExportInterface()
    {
        return $this->exportInterface;
    }

    /**
     * @return ImportInterface
     */
    public function getImportInterface()
    {
        return $this->importInterface;
    }

    /**
     * @return CronInterface
     */
    public function getCronInterface()
    {
        return $this->cronInterface;
    }
}
