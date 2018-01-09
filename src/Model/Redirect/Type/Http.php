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

namespace Shopgate\Base\Model\Redirect\Type;

use Shopgate\Base\Model\Redirect\Builder;
use Shopgate\Base\Model\Redirect\Route\Utility;

/**
 * Class responsible for HTTP redirect only
 */
class Http implements TypeInterface
{

    /** @var Utility */
    private $routeUtility;
    /** @var Builder */
    private $builder;

    /**
     * @param Utility $utility
     * @param Builder $builder
     */
    public function __construct(
        Utility $utility,
        Builder $builder
    ) {
        $this->routeUtility = $utility;
        $this->builder      = $builder;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $route    = $this->routeUtility->getRoute();
        $redirect = $this->builder->buildHttpRedirect();

        if (!$redirect) {
            return;
        }

        $route->callScriptBuilder($redirect);
    }
}
