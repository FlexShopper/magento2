<?php

namespace FlexShopper\Payments\Model\Category;

use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class CategoryList implements ArrayInterface
{
    protected $_categoryCollectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->_categoryCollectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        $categoryCollection = $this->_categoryCollectionFactory->create();
        $categoryCollection->addAttributeToSelect('name');
        $categoryCollection->setOrder('name', 'ASC');

        $options = [];

        $options[] = ['label' => __('-- Filter Category --'), 'value' => ''];

        foreach ($categoryCollection as $category) {
            $options[] = [
                'label' => $category->getName(),
                'value' => $category->getId()
            ];
        }

        return $options;
    }
}
