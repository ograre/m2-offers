<?php

namespace Ograre\Offers\Model\OfferLink\Processor;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Ograre\Offers\Api\OfferLinkProcessorInterface;

class Product implements OfferLinkProcessorInterface
{
    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        protected ProductRepositoryInterface $productRepository
    ) {}

    /**
     * @inheritDoc
     */
    public function process(string $value): string
    {
        $product = $this->productRepository->getById($value);
        return $product->getProductUrl();
    }

    /**
     * @inheritDoc
     */
    public function validate(string $value): bool
    {
        return preg_match('/^\d+$/i', $value);
    }
}
