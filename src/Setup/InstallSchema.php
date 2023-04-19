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

namespace Shopgate\Base\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('shopgate_order'))
            ->addColumn(
                'shopgate_order_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Order Id'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true],
                'Store Id'
            )->addColumn(
                'shopgate_order_number',
                Table::TYPE_TEXT,
                20,
                ['nullable' => false]
            )->addColumn(
                'is_shipping_blocked',
                Table::TYPE_INTEGER,
                1,
                ['nullable' => false]
            )->addColumn(
                'is_paid',
                Table::TYPE_INTEGER,
                1,
                ['nullable' => false]
            )->addColumn(
                'is_sent_to_shopgate',
                Table::TYPE_INTEGER,
                1,
                ['nullable' => false]
            )->addColumn(
                'is_cancellation_sent_to_shopgate',
                Table::TYPE_INTEGER,
                1,
                ['nullable' => false]
            )->addColumn(
                'is_test',
                Table::TYPE_INTEGER,
                1,
                ['nullable' => false]
            )->addColumn(
                'is_customer_invoice_blocked',
                Table::TYPE_INTEGER,
                1,
                ['nullable' => false]
            )->addColumn(
                'reported_shipping_collections',
                Table::TYPE_TEXT
            )->addColumn(
                'received_data',
                Table::TYPE_TEXT
            )->addIndex(
                $installer->getIdxName('shopgate_order', ['order_id']),
                ['order_id']
            )->addIndex(
                $installer->getIdxName('shopgate_order', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('shopgate_order', 'order_id', 'sales_order', 'entity_id'),
                'order_id',
                $installer->getTable('sales_order'),
                'entity_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName('shopgate_order', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_SET_NULL
            )->setComment('Shopgate Orders');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('shopgate_customer'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true]
            )->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Customer ID'
            )->addColumn(
                'token',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )->addIndex(
                $installer->getIdxName('shopgate_customer', ['customer_id']),
                ['customer_id']
            )->addForeignKey(
                $installer->getFkName('shopgate_customer', 'customer_id', 'customer_entity', 'entity_id'),
                'customer_id',
                $installer->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )->setComment('Shopgate Customer');

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
