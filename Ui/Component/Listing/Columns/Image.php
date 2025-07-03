<?php

namespace Ograre\Offers\Ui\Component\Listing\Columns;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Ograre\Offers\Helper\Image as OfferImageHelper;

class Image extends Column
{
    public const NAME = 'image';
    public const ALT_FIELD = 'entity_id';

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param OfferImageHelper $imageHelper
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        protected OfferImageHelper $imageHelper,
        protected UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $offer = new DataObject($item);
                $item[$fieldName . '_src'] = $this->imageHelper->getOfferImageUrl($offer);
                $item[$fieldName . '_alt'] = $this->getAlt($item);
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl('ograre/offer/edit', ['offer_id' => $offer->getId()]);
            }
        }

        return $dataSource;
    }

    /**
     * @param array $row
     * @return string
     */
    protected function getAlt(array $row): string
    {
        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
        return html_entity_decode($row[$altField], ENT_QUOTES, 'UTF-8') ?? '';
    }
}
