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

namespace Shopgate\Base\Model\Redirect\Route;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Shopgate\Base\Model\Redirect\Route\Type\Generic;

class Utility
{
    /** @var array */
    private $map = [];
    /** @var Context */
    private $context;

    /**
     * @param Context         $context
     * @param TypeInterface[] $map - check di.xml
     *
     * @codeCoverageIgnore
     */
    public function __construct(Context $context, array $map)
    {
        $this->context = $context;
        $this->map     = $map;
    }

    /**
     * Retrieves the correct route based on
     * the current controller
     *
     * @return TypeInterface
     */
    public function getRoute()
    {
        /** @var Http $req */
        $req = $this->context->getRequest();
        if (isset($this->map[$req->getControllerName()])) {
            return $this->map[$req->getControllerName()];
        }

        return $this->map[Generic::CONTROLLER_KEY];
    }
}
