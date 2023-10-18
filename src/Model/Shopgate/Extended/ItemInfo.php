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

namespace Shopgate\Base\Model\Shopgate\Extended;

use JsonSerializable;
use Magento\Framework\DataObject;

use function json_decode;

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
class ItemInfo extends DataObject implements JsonSerializable
{
    /**
     * Translates SG info data into Mage object
     *
     * @param string|array|null $data
     *
     * @return ItemInfo
     */
    public function loadInfo($data): self
    {
        if ($this->isJson($data)) {
            $data = json_decode($data, true);
        } elseif (null === $data) {
            $data = [];
        }

        $this->addData($data);

        return $this;
    }

    /**
     * Checks if value is proper JSON
     *
     * @param mixed $value
     *
     * @return bool
     */
    private function isJson($value): bool
    {
        return !empty($value) && is_string($value) && strpos($value, '{') === 0;
    }

    /**
     * Checks quantity
     *
     * @return int
     */
    public function getStackQuantity(): int
    {
        return (int) $this->getData('stack_quantity');
    }

    /**
     * Helps encode json data when saving to DB
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->getData();
    }
}
