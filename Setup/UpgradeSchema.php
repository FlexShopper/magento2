<?php

namespace FlexShopper\Payments\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    const COLUMN_FLEXSHOPPER_ID = 'flexshopper_id';
    const COLUMN_FLEXSHOPPER_TXID = 'flexshopper_txid';
    
    private $attributeRepository;
    private $eavSetupFactory;
    private $moduleDataSetup;
    
    
    public function __construct(\Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
                                ModuleDataSetupInterface $moduleDataSetup,
                                EavSetupFactory $eavSetupFactory)
    {
        $this->attributeRepository = $attributeRepository;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

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

        if (version_compare($context->getVersion(), '1.1.2', '<')) {
            try {
                $this->attributeRepository->get(\Magento\Catalog\Model\Product::ENTITY, 'flexshopper_leasing_enabled');    
            }
            catch(\Magento\Framework\Exception\NoSuchEntityException $ex) {
                $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    'flexshopper_leasing_enabled',
                    [
                        'type' => 'int',
                        'label' => 'FlexShopper Leasing Enabled',
                        'input' => 'boolean',
                        'source' => '',
                        'frontend' => '',
                        'required' => true,
                        'backend' => '',
                        'sort_order' => '30',
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                        'default' => null,
                        'visible' => true,
                        'user_defined' => true,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'unique' => false,
                        'apply_to' => '',
                        'group' => 'General',
                        'used_in_product_listing' => true,
                        'is_used_in_grid' => true,
                        'is_visible_in_grid' => false,
                        'is_filterable_in_grid' => false,
                        'option' => array('values' => array(""))
                    ]
                );
            }
            
        }

        $setup->endSetup();
    }
}
