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

namespace Shopgate\Base\Model\Storage;

use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\App\Cache\Type\FrontendPool;
use Magento\Framework\App\Cache\TypeListInterface as AppCache;
use Magento\Framework\Cache\Frontend\Decorator\TagScope;

/** System / Cache Management / Cache type "Shopgate Cache" */
class Cache extends TagScope
{
    /**
     * Cache type code unique among all cache types
     */
    const TYPE_IDENTIFIER = 'shopgate';
    /**
     * Cache tag used to distinguish the cache type from all other cache
     */
    const CACHE_TAG = 'SHOPGATE';

    /** @var StateInterface */
    private $cacheState;
    /** @var AppCache */
    private $appCache;

    /**
     * @param FrontendPool   $cacheFrontendPool
     * @param StateInterface $cacheState
     * @param AppCache       $appCache
     */
    public function __construct(FrontendPool $cacheFrontendPool, StateInterface $cacheState, AppCache $appCache)
    {
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
        $this->cacheState = $cacheState;
        $this->appCache   = $appCache;
    }

    /**
     * @inheritdoc
     */
    public function save($data, $identifier, array $tags = [], $lifeTime = null)
    {
        if (empty($data) || !$this->isEnabled()) {
            return false;
        }

        return parent::save($data, $identifier, $tags, $lifeTime);
    }

    /**
     * Checks if cache is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->cacheState->isEnabled(self::TYPE_IDENTIFIER);
    }

    /**
     * Invalidate our cache
     */
    public function invalidate()
    {
        $this->appCache->invalidate(self::TYPE_IDENTIFIER);
    }
}
