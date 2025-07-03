<?php

namespace Ograre\Offers\Controller\Adminhtml\Offers;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Ograre\Offers\Api\OfferRepositoryInterface;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'Ograre_Offers::manage';

    /**
     * @param Context $context
     * @param OfferRepositoryInterface $offerRepository
     */
    public function __construct(
        Context $context,
        protected OfferRepositoryInterface $offerRepository
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/');

        try {
            if (!$offerId = $this->getRequest()->getParam('offer_id')) {
                throw new LocalizedException(__('Invalid offer_id'));
            }

            $this->offerRepository->deleteById($offerId);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__(
                'Something went wrong deleting offer: %1',
                $e->getMessage()
            ));
            return $resultRedirect;
        }

        $this->messageManager->addSuccessMessage(__('Offer successfully deleted.'));

        return $resultRedirect;
    }
}
