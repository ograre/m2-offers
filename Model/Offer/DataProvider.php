<?php
namespace Ograre\Offers\Model\Offer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;
use Ograre\Offers\Api\Data\OfferInterface;
use Ograre\Offers\Api\OfferRepositoryInterface;
use Ograre\Offers\Helper\OfferLink;
use Ograre\Offers\Model\ResourceModel\Offer\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

class DataProvider extends ModifierPoolDataProvider
{
    /** @var array $loadedData */
    protected $loadedData;

    /** @var PoolInterface $pool */
    protected $pool;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $offerCollectionFactory
     * @param RequestInterface $request
     * @param OfferRepositoryInterface $offerRepository
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        protected CollectionFactory $offerCollectionFactory,
        protected RequestInterface $request,
        protected OfferRepositoryInterface $offerRepository,
        protected DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = [],
        ?PoolInterface $pool = null
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
        $this->collection = $this->offerCollectionFactory->create();
        $this->pool = $pool ?: ObjectManager::getInstance()->get(PoolInterface::class);
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $offer = $this->getCurrentOffer();
        $offerData = $offer->getData();

        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $offerData = $modifier->modifyData($offerData);
        }

        $this->loadedData[$offer->getId()] = $offerData;

        return $this->loadedData;
    }

    /**
     * @return OfferInterface
     */
    protected function getCurrentOffer(): OfferInterface
    {
        try {
            $offer = $this->offerRepository->getById($this->getOfferId());
        } catch (NoSuchEntityException $e) {
            $offer = $this->offerRepository->getNewInstance();
            if ($data = $this->dataPersistor->get('offer')) {
                $this->dataPersistor->clear('offer');
                $offer->setData($data);
            }
        }

        // init those values
        $offer->getLinkType();
        $offer->getLinkValue();

        return $offer;
    }

    /**
     * @return int
     */
    protected function getOfferId(): int
    {
        return (int)$this->request->getParam($this->getRequestFieldName(), 0);
    }
}
