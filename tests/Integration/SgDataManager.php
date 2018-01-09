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

namespace Shopgate\Base\Tests\Integration;

class SgDataManager
{

    /**
     * Retrieves a customer address in Germany
     *
     * @param bool $billing
     *
     * @return array
     */
    public function getGermanAddress($billing = true)
    {
        return [
            'is_invoice_address'  => $billing,
            'is_delivery_address' => !$billing,
            'first_name'          => 'German',
            'last_name'           => 'Customer',
            'gender'              => 'f',
            'birthday'            => '10/19/15',
            'company'             => 'Shopgate Inc',
            'street_1'            => 'Zevener Straße 8',
            'street_2'            => null,
            'zipcode'             => '27404',
            'city'                => 'Frankenbostel',
            'country'             => 'DE',
            'state'               => null,
            'phone'               => '123456789',
            'mobile'              => '987654321',
            'mail'                => 'shopgate@shopgate.com',
            'custom_fields'       => [
                [
                    'label'               => 'Is house?',
                    'internal_field_name' => 'is_house',
                    'value'               => (int) $billing,
                ]
            ],
        ];
    }

    /**
     * Retrieves a US based address
     *
     * @param bool $billing
     *
     * @return array
     */
    public function getUsAddress($billing = true)
    {
        return [
            'is_invoice_address'  => $billing,
            'is_delivery_address' => !$billing,
            'first_name'          => 'US',
            'last_name'           => 'Customer',
            'gender'              => 'f',
            'birthday'            => '10/19/15',
            'company'             => 'Shopgate Inc',
            'street_1'            => '303 E Elliot Rd',
            'street_2'            => 'Suite 101',
            'zipcode'             => '85253',
            'city'                => 'Tempe',
            'country'             => 'US',
            'state'               => 'US-AZ',
            'phone'               => '123456789',
            'mobile'              => '987654321',
            'mail'                => 'shopgate@shopgate.com',
            'custom_fields'       => [
                [
                    'label'               => 'Is house?',
                    'internal_field_name' => 'is_house',
                    'value'               => (int) $billing,
                ]
            ],
        ];
    }

    /**
     * Retrieve a simple product to use in ShopgateOrder or ShopgateCart
     *
     * @param int $qty
     *
     * @return array
     */
    public function getSimpleProduct($qty = 1)
    {
        return [
            'item_number'          => '15',
            'item_number_public'   => '24-UG06',
            'order_item_id'        => '45059',
            'type'                 => 'item',
            'quantity'             => $qty,
            'name'                 => 'Affirm Water Bottle ',
            'unit_amount'          => '7.00',
            'unit_amount_with_tax' => '7.00',
            'tax_percent'          => '0.00',
            'currency'             => 'EUR',
            'internal_order_info'  => '{"store_view_id":"1","product_id":"15","item_type":"simple"}',
            'options'              => [],
            'inputs'               => [],
            'attributes'           => [],
        ];
    }

    /**
     * Retrieve a grouped product to use in ShopgateOrder or ShopgateCart
     *
     * @param int $qty
     *
     * @return array
     */
    public function getGroupedProduct($qty = 1)
    {
        return [
            'item_number'          => '33',
            'item_number_public'   => '24-WG085',
            'order_item_id'        => '45056',
            'type'                 => 'item',
            'quantity'             => $qty,
            'name'                 => 'Sprite Yoga Strap 6 foot',
            'unit_amount'          => '14.00',
            'unit_amount_with_tax' => '14.00',
            'tax_percent'          => '0.00',
            'currency'             => 'EUR',
            'internal_order_info'  => '{"store_view_id":"1","product_id":"33","item_type":"simple"}',
            'options'              => [],
            'inputs'               => [],
            'attributes'           => [],
        ];
    }

    /**
     * Retrieve a configurable product to use in ShopgateOrder or ShopgateCart
     *
     * @param int $qty
     *
     * @return array
     */
    public function getConfigurableProduct($qty = 1)
    {
        return [
            'item_number'          => '67-52',
            'parent_item_number'   => '',
            'quantity'             => $qty,
            'unit_amount_net'      => 2.5,
            'unit_amount_with_tax' => 3,
            'unit_amount'          => 2.5,
            'name'                 => 'Configurable',
            'tax_percent'          => 20.00,
            'internal_order_info'  => '{"product_id":"52","item_type":"configurable"}',
            'attributes'           => [
                [
                    'name'  => 'Color', //90
                    'value' => 'Black' //49
                ],
                [
                    'name'  => 'Size', //137
                    'value' => 'XS' //167
                ],
            ],
            'inputs'               => [],
            'options'              => [],
        ];
    }
}
