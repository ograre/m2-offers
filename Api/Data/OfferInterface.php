<?php
namespace Ograre\Offers\Api\Data;

use DateTime;

interface OfferInterface
{
    public const KEY_ENTITY_ID = 'entity_id';
    public const KEY_TITLE = 'title';
    public const KEY_IMAGE = 'image';
    public const KEY_IMAGE_URL = 'image_url';
    public const KEY_LINK = 'link';
    public const KEY_LINK_URL = 'link_url';
    public const KEY_LINK_TYPE = 'link_type';
    public const KEY_LINK_VALUE = 'link_value';
    public const KEY_CATEGORY_IDS = 'category_ids';
    public const KEY_START_DATE = 'start_date';
    public const KEY_END_DATE = 'end_date';
    public const KEY_CREATED_AT = 'created_at';
    public const KEY_UPDATED_AT = 'updated_at';

    public const IMAGE_FOLDER = 'offers';
    public const CACHE_TAG = 'offers';

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @param int $id
     * @return OfferInterface
     */
    public function setId(int $id);

    /**
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * @param string $title
     * @return OfferInterface
     */
    public function setTitle(string $title): OfferInterface;

    /**
     * @return string|null
     */
    public function getImage(): ?string;

    /**
     * @return string|null
     */
    public function getImageUrl(): ?string;

    /**
     * @param string $image
     * @return OfferInterface
     */
    public function setImage(string $image): OfferInterface;

    /**
     * @return string|null
     */
    public function getLink(): ?string;

    /**
     * @return string
     */
    public function getLinkUrl(): string;

    /**
     * @return string
     */
    public function getLinkType(): string;

    /**
     * @return string
     */
    public function getLinkValue(): string;

    /**
     * Link should be {link_type}/{link_value}
     *
     * @param string $link
     * @return OfferInterface
     */
    public function setLink(string $link): OfferInterface;

    /**
     * @return array
     */
    public function getCategoryIds(): array;

    /**
     * @param array $categories
     * @return OfferInterface
     */
    public function setCategoryIds(array $categories): OfferInterface;

    /**
     * @return DateTime|null
     */
    public function getStartDate(): ?DateTime;

    /**
     * @param DateTime $startDate
     * @return OfferInterface
     */
    public function setStartDate(DateTime $startDate): OfferInterface;

    /**
     * @return DateTime|null
     */
    public function getEndDate(): ?DateTime;

    /**
     * @param DateTime $endDate
     * @return OfferInterface
     */
    public function setEndDate(DateTime $endDate): OfferInterface;

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime;

    /**
     * @param DateTime $createdAt
     * @return OfferInterface
     */
    public function setCreatedAt(DateTime $createdAt): OfferInterface;

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime;

    /**
     * @param DateTime $updatedAt
     * @return OfferInterface
     */
    public function setUpdatedAt(DateTime $updatedAt): OfferInterface;

    /**
     * @param $modelId
     * @param $field
     * @return OfferInterface
     */
    public function load($modelId, $field = null);
}
