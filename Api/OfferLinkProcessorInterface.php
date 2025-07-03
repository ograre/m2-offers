<?php
namespace Ograre\Offers\Api;

interface OfferLinkProcessorInterface
{
    /**
     * @param string $value
     * @return string
     */
    public function process(string $value): string;

    /**
     * @param string $value
     * @return bool
     */
    public function validate(string $value) : bool;
}
