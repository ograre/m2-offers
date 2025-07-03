<?php

namespace Ograre\Offers\Model\ResourceModel;

use Exception;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Ograre\Offers\Api\Data\OfferInterface;
use Ograre\Offers\Api\Data\OfferInterfaceFactory;
use Ograre\Offers\Api\Data\OfferSearchResultsInterface;
use Ograre\Offers\Api\Data\OfferSearchResultsInterfaceFactory;
use Ograre\Offers\Api\OfferRepositoryInterface;
use Ograre\Offers\Model\ResourceModel\Offer as OfferResource;
use Ograre\Offers\Model\ResourceModel\Offer\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

class OfferRepository implements OfferRepositoryInterface
{
    /** @var OfferInterface[] $registry */
    protected array $registry = [];

    /**
     * @param Offer $offerResource
     * @param OfferInterfaceFactory $offerFactory
     * @param CollectionFactory $collectionFactory
     * @param OfferSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        protected OfferResource $offerResource,
        protected OfferInterfaceFactory $offerFactory,
        protected CollectionFactory $collectionFactory,
        protected OfferSearchResultsInterfaceFactory $searchResultsFactory,
        protected CollectionProcessorInterface $collectionProcessor
    ) {}

    /**
     * @inheritDoc
     */
    public function getById(int $offerId): OfferInterface
    {
        if (!isset($this->registry[$offerId])) {
            $offer = $this->getNewInstance();
            $this->offerResource->load($offer, $offerId, OfferInterface::KEY_ENTITY_ID);
            if (!$offer->getId()) {
                throw new NoSuchEntityException(__('Could not find offer with id "%1".', $offerId));
            }

            $this->registry[$offerId] = $offer;
        }

        return $this->registry[$offerId];
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): OfferSearchResultsInterface
    {
        $offerCollection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $offerCollection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($offerCollection->getItems());
        $searchResults->setTotalCount($offerCollection->getSize());

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function save(OfferInterface $offer): OfferInterface
    {
        $this->offerResource->save($offer);
        $this->registry[$offer->getId()] = $offer;
        return $offer;
    }

    /**
     * @inheritDoc
     */
    public function delete(OfferInterface $offer): bool
    {
        try {
            $this->offerResource->delete($offer);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete the offer: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $offerId): bool
    {
        return $this->delete($this->getById($offerId));
    }

    /**
     * @inheritdoc
     */
    public function getNewInstance(array $data = []): OfferInterface
    {
        return $this->offerFactory->create($data);
    }
}
