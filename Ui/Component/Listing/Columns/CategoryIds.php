<?php

namespace Ograre\Offers\Ui\Component\Listing\Columns;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class CategoryIds extends Column
{
    /** @var array $registry */
    protected array $registry;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CollectionFactory $categoryCollectionFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        protected CollectionFactory $categoryCollectionFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     * @throws LocalizedException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldname = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$fieldname] = $this->getCategoriesName($item[$fieldname]);
            }
        }

        return $dataSource;
    }

    /**
     * @param array $catgeoryIds
     * @return string
     * @throws LocalizedException
     */
    protected function getCategoriesName(array $catgeoryIds): string
    {
        $value = '';
        $toRegister = [];
        foreach ($catgeoryIds as $categoryId) {
            if (!isset($this->registry[$categoryId])) {
                $toRegister[] = $categoryId;
            }
        }

        if (!empty($toRegister)) {
            $this->registerCategoryNames($toRegister);
        }

        foreach ($catgeoryIds as $categoryId) {
            $value .= sprintf("%s<br/>", $this->registry[$categoryId]);
        }

        return $value;
    }

    /**
     * @param array $catgeoryIds
     * @return void
     * @throws LocalizedException
     */
    protected function registerCategoryNames(array $catgeoryIds): void
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addFieldToFilter('entity_id', ['in' => $catgeoryIds])
            ->addAttributeToSelect('name');

        foreach ($collection as $category) {
            $this->registry[$category->getId()] = $category->getName();
        }
    }
}
