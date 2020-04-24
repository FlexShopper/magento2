<?php


namespace FlexShopper\Payments\Ui\Component\Listing\Column;

use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Category extends Column
{
    private $productRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $productId = $item['entity_id'];
                $product = $this->productRepository->getById($productId);

                $categoryIds = $product->getCategoryIds();
                $categories = array();

                if (count($categoryIds)) {
                    foreach ($categoryIds as $categoryId) {
                        $category = $this->categoryRepository->get($categoryId);
                        $categories[] = $category->getName();
                    }
                }
                $item[$fieldName] = implode(',', $categories);
            }
        }

        return $dataSource;
    }
}
