<?php

namespace Ograre\Offers\Controller\Adminhtml\Offers;

use Magento\Backend\App\AbstractAction;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;

class NewAction extends AbstractAction implements HttpGetActionInterface
{
    /**
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        protected ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        return $this->resultForwardFactory->create()
            ->forward('edit');
    }
}
