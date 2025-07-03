<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ograre\Offers\Block\Adminhtml\Offer\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Ograre\Offers\Api\OfferRepositoryInterface;

/**
 * Class GenericButton
 */
class GenericButton
{
    /**
     * @param Context $context
     * @param OfferRepositoryInterface $offerRepository
     */
    public function __construct(
        protected Context $context,
        protected OfferRepositoryInterface $offerRepository
    ) {}

    /**
     * Return Offer ID
     *
     * @return int|null
     */
    public function getOfferId(): ?int
    {
        try {
            return $this->offerRepository->getById(
                (int)$this->context->getRequest()->getParam('offer_id', 0)
            )->getId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl(string $route = '', array $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
