<?php

namespace Ograre\Offers\Test\Unit\Controller\Offers;

use ArrayIterator;
use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Ui\Component\MassAction\Filter;
use Ograre\Offers\Api\Data\OfferInterface;
use Ograre\Offers\Controller\Adminhtml\Offers\MassDelete;
use Ograre\Offers\Model\ResourceModel\Offer\Collection;
use Ograre\Offers\Model\ResourceModel\Offer\CollectionFactory;
use Ograre\Offers\Model\ResourceModel\OfferRepository;
use PHPUnit\Framework\TestCase;

class MassDeleteTest extends TestCase
{
    protected $filterMock;
    protected $offerCollectionMock;
    protected $offerCollectionFactoryMock;
    protected $offerRepositoryMock;
    protected $resultRedirectMock;
    protected $resultRedirectFactoryMock;
    protected $messageManagerMock;
    protected $contextMock;
    protected $controller;

    protected function setUp(): void
    {
        $this->filterMock = $this->getMockBuilder(Filter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->offerCollectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getSize', 'getIterator'])
            ->getMock();

        $this->filterMock->expects($this->any())
            ->method('getCollection')
            ->with($this->offerCollectionMock)
            ->willReturn($this->offerCollectionMock);

        $this->offerCollectionFactoryMock = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->offerCollectionFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->offerCollectionMock);

        $this->offerRepositoryMock = $this->getMockBuilder(OfferRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();

        $this->resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setPath'])
            ->getMock();

        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();
        $this->resultRedirectFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->resultRedirectMock);

        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);

        $this->contextMock = $this->createMock(Context::class);
        $this->contextMock->expects($this->any())->method('getMessageManager')->willReturn($this->messageManagerMock);
        $this->contextMock->expects($this->any())->method('getResultRedirectFactory')->willReturn($this->resultRedirectFactoryMock);

        $this->controller = (new ObjectManager($this))->getObject(MassDelete::class, [
            'context' => $this->contextMock,
            'massActionFilter' => $this->filterMock,
            'offerCollectionFactory' => $this->offerCollectionFactoryMock,
            'offerRepository' => $this->offerRepositoryMock
        ]);
    }

    public function testMassDeleteAction(): void
    {
        $offeritems = new ArrayIterator([
            $this->getOfferMock(),
            $this->getOfferMock()
        ]);
        $this->offerCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn(2);

        $this->offerCollectionMock->expects($this->once())
            ->method('getIterator')
            ->willReturn($offeritems);

        $this->offerRepositoryMock->expects($this->exactly(2))
            ->method('delete')
            ->willReturn(true);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage');

        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create');

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->controller->execute());
    }

    public function testMassDeleteActionWithoutOffers(): void
    {
        $this->offerCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn(0);
        $this->offerCollectionMock->expects($this->once())
            ->method('getIterator')
            ->willReturn(new ArrayIterator([]));

        $this->offerRepositoryMock->expects($this->never())
            ->method('delete');

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage');

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->controller->execute());
    }

    public function testMassDeleteActionThrowsException(): void
    {
        $offeritems = new ArrayIterator([
            $this->getOfferMock(),
            $this->getOfferMock()
        ]);
        $this->offerCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn(2);

        $this->offerCollectionMock->expects($this->once())
            ->method('getIterator')
            ->willReturn($offeritems);

        $this->offerRepositoryMock->expects($this->exactly(2))
            ->method('delete')
            ->willThrowException(new Exception());

        $this->messageManagerMock->expects($this->exactly(2))
            ->method('addErrorMessage');

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage');

        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create');

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->controller->execute());
    }

    protected function getOfferMock(): object
    {
        return $this->createMock(OfferInterface::class);
    }
}
