<?php

namespace Ograre\Offers\Model\Offer\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Ograre\Offers\Helper\OfferLink;

class LinkType implements OptionSourceInterface
{
    /**
     * @param OfferLink $offerLinkHelper
     */
    public function __construct(
        protected OfferLink $offerLinkHelper
    ) {}

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $offerLinks = $this->offerLinkHelper->getAllLinks();
        $options = [];

        foreach ($offerLinks as $offerLink) {
            $options[] = [
                'label' => $offerLink,
                'value' => $offerLink
            ];
        }

        return $options;
    }
}
