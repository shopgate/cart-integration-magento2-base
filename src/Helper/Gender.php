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

namespace Shopgate\Base\Helper;

use Shopgate\Base\Api\Config\CoreInterface;
use Shopgate\Base\Api\Config\SgCoreInterface;

class Gender
{
    /** @var CoreInterface */
    private $config;
    /** @var Encoder */
    private $encoder;

    public function __construct(
        CoreInterface $config,
        Encoder $encoder
    ) {
        $this->config  = $config;
        $this->encoder = $encoder;
    }

    /**
     * @return array
     */
    public function getMapping(): array
    {
        $genderConfig = $this->config->getConfigByPath(SgCoreInterface::PATH_GENDER_MAP)->getValue();

        return !empty($genderConfig)
            ? (array) $this->encoder->decode($genderConfig)
            : [];
    }
}
