<?php

namespace Ograre\Offers\Test\Unit\Model\ResourceModel;

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Ograre\Offers\Api\Data\OfferInterface;
use Ograre\Offers\Api\Data\OfferInterfaceFactory;
use Ograre\Offers\Api\Data\OfferSearchResultsInterfaceFactory;
use Ograre\Offers\Model\Offer as OfferModel;
use Ograre\Offers\Model\OfferSearchResults;
use Ograre\Offers\Model\ResourceModel\Offer as OfferResource;
use Ograre\Offers\Model\ResourceModel\OfferRepository;
use Ograre\Offers\Model\ResourceModel\Offer\Collection as OfferCollection;
use Ograre\Offers\Model\ResourceModel\Offer\CollectionFactory;
use PHPUnit\Framework\TestCase;

class OfferRepositoryTest extends TestCase
{
    protected $offerResourceMock;
    protected $offerFactoryMock;
    protected $offerMock;
    protected $collectionFactoryMock;
    protected $offerCollectionMock;
    protected $offerSearchResultsFactoryMock;
    protected $offerSearchResultsMock;
    protected $collectionProcessorMock;
    protected $offerRepository;

    protected $offerId = 1;

    protected function setUp(): void
    {
        $this->offerResourceMock = $this->getMockBuilder(OfferResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'delete', 'load'])
            ->getMock();

        $this->offerMock = $this->createMock(OfferModel::class);

        $this->offerFactoryMock = $this->getMockBuilder(OfferInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMockForAbstractClass();
        $this->offerFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->offerMock);

        $this->offerCollectionMock = $this->createMock(OfferCollection::class);

        $this->collectionFactoryMock = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();
        $this->collectionFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->offerCollectionMock);

        $this->offerSearchResultsMock = $this->createMock(OfferSearchResults::class);

        $this->offerSearchResultsFactoryMock = $this->getMockBuilder(OfferSearchResultsInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMockForAbstractClass();
        $this->offerSearchResultsFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->offerSearchResultsMock);

        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);

        $this->offerRepository = (new ObjectManager($this))->getObject(OfferRepository::class, [
            'offerResource' => $this->offerResourceMock,
            'offerFactory' => $this->offerFactoryMock,
            'collectionFactory' => $this->collectionFactoryMock,
            'searchResultsFactory' => $this->offerSearchResultsFactoryMock,
            'collectionProcessor' => $this->collectionProcessorMock
        ]);
    }

    public function testGetById(): void
    {
        $this->offerResourceMock->expects($this->once())
            ->method('load')
            ->with($this->offerMock, $this->offerId, OfferInterface::KEY_ENTITY_ID)
            ->willReturnSelf();

        $this->offerMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->offerId);

        $this->assertEquals($this->offerMock, $this->offerRepository->getById($this->offerId));
    }

    public function testGetByIdNoEntity(): void
    {
        $this->offerResourceMock->expects($this->once())
            ->method('load')
            ->with($this->offerMock, 0, OfferInterface::KEY_ENTITY_ID)
            ->willReturnSelf();

        $this->offerMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $this->expectException(NoSuchEntityException::class);
        $this->offerRepository->getById(0);
    }

    public function testGetList(): void
    {
        $searchCriteria = $this->getMockForAbstractClass(SearchCriteriaInterface::class);

        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteria, $this->offerCollectionMock);

        $this->offerCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$this->offerMock]);

        $this->offerCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn(1);

        $this->offerSearchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteria);

        $this->assertSame($this->offerSearchResultsMock, $this->offerRepository->getList($searchCriteria));
    }

    public function testSave(): void
    {
        $this->offerResourceMock->expects($this->once())
            ->method('save')
            ->with($this->offerMock)
            ->willReturnSelf();

        $this->assertSame($this->offerMock, $this->offerRepository->save($this->offerMock));
    }

    public function testDelete(): void
    {
        $this->offerResourceMock->expects($this->once())
            ->method('delete')
            ->with($this->offerMock)
            ->willReturnSelf();

        $this->assertTrue($this->offerRepository->delete($this->offerMock));
    }

    public function testDeleteThrowsException(): void
    {
        $this->offerResourceMock->expects($this->once())
            ->method('delete')
            ->with($this->offerMock)
            ->willThrowException(new Exception());

        $this->expectException(CouldNotDeleteException::class);
        $this->offerRepository->delete($this->offerMock);
    }

    public function testDeleteById(): void
    {
        $this->offerMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->offerId);

        $this->offerResourceMock->expects($this->once())
            ->method('load')
            ->with($this->offerMock, $this->offerId, OfferInterface::KEY_ENTITY_ID)
            ->willReturn($this->offerMock);

        $this->offerResourceMock->expects($this->once())
            ->method('delete')
            ->with($this->offerMock)
            ->willReturnSelf();

        $this->assertTrue($this->offerRepository->deleteById($this->offerId));
    }

    public function testGetNewInstance(): void
    {
        $this->assertEquals($this->offerMock, $this->offerRepository->getNewInstance());
    }
}
