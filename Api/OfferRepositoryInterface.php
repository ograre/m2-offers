<?php
namespace Ograre\Offers\Api;

use Ograre\Offers\Api\Data\OfferInterface;
use Ograre\Offers\Api\Data\OfferSearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteriaInterface;

interface OfferRepositoryInterface
{
    /**
     * @param int $offerId
     * @return OfferInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $offerId): OfferInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return OfferSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): OfferSearchResultsInterface;

    /**
     * @param OfferInterface $offer
     * @return OfferInterface
     * @throws LocalizedException
     */
    public function save(OfferInterface $offer): OfferInterface;

    /**
     * @param OfferInterface $offer
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(OfferInterface $offer): bool;

    /**
     * @param int $offerId
     * @return bool true on success
     * @throws NoSuchEntityException|LocalizedException
     */
    public function deleteById(int $offerId): bool;

    /**
     * @param array $data
     * @return OfferInterface
     */
    public function getNewInstance(array $data = []): OfferInterface;
}
