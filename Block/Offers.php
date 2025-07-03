<?php

namespace Ograre\Offers\Block;

use Magento\Catalog\Model\Category;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Ograre\Offers\Api\Data\OfferInterface;
use Ograre\Offers\Model\ResourceModel\Offer\Collection;
use Ograre\Offers\Model\ResourceModel\Offer\CollectionFactory;

class Offers extends Template
{
    /** @var Collection $collection */
    protected $collection;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param TimezoneInterface $timezone
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        protected CollectionFactory $collectionFactory,
        protected TimezoneInterface $timezone,
        protected Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getOffers(): array
    {
        return $this->getOfferCollection()->getItems();
    }

    /**
     * @return bool
     */
    public function hasOffers(): bool
    {
        return (bool)$this->getOfferCollection()->getSize();
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [OfferInterface::CACHE_TAG.'_'.$this->getCurrentCategory()->getId()];
    }

    /**
     * @return Collection
     */
    protected function getOfferCollection(): Collection
    {
        if ($this->collection) {
            return $this->collection;
        }

        $date = $this->timezone->date();
        $this->collection = $this->collectionFactory->create();
        $this->collection->addCategoryToFilter($this->getCurrentCategory())
            ->addInTimeFilter($date);

        return $this->collection;
    }

    /**
     * Retrieve current category model object
     *
     * @return Category
     */
    protected function getCurrentCategory(): Category
    {
        if (!$this->hasData('current_category')) {
            $this->setData('current_category', $this->registry->registry('current_category'));
        }
        return $this->getData('current_category');
    }
}
