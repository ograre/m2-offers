<?php

namespace Ograre\Offers\Controller\Adminhtml\Offers;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Ograre\Offers\Api\Data\OfferInterface;
use Ograre\Offers\Api\OfferRepositoryInterface;
use Magento\Framework\Controller\Result\Redirect;

class Save extends Action implements HttpPostActionInterface
{
    protected const BACK_VALUE_DUPLICATION = 'duplicate';
    protected const BACK_VALUE_CLOSE = 'close';

    /**
     * @param Context $context
     * @param OfferRepositoryInterface $offerRepository
     * @param PostDataProcessor $dataProcessor
     */
    public function __construct(
        Context $context,
        protected OfferRepositoryInterface $offerRepository,
        protected PostDataProcessor $dataProcessor
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue(null, false);
        if (!$data) {
            $this->messageManager->addErrorMessage(
                __('No data were sent.')
            );
            return $resultRedirect->setPath('*/*/');
        }

        $data = $this->dataProcessor->filter($data);
        $errors = $this->dataProcessor->validate($data);
        if (!empty($errors)) {
            /** @var Phrase $error */
            foreach ($errors as $error) {
                $this->messageManager->addErrorMessage($error);
            }

            return $resultRedirect->setPath('*/*/');
        }

        if (!empty($data[OfferInterface::KEY_ENTITY_ID])) {
            $offerId = $data[OfferInterface::KEY_ENTITY_ID];
            try {
                $offer = $this->offerRepository->getById($offerId);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('Could not find offer with id "%1"', $offerId));
                return $resultRedirect->setPath('*/*/');
            }
        } else {
            unset($data['entity_id']);
            $offer = $this->offerRepository->getNewInstance();
        }

        $offer->setData($data);

        try {
            $this->offerRepository->save($offer);
            $this->messageManager->addSuccessMessage(__('Offer has been saved.'));
            return $this->processResultRedirect($offer, $resultRedirect, $data);
        } catch (LocalizedException $e) {
            $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
        } catch (\Throwable $e) {
            echo $e->getMessage();
            $this->messageManager->addErrorMessage(__('Something went wrong while saving the offer: %1', $e->getMessage()));
        }

        return $resultRedirect->setPath('*/*/edit', $offerId ? ['offer_id' => $offerId] : []);
    }

    /**
     * @param OfferInterface $offer
     * @param Redirect $resultRedirect
     * @param array $data
     * @return Redirect
     * @throws LocalizedException
     */
    protected function processResultRedirect(OfferInterface $offer, Redirect $resultRedirect, array $data): Redirect
    {
        $back = $this->getRequest()->getParam('back', false);

        switch ($back) {
            case self::BACK_VALUE_DUPLICATION:
                $newOffer = $this->offerRepository->getNewInstance();
                $newOffer->setData($offer->getData());
                $newOffer->setData(OfferInterface::KEY_ENTITY_ID, null);
                $this->offerRepository->save($newOffer);
                $this->messageManager->addSuccessMessage(__('The offer #%1 has been duplicated.', $offer->getEntityId()));
                $resultRedirect->setPath('*/*/edit', ['offer_id' => $newOffer->getId()]);
                break;
            case self::BACK_VALUE_CLOSE:
                $resultRedirect->setPath('*/*/');
                break;
            default:
                $resultRedirect->setPath('*/*/edit', ['offer_id' => $offer->getId()]);
        }

        return $resultRedirect;
    }
}
