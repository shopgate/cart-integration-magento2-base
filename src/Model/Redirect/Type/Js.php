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
use Shopgate\Base\Model\Storage\Cache;
use Shopgate\Base\Model\Storage\Session;
use Shopgate\Base\Model\Utility\SgLoggerInterface;

/**
 * Class responsible for the javascript HTML creation & saving to session
 * to be picked up by the frontend phtml template and output in the <head>
 */
class Js implements TypeInterface
{

    /** @var Session */
    private $session;
    /** @var Utility */
    private $routeUtility;
    /** @var Cache */
    private $cache;
    /** @var Builder */
    private $builder;
    /** @var SgLoggerInterface */
    private $logger;

    /**
     * @param Session           $session
     * @param Utility           $utility
     * @param Cache             $cache
     * @param Builder           $builder
     * @param SgLoggerInterface $logger
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        Session $session,
        Utility $utility,
        Cache $cache,
        Builder $builder,
        SgLoggerInterface $logger
    ) {
        $this->session      = $session;
        $this->routeUtility = $utility;
        $this->cache        = $cache;
        $this->builder      = $builder;
        $this->logger       = $logger;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $route    = $this->routeUtility->getRoute();
        $cacheKey = $route->getCacheKey();
        $jsHeader = $this->cache->load($cacheKey);

        if ($jsHeader === false) {
            $redirect = $this->builder->buildJsRedirect();

            if (!$redirect) {
                return;
            }

            $redirect->getBuilder()->setSiteParameters($route->getTags());

            $jsHeader = $route->callScriptBuilder($redirect);
            $cached   = $this->cache->save($jsHeader, $cacheKey);

            if (!$cached) {
                $this->logger->debug(
                    'Could not save header to cache. It was enabled: ' . $this->cache->isEnabled() .
                    '. Header is empty: ' . empty($jsHeader)
                );
            }
        }

        $this->session->setScript($jsHeader);
    }
}
