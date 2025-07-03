<?php

namespace Ograre\Offers\Model\OfferLink\Processor;

use Ograre\Offers\Api\OfferLinkProcessorInterface;

class Url implements OfferLinkProcessorInterface
{
    protected const URL_MATCH_PATTERN = '/^https?:\/\/(?:www.)?[^\s\/$.?#][^\s]*$/i';

    /**
     * @inheritDoc
     */
    public function process(string $value): string
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function validate(string $value): bool
    {
        return preg_match(self::URL_MATCH_PATTERN, $value);
    }
}
