<?php

namespace Ograre\Offers\Controller\Adminhtml\Offers\Image;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Ograre\Offers\Api\Data\OfferFileProcessorInterface;
use Magento\Framework\Controller\ResultFactory;

class Upload extends Action
{
    /**
     * @param Context $context
     * @param OfferFileProcessorInterface $fileProcessor
     */
    public function __construct(
        Context $context,
        protected OfferFileProcessorInterface $fileProcessor
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $imageId = $this->getRequest()->getParam('param_name', 'image');

        try {
            $result = $this->fileProcessor->saveToTmp($imageId);
        } catch (Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
