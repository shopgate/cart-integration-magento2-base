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

namespace Shopgate\Base\Model\Service;

use Magento\Framework\Phrase;
use Magento\Framework\Webapi\Exception;
use Shopgate\Base\Api\ExportInterface;

/**
 * This is supposed to be rewritten by the Shopgate_Export module
 */
class Export implements ExportInterface
{
    const NOT_ENABLED_ERROR = 'Shopgate_Export module is not enabled or installed';

    /**
     * @inheritdoc
     *
     * @throws Exception
     */
    public function getCategories($action, $shopNumber, $traceId, $limit = null, $offset = null, $uids = [])
    {
        throw new Exception(new Phrase(self::NOT_ENABLED_ERROR));
    }

    /**
     * @inheritdoc
     *
     * @throws Exception
     */
    public function getCategoriesRaw($limit = null, $offset = null, array $uids = [])
    {
        throw new Exception(new Phrase(self::NOT_ENABLED_ERROR));
    }

    /**
     * @inheritdoc
     *
     * @throws Exception
     */
    public function getCustomer($user, $pass)
    {
        throw new Exception(new Phrase(self::NOT_ENABLED_ERROR));
    }

    /**
     * @inheritdoc
     *
     * @throws Exception
     */
    public function getCustomerRaw($user, $pass)
    {
        throw new Exception(new Phrase(self::NOT_ENABLED_ERROR));
    }

    /**
     * @inheritdoc
     *
     * @throws Exception
     */
    public function getItems($action, $shopNumber, $traceId, $limit = null, $offset = null, array $uids = [])
    {
        throw new Exception(new Phrase(self::NOT_ENABLED_ERROR));
    }

    /**
     * @inheritdoc
     *
     * @throws Exception
     */
    public function getItemsRaw($limit = null, $offset = null, array $uids = [])
    {
        throw new Exception(new Phrase(self::NOT_ENABLED_ERROR));
    }

    /**
     * @inheritdoc
     *
     * @throws Exception
     */
    public function getReviews($action, $shopNumber, $traceId, $limit = null, $offset = null, $uids = [])
    {
        throw new Exception(new Phrase(self::NOT_ENABLED_ERROR));
    }

    /**
     * @inheritdoc
     *
     * @throws Exception
     */
    public function getReviewsRaw($limit = null, $offset = null, array $uids = [])
    {
        throw new Exception(new Phrase(self::NOT_ENABLED_ERROR));
    }

    /**
     * @inheritdoc
     *
     * @throws Exception
     */
    public function checkCart(array $cart)
    {
        throw new Exception(new Phrase(self::NOT_ENABLED_ERROR));
    }

    /**
     * @inheritdoc
     *
     * @throws Exception
     */
    public function checkCartRaw($cart)
    {
        throw new Exception(new Phrase(self::NOT_ENABLED_ERROR));
    }

    /**
     * @inheritdoc
     *
     * @throws Exception
     */
    public function checkStockRaw($cart)
    {
        throw new Exception(new Phrase(self::NOT_ENABLED_ERROR));
    }
}
