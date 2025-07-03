<?php

namespace Ograre\Offers\Test\Unit\Controller\Offers;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Ograre\Offers\Controller\Adminhtml\Offers\Delete;
use Ograre\Offers\Model\ResourceModel\OfferRepository;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class DeleteTest extends TestCase
{
    protected $controller;
    protected $messageManagerMock;
    protected $requestMock;
    protected $offerRepositoryMock;
    protected $resultRedirectMock;
    protected $resultRedirectFactoryMock;
    protected $contextMock;

    protected $offerId = 1;

    protected function setUp(): void
    {
        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);

        $this->requestMock = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['getParam']
        );

        $this->offerRepositoryMock = $this->getMockBuilder(OfferRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['deleteById'])
            ->getMock();

        $this->resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setPath'])
            ->getMock();

        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();
        $this->resultRedirectFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->resultRedirectMock);

        $this->contextMock = $this->createMock(Context::class);
        $this->contextMock->expects($this->any())->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->expects($this->any())->method('getMessageManager')->willReturn($this->messageManagerMock);
        $this->contextMock->expects($this->any())->method('getResultRedirectFactory')->willReturn($this->resultRedirectFactoryMock);

        $objectManager = new ObjectManager($this);
        $this->controller = $objectManager->getObject(Delete::class, [
            'context' => $this->contextMock,
            'offerRepository' => $this->offerRepositoryMock
        ]);
    }

    public function testDeleteAction(): void
    {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('offer_id')
            ->willReturn($this->offerId);

        $this->offerRepositoryMock->expects($this->once())
            ->method('deleteById')
            ->with($this->offerId);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage');
        $this->messageManagerMock->expects($this->never())
            ->method('addErrorMessage');

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->controller->execute());
    }

    public function testDeleteNoId(): void
    {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('offer_id')
            ->willReturn(null);

        $this->offerRepositoryMock->expects($this->never())
            ->method('deleteById');

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage');

        $this->messageManagerMock->expects($this->never())
            ->method('addSuccessMessage');

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->controller->execute());
    }

    public function testDeleteActionThrowsException(): void
    {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('offer_id')
            ->willReturn($this->offerId);

        $this->offerRepositoryMock->expects($this->once())
            ->method('deleteById')
            ->willThrowException(new Exception());

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage');
        $this->messageManagerMock->expects($this->never())
            ->method('addSuccessMessage');

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->controller->execute());
    }
}
