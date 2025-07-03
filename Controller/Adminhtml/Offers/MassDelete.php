<?php

namespace Ograre\Offers\Controller\Adminhtml\Offers;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Ograre\Offers\Api\OfferRepositoryInterface;
use Ograre\Offers\Model\ResourceModel\Offer\CollectionFactory;

class MassDelete extends Action
{
    const ADMIN_RESOURCE = 'Ograre_Offers::manage';

    /**
     * @param Context $context
     * @param Filter $massActionFilter
     * @param CollectionFactory $offerCollectionFactory
     * @param OfferRepositoryInterface $offerRepository
     */
    public function __construct(
        Context $context,
        protected Filter $massActionFilter,
        protected CollectionFactory $offerCollectionFactory,
        protected OfferRepositoryInterface $offerRepository
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $collection = $this->massActionFilter->getCollection($this->offerCollectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $offer) {
            try {
                $this->offerRepository->delete($offer);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__(
                    'Something went went wrong deleting offer with id "%1": %2',
                    $offer->getId(),
                    $e->getMessage()
                ));
            }
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been processed.', $collectionSize));
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
