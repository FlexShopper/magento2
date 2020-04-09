<?php

namespace FlexShopper\Payments\Setup;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    const COLUMN_FLEXSHOPPER_ID = 'flexshopper_id';
    const COLUMN_FLEXSHOPPER_TXID = 'flexshopper_txid';
    /**
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();


        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                self::COLUMN_FLEXSHOPPER_ID,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Flexshopper ID'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                self::COLUMN_FLEXSHOPPER_ID,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Flexshopper ID'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                self::COLUMN_FLEXSHOPPER_TXID,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Flexshopper TX ID'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                self::COLUMN_FLEXSHOPPER_TXID,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Flexshopper TX ID'
                ]
            );
        }

        $setup->endSetup();
    }
}
