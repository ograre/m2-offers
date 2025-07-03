<?php

namespace Ograre\Offers\Controller\Adminhtml\Offers;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Ograre\Offers\Helper\OfferLink;

class PostDataProcessor
{
    /**
     * @param OfferLink $offerLinkHelper
     */
    public function __construct(
        protected OfferLink $offerLinkHelper
    ) {}

    /**
     * @param array $data
     * @return array
     */
    public function filter(array $data): array
    {
        if (!empty($data['link_type']) && !empty($data['link_value'])) {
            $composedLink = sprintf(
                '%s/%s',
                $data['link_type'],
                $data['link_value']
            );
            $data['link'] = $composedLink;
            unset($data['link_type']);
            unset($data['link_value']);
        }

        if (!empty($data['image'])) {
            $data['image'] = $data['image'][0]['name'];
        }

        return $data;
    }

    /**
     * @param array $data
     * @return Phrase[]
     * @throws LocalizedException
     */
    public function validate(array $data): array
    {
        $errors = [];
        return array_merge(
            $this->validateRequiredData($data),
            $this->validateLink($data),
            $errors
        );
    }

    /**
     * @param array $data
     * @return array
     */
    public function validateRequiredData(array $data): array
    {
        $errors = [];
        $requiredFields = [
            'title',
            'image',
            'link'
        ];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = __('%s is required.', $field);
            }
        }

        return $errors;
    }

    /**
     * @param array $data
     * @return array
     * @throws LocalizedException
     */
    public function validateLink(array $data): array
    {
        $errors = [];
        if (!$this->offerLinkHelper->isValidLink($data['link'])) {
            $errors[] = __('Invalid link value.');
        }

        return $errors;
    }
}
