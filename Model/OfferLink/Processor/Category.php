<?php

namespace Ograre\Offers\Model\OfferLink\Processor;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Ograre\Offers\Api\OfferLinkProcessorInterface;

class Category implements OfferLinkProcessorInterface
{
    /**
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository
    ) {}

    /**
     * @inheritDoc
     */
    public function process(string $value): string
    {
        $category = $this->categoryRepository->get($value);
        return $category->getUrl();
    }

    /**
     * @inheritDoc
     */
    public function validate(string $value): bool
    {
        return preg_match('/^\d+$/i', $value);
    }
}
