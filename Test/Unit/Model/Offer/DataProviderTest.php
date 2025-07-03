<?php

namespace Ograre\Offers\Test\Unit\Model\Offer;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Ograre\Offers\Model\Offer;
use Ograre\Offers\Model\Offer\DataProvider;
use Ograre\Offers\Model\ResourceModel\Offer\CollectionFactory;
use Ograre\Offers\Model\ResourceModel\OfferRepository;
use PHPUnit\Framework\TestCase;

class DataProviderTest extends TestCase
{
    protected $name = 'name';
    protected $primaryFieldName = 'offer_id';
    protected $requestFieldName = 'entity_id';
    protected $offerCollectionFactoryMock;
    protected$offerCollectionMock;
    protected $requestMock;
    protected $offerRepositoryMock;
    protected $offerMock;
    protected $dataPersistorMock;
    protected $poolMock;
    protected $dataProvider;

    protected $offerId = 1;

    protected function setUp(): void
    {
        $this->offerCollectionFactoryMock = $this->createMock(CollectionFactory::class);

        $this->requestMock = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['getParam']
        );

        $this->offerMock = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId', 'getLinkType', 'getLinkValue', 'setData', 'getData'])
            ->getMock();

        $this->offerRepositoryMock = $this->getMockBuilder(OfferRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getById', 'getNewInstance'])
            ->getMock();
        $this->offerRepositoryMock->expects($this->any())
            ->method('getById')
            ->with($this->offerId)
            ->willReturn($this->offerMock);

        $this->dataPersistorMock = $this->getMockForAbstractClass(DataPersistorInterface::class);

        $this->poolMock = $this->getMockBuilder(PoolInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getModifiersInstances'])
            ->getMockForAbstractClass();
        $this->poolMock->expects($this->any())
            ->method('getModifiersInstances')
            ->willReturn([]);

        $this->dataProvider = (new ObjectManager($this))->getObject(DataProvider::class, [
            'name' => $this->name,
            'primaryFieldName' => $this->primaryFieldName,
            'requestFieldName' => $this->requestFieldName,
            'offerCollectionFactory' => $this->offerCollectionFactoryMock,
            'request' => $this->requestMock,
            'offerRepository' => $this->offerRepositoryMock,
            'dataPersistor' => $this->dataPersistorMock,
            'meta' => [],
            'data' => [],
            'pool' => $this->poolMock
        ]);
    }

    public function testGetData(): void
    {
        $offerData = [
            'entity_id' => $this->offerId,
            'title' => 'title test',
            'link_type' => 'product',
            'link_value' => '5',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
            'category_ids' => [1, 2, 3]
        ];
        $expectedData = [$this->offerId => $offerData];
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with($this->requestFieldName)
            ->willReturn($this->offerId);

        $this->offerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($this->offerId)
            ->willReturn($this->offerMock);

        $this->offerRepositoryMock->expects($this->never())
            ->method('getNewInstance');

        $this->offerMock->expects($this->once())
            ->method('getLinkType');
        $this->offerMock->expects($this->once())
            ->method('getLinkValue');
        $this->offerMock->expects($this->once())
            ->method('getData')
            ->willReturn($offerData);

        $this->offerMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->offerId);

        $this->assertEquals($expectedData, $this->dataProvider->getData());
    }

    /**
     * @param array $offerData
     * @param array $expectedOfferData
     * @return void
     *
     * @dataProvider getDataNoIdDataProvider
     */
    public function testGetDataNoId(array $offerData, array $expectedOfferData): void
    {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with($this->requestFieldName, 0)
            ->willReturn(0);

        $this->offerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with(0)
            ->willThrowException(new NoSuchEntityException(__('Offer not found')));

        $this->offerRepositoryMock->expects($this->once())
            ->method('getNewInstance')
            ->willReturn($this->offerMock);

        $this->dataPersistorMock->expects($this->once())
            ->method('get')
            ->with('offer')
            ->willReturn($offerData);

        $this->offerMock->expects(!empty($offerData) ? $this->once() : $this->never())
            ->method('setData')
            ->with($offerData);
        $this->offerMock->expects($this->once())
            ->method('getLinkType');
        $this->offerMock->expects($this->once())
            ->method('getLinkValue');
        $this->offerMock->expects($this->once())
            ->method('getData')
            ->willReturn($offerData);

        $this->assertEquals($expectedOfferData, $this->dataProvider->getData());
    }

    /**
     * @return array[]
     */
    protected function getDataNoIdDataProvider(): array
    {
        return [ //data sets
            'dataPersistor with data' => [ //data set 1
                 [ // offerData
                    'title' => 'title test',
                    'link_type' => 'product',
                    'link_value' => '5',
                    'start_date' => '2025-01-01',
                    'end_date' => '2025-12-31',
                    'category_ids' => [1, 2, 3]
                ],
                ['' => [ // expectedOfferData
                    'title' => 'title test',
                    'link_type' => 'product',
                    'link_value' => '5',
                    'start_date' => '2025-01-01',
                    'end_date' => '2025-12-31',
                    'category_ids' => [1, 2, 3]
                ]]
            ],
            'dataPersistor without data' => [[], ['' => []]]
        ];
    }
}
