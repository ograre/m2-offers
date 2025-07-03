<?php

namespace Ograre\Offers\Controller\Adminhtml\Offers;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Ograre\Offers\Api\Data\OfferInterface;
use Ograre\Offers\Api\OfferRepositoryInterface;

class Edit extends Action implements HttpGetActionInterface
{
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param OfferRepositoryInterface $offerRepository
     */
    public function __construct(
        Context $context,
        protected PageFactory $resultPageFactory,
        protected Registry $registry,
        protected OfferRepositoryInterface $offerRepository
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if ($offerId = $this->getRequest()->getParam('offer_id')) {
            try {
                $offer = $this->offerRepository->getById($offerId);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This offer does not exist.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        } else {
            $offer = $this->offerRepository->getNewInstance();
        }

        $this->registry->register('offer', $offer);

        return $this->_initAction($offer);
    }

    /**
     * @return Page
     */
    protected function _initAction(OfferInterface $offer)
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ograre_Offers::offers');
        $resultPage->addBreadcrumb(__('Offers'), __('Offers'));
        $resultPage->addBreadcrumb(
            $offer->getId() ? __('Edit Offer') : __('New Offer'),
            $offer->getId() ? __('Edit Offer') : __('New Offer')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Offers'));
        $resultPage->getConfig()->getTitle()->prepend(
            $offer->getId() ? __('Edit Offer') : __('New Offer')
        );

        return $resultPage;
    }
}
