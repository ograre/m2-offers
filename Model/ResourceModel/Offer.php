<?php
namespace Ograre\Offers\Model\ResourceModel;

use DateTime;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Ograre\Offers\Api\Data\OfferFileProcessorInterface;
use Ograre\Offers\Api\Data\OfferInterface;

class Offer extends AbstractDb
{
    public const OFFER_CATEGORY_TABLE = 'offer_category';
    protected const CATEGORY_IDS_SEPARATOR = ',';

    /** @inheritdoc */
    protected $_idFieldName = 'entity_id';

    /**
     * @param Context $context
     * @param OfferFileProcessorInterface $offerFileProcessor
     * @param $connectionName
     */
    public function __construct(
        Context $context,
        protected OfferFileProcessorInterface $offerFileProcessor,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('offer_entity', 'entity_id');
    }

    /**
     * @inheritdoc
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $object->setUpdatedAt(new DateTime());

        if (($object->isObjectNew() || $this->imageHasChanged($object)) && $this->offerFileProcessor->tmpFileExists($object->getImage())) {
            $destination = $this->offerFileProcessor->moveFileFromTmp($object->getImage());
            $splitDestination = explode('/', $destination);
            $object->setImage(end($splitDestination));
        }

        if ($object->getCategoryIds()) {
            $object->setData(
                OfferInterface::KEY_CATEGORY_IDS,
                implode(self::CATEGORY_IDS_SEPARATOR, $object->getCategoryIds())
            );
        }

        return parent::_beforeSave($object);
    }

    /**
     * @param AbstractModel $object
     * @return Offer
     */
    protected function _afterSave(AbstractModel $object)
    {
        $categoryIds = $object->getData(OfferInterface::KEY_CATEGORY_IDS)?? [];
        if (!is_array($categoryIds)) {
            $object->setCategoryIds(
                explode(
                    self::CATEGORY_IDS_SEPARATOR,
                    $categoryIds
                ) ?? []
            );
        }

        $this->processCategoryOfferLinkage($object);

        return parent::_afterSave($object);
    }

    /**
     * @param AbstractModel $object
     * @return Offer
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $categoryIds = $object->getData(OfferInterface::KEY_CATEGORY_IDS)?? '';
        if (is_string($categoryIds)) {
            $object->setCategoryIds(
                explode(
                    self::CATEGORY_IDS_SEPARATOR,
                    $categoryIds
                )
            );
        }

        return parent::_afterLoad($object);
    }

    /**
     * @param AbstractModel $object
     * @return bool
     */
    protected function imageHasChanged(AbstractModel $object): bool
    {
        return $object->getOrigData(OfferInterface::KEY_IMAGE) !== $object->getData(OfferInterface::KEY_IMAGE);
    }

    /**
     * @param AbstractModel $object
     * @return void
     */
    protected function processCategoryOfferLinkage(AbstractModel $object)
    {
        $categoryIds = $object->getData(OfferInterface::KEY_CATEGORY_IDS)?? [];
        $offerCategoryTable = $this->getConnection()->getTableName('offer_category');
        $select = $this->getConnection()->select()
            ->from($offerCategoryTable, [])
            ->where('offer_id = ?', $object->getId())
            ->where('category_id NOT IN (?)', $categoryIds);

        $query = sprintf('DELETE %s', $select->assemble());
        $this->getConnection()->query($query);

        $toInsert = [];
        foreach ($categoryIds as $categoryId) {
            $toInsert[] = [
                'offer_id' => $object->getId(),
                'category_id' => $categoryId
            ];
        }

        if (!empty($toInsert)) {
            $this->getConnection()->insertOnDuplicate(
                $offerCategoryTable,
                $toInsert,
                ['offer_id']
            );
        }
    }
}
