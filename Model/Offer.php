<?php

namespace Ograre\Offers\Model;

use DateTime;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Registry;
use Ograre\Offers\Api\Data\OfferInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Ograre\Offers\Helper\Image;
use Ograre\Offers\Helper\OfferLink;
use Ograre\Offers\Model\ResourceModel\Offer as OfferResource;

class Offer extends AbstractModel implements OfferInterface
{
    /** @inheritdoc  */
    protected $_eventPrefix = 'offer';

    /** @inheritdoc */
    protected $_eventObject = 'offer';

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Image $imageHelper
     * @param OfferLink $offerLinkHelper
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @throws LocalizedException
     */
    public function __construct(
        Context             $context,
        Registry            $registry,
        protected Image     $imageHelper,
        protected OfferLink $offerLinkHelper,
        ?AbstractResource   $resource = null,
        ?AbstractDb         $resourceCollection = null,
        array               $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(OfferResource::class);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): ?string
    {
        return $this->getData(self::KEY_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setTitle(string $title): OfferInterface
    {
        $this->setData(self::KEY_TITLE, $title);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getImage(): ?string
    {
        return $this->getData(self::KEY_IMAGE);
    }

    /**
     * @inheritdoc
     */
    public function getImageUrl(): ?string
    {
        if (!$this->getImage()) {
            return '';
        }

        if (!$url = $this->getData(self::KEY_IMAGE_URL)) {
            $url = $this->imageHelper->getOfferImageUrl($this);
            $this->setData(self::KEY_IMAGE_URL, $url);
        }

        return $url;
    }

    /**
     * @inheritDoc
     */
    public function setImage(string $image): OfferInterface
    {
        return $this->setData(self::KEY_IMAGE, $image);
    }

    /**
     * @inheritDoc
     */
    public function getLink(): ?string
    {
        return $this->getData(self::KEY_LINK);
    }

    /**
     * @inheritdoc
     */
    public function getLinkUrl(): string
    {
        if (!$link = $this->getLink()) {
            return "";
        }

        if(!$url = $this->getData(static::KEY_LINK_URL)) {
            $url = $this->offerLinkHelper->processLink($link);
            $this->setData(static::KEY_LINK_URL, $url);
        }

        return $url;
    }

    /**
     * @inheritdoc
     */
    public function getLinkType(): string
    {
        if (!$link = $this->getLink()) {
            return '';
        }

        if (!$linkType = $this->getData(static::KEY_LINK_TYPE)) {
            $linkType = $this->offerLinkHelper->getLinkType($link);
            $this->setData(static::KEY_LINK_TYPE, $linkType);
        }

        return $linkType;
    }

    /**
     * @inheritdoc
     */
    public function getLinkValue(): string
    {
        if (!$link = $this->getLink()) {
            return '';
        }

        if (!$linkValue = $this->getData(static::KEY_LINK_VALUE)) {
            $linkValue = $this->offerLinkHelper->getLinkValue($link);
            $this->setData(static::KEY_LINK_VALUE, $linkValue);
        }

        return $linkValue;
    }

    /**
     * @inheritDoc
     */
    public function setLink(string $link): OfferInterface
    {
        if (!$this->checkLink($link)) {
            throw new \Exception();
        }
        $this->setData(self::KEY_LINK, $link);
        $this->resetLinkData();
        return $this;
    }

    /**
     * @return void
     */
    public function resetLinkData(): void
    {
        $this->setData(static::KEY_LINK_URL, null);
        $this->setData(static::KEY_LINK_TYPE, null);
        $this->setData(static::KEY_LINK_VALUE, null);
    }

    /**
     * @param string $link
     * @return bool
     * @throws LocalizedException
     */
    protected function checkLink(string $link): bool
    {
        return $this->offerLinkHelper->isValidLink($link);
    }

    /**
     * @inheritDoc
     */
    public function getCategoryIds(): array
    {
        return $this->getData(self::KEY_CATEGORY_IDS);
    }

    /**
     * @inheritDoc
     */
    public function setCategoryIds(array $categories): OfferInterface
    {
        return  $this->setData(self::KEY_CATEGORY_IDS, $categories);
    }

    /**
     * @inheritDoc
     */
    public function getStartDate(): ?DateTime
    {
        return $this->getData(self::KEY_START_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setStartDate(DateTime $startDate): OfferInterface
    {
        return $this->setData(self::KEY_START_DATE, $startDate);
    }

    /**
     * @inheritDoc
     */
    public function getEndDate(): ?DateTime
    {
        return $this->getData(self::KEY_END_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setEndDate(DateTime $endDate): OfferInterface
    {
        return $this->setData(self::KEY_END_DATE, $endDate);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->getData(self::KEY_CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(DateTime $createdAt): OfferInterface
    {
        return $this->setData(self::KEY_CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->getData(self::KEY_UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt(DateTime $updatedAt): OfferInterface
    {
        return $this->setData(self::KEY_UPDATED_AT, $updatedAt);
    }
}
