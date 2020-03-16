<?php


namespace FlexShopper\Payments\Model\Config;


use Magento\Catalog\Model\Entity\Attribute;

class Brand implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    private $attributeFactory;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeFactory
    ) {
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $attributeInfo = $this->attributeFactory->create()
        ->addFieldToFilter('is_visible', '1');
        $ret = [];
        /** @var Attribute $attr */
        foreach ($attributeInfo as $attr) {
            $ret[] = [
                'label' => $attr->getAttributeCode(),
                'value' => $attr->getAttributeCode()
            ];
        }
        return $ret;
    }
}
