<?php

namespace Ograre\Offers\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Ograre\Offers\Api\Data\OfferInterface;

class Image extends AbstractHelper implements ArgumentInterface
{
    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        protected StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
    }

    /**
     * @param DataObject $offer
     * @return string
     * @throws NoSuchEntityException
     */
    public function getOfferImageUrl(DataObject $offer): string
    {
        $store = $this->storeManager->getStore();
        $mediaBaseUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        return sprintf(
            '%s%s/%s',
            $mediaBaseUrl,
            OfferInterface::IMAGE_FOLDER,
            $offer->getImage()
        );
    }

    /**
     * @param string $filename
     * @return string
     * @throws NoSuchEntityException
     */
    public function getImageUrlFromFilename(string $filename): string
    {
        $store = $this->storeManager->getStore();
        $mediaBaseUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        return sprintf(
            '%s%s/%s',
            $mediaBaseUrl,
            OfferInterface::IMAGE_FOLDER,
            $filename
        );
    }
}
