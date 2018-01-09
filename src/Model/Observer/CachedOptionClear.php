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

namespace Shopgate\Base\Model\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Shopgate\Base\Model\Utility\Registry;

class CachedOptionClear implements ObserverInterface
{

    /** @var Registry */
    private $registry;
    /** @var array - list of allowed actions */
    private $actionWhiteList = ['check_cart', 'add_order'];

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Product options currently have a bug that gets produced when
     * we call the collector multiple times. This fix resets the
     * cache instance collection and forces magento not to use a
     * cached flag.
     * To replicate, use Bundled product 51 /w options in check_cart
     *
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        if (!$this->registry->isActionInList($this->actionWhiteList)) {
            return;
        }

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getData('product');
        $product->unsetData('_cache_instance_options_collection');
    }
}
