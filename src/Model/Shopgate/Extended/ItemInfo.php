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

namespace Shopgate\Base\Model\Shopgate\Extended;

use Magento\Framework\DataObject;

/**
 * @method string getProductId()
 * @method ItemInfo setProductId(string $id)
 * @method ItemInfo setStackQuantity(string $qty)
 * @method array getOptions()
 * @method ItemInfo setOptions(array $options)
 * @method string getItemType() - e.g. configurable, bundled, simple
 * @method ItemInfo setItemType(string $type)
 * @method string getStoreViewId()
 * @method ItemInfo setStoreViewId(string $id)
 * @method string getParentSku()
 * @method ItemInfo setParentSku(string $sku)
 */
class ItemInfo extends DataObject
{
    /**
     * @param string | array | null $data
     *
     * @return $this
     * @throws \Zend_Json_Exception
     */
    public function loadInfo($data)
    {
        if ($this->isJson($data)) {
            $data = \Zend_Json_Decoder::decode($data);
        } elseif (is_null($data)) {
            $data = [];
        }

        $this->addData($data);

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    private function isJson($value)
    {
        return !empty($value) && is_string($value) && strpos($value, '{') === 0;
    }

    /**
     * @return int
     */
    public function getStackQuantity()
    {
        return (int) $this->getData('stack_quantity');
    }
}
