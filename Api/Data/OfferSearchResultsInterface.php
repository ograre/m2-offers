<?php
namespace Ograre\Offers\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;
use Ograre\Offers\Api\Data\OfferInterface;

interface OfferSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Ograre\Offers\Api\Data\OfferInterface[]
     */
    public function getItems();

    /**
     * @param \Ograre\Offers\Api\Data\OfferInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
