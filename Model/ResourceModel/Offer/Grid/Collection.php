<?php
namespace Ograre\Offers\Model\ResourceModel\Offer\Grid;

use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Ograre\Offers\Model\ResourceModel\Offer\Collection as OfferCollection;
use Psr\Log\LoggerInterface;

class Collection extends OfferCollection implements SearchResultInterface
{
    protected AggregationInterface $aggregations;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param string $mainTable
     * @param $_eventPrefix
     * @param $_eventObject
     * @param $resourceModel
     * @param $model
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface        $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface       $eventManager,
        string                 $mainTable,
        protected              $_eventPrefix,
        protected              $_eventObject,
        protected              $resourceModel,
        protected              $model = Document::class,
        ?AdapterInterface      $connection = null,
        ?AbstractDb            $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_init($this->model, $this->resourceModel);
        $this->setMainTable($mainTable);
    }

    /**
     * @param array|null $items
     * @return $this|Collection
     */
    public function setItems(?array $items = null)
    {
        return $this;
    }

    /**
     * Get aggregation interface instance
     *
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * Set aggregation interface instance
     *
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * @inheritdoc
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }
}
