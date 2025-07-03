<?php
namespace Ograre\Offers\Helper;

use Ograre\Offers\Api\OfferLinkProcessorInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;

class OfferLink extends AbstractHelper
{
    protected const LINK_PATTERN = '/^(?<type>[a-z0-9_-]+)\/(?<value>.+)$/';

    /**
     * @param Context $context
     * @param array $processors
     * @throws LocalizedException
     */
    public function __construct (
        Context $context,
        protected array $processors
    ) {
        parent::__construct($context);
        foreach ($this->processors as $processor) {
            if (!($processor instanceof OfferLinkProcessorInterface)) {
                throw new LocalizedException(__('Link processor must implement Ograre\Offers\Api\OfferLinkProcessorInterface'));
            }
        }
    }

    /**
     * @param string $link
     * @return string
     * @throws LocalizedException
     */
    public function processLink(string $link) : string
    {
        if (preg_match(self::LINK_PATTERN, $link, $matches)) {
            return $this->selectProcessor($link)->process($matches['value']);
        }

        throw new LocalizedException(__('Link provided is not valid.'));
    }

    /**
     * @param string $link
     * @return bool
     * @throws LocalizedException
     */
    public function isValidLink(string $link) : bool
    {
        return $this->selectProcessor($link)->validate($this->getLinkValue($link));
    }

    /**
     * @return array
     */
    public function getAllLinks() : array
    {
        return array_keys($this->processors);
    }

    /**
     * @param string $link
     * @return string
     * @throws LocalizedException
     */
    public function getLinkType (string $link) : string
    {
        preg_match(self::LINK_PATTERN, $link, $matches);
        if (!isset($matches['type'])) {
            throw new LocalizedException(__('Could not find type for link "%1"', $link));
        }

        return $matches['type'];
    }

    /**
     * @param string $link
     * @return string
     * @throws LocalizedException
     */
    public function getLinkValue (string $link) : string
    {
        preg_match(self::LINK_PATTERN, $link, $matches);
        if (!isset($matches['value'])) {
            throw new LocalizedException(__('Could not find value for link "%1"', $link));
        }

        return $matches['value'];
    }

    /**
     * @param string $link
     * @return OfferLinkProcessorInterface
     * @throws LocalizedException
     */
    protected function selectProcessor(string $link): OfferLinkProcessorInterface
    {
        $linkType = $this->getLinkType($link);
        if (!isset($this->processors[$linkType])) {
            throw new LocalizedException(__('Could not find Offer Link processor for type "%1"', $linkType));
        }

        return $this->processors[$linkType];
    }
}
