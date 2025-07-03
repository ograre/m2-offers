<?php
namespace Ograre\Offers\Ui\Component;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiDataProvider;
use Ograre\Offers\Api\Data\OfferInterface;
use Ograre\Offers\Helper\OfferLink;

class DataProvider extends UiDataProvider
{
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param OfferLink $offerLinkHelper
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        protected OfferLink $offerLinkHelper,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $meta, $data);
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getData()
    {
        $data = parent::getData();
        foreach ($data['items'] as &$item) {
            $item[OfferInterface::KEY_LINK_TYPE] = $this->offerLinkHelper->getLinkType($item[OfferInterface::KEY_LINK]);
        }

        return $data;
    }
}
