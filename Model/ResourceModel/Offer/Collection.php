<?php
namespace Ograre\Offers\Model\ResourceModel\Offer;

use DateTime;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Ograre\Offers\Api\Data\OfferInterface;
use Ograre\Offers\Model\Offer;
use Ograre\Offers\Model\ResourceModel\Offer as OfferResource;

class Collection extends AbstractCollection
{

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(Offer::class, OfferResource::class);
    }

    /**
     * @inheritdoc
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $splittedIds = explode(',', $item->getData(OfferInterface::KEY_CATEGORY_IDS));
            $item->setCategoryIds($splittedIds);
        }

        return parent::_afterLoad();
    }

    /**
     * @param CategoryInterface $category
     * @return Collection
     */
    public function addCategoryToFilter(CategoryInterface $category): Collection
    {
        $this->getSelect()->joinInner(
            ['oc' => $this->getTable(OfferResource::OFFER_CATEGORY_TABLE)],
            'oc.offer_id = main_table.entity_id',
            []
        )->where('oc.category_id = ?', $category->getId());

        return $this;
    }

    /**
     * @param DateTime $dateTime
     * @return Collection
     */
    public function addInTimeFilter(DateTime $dateTime): Collection
    {
        $this->addFieldToFilter(
            ['start_date', 'start_date'],
            [['null' => true], ['lteq' => $dateTime->format('Y-m-d')]]
        )->addFieldToFilter(
            ['end_date', 'end_date'],
            [['null' => true], ['gteq' => $dateTime->format('Y-m-d')]]
        );

        return $this;
    }
}
