<?php

namespace Ograre\Offers\Test\Unit\Controller\Offers;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Page\Title;
use Ograre\Offers\Controller\Adminhtml\Offers\Edit;
use Ograre\Offers\Model\Offer;
use Ograre\Offers\Model\ResourceModel\OfferRepository;
use PHPUnit\Framework\TestCase;
use Magento\Framework\Registry;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class EditTest extends TestCase
{
    protected $offerRepositoryMock;
    protected $offerMock;
    protected $registryMock;
    protected $resultPageFactoryMock;
    protected $resultPageMock;
    protected $messageManagerMock;
    protected $requestMock;
    protected $resultRedirectFactoryMock;
    protected $resultRedirectMock;
    protected $contextMock;
    protected $controller;

    protected $offerId = 1;

    protected function setUp(): void
    {
        $this->offerMock = $this->createMock(Offer::class);

        $this->offerRepositoryMock = $this->getMockBuilder(OfferRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getById', 'getNewInstance'])
            ->getMock();

        $this->offerRepositoryMock->expects($this->any())
            ->method('getNewInstance')
            ->willReturn($this->offerMock);

        $this->registryMock = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['register'])
            ->getMock();

        $pageTitle = $this->createMock(Title::class);
        $pageConfigMock = $this->createMock(PageConfig::class);
        $pageConfigMock->expects($this->any())
            ->method('getTitle')
            ->willReturn($pageTitle);

        $this->resultPageMock = $this->createMock(Page::class);
        $this->resultPageMock->expects($this->any())
            ->method('getConfig')
            ->willReturn($pageConfigMock);

        $this->resultPageFactoryMock = $this->createMock(PageFactory::class);

        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);

        $this->requestMock = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            true,
            true,
            []
        );

        $this->resultRedirectMock = $this->createMock(Redirect::class);

        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultRedirectFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->resultRedirectMock);

        $this->contextMock = $this->createMock(Context::class);
        $this->contextMock->method('getMessageManager')->willReturn($this->messageManagerMock);
        $this->contextMock->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->method('getResultRedirectFactory')->willReturn($this->resultRedirectFactoryMock);

        $this->controller = (new ObjectManager($this))->getObject(Edit::class, [
            'context' => $this->contextMock,
            'resultPageFactory' => $this->resultPageFactoryMock,
            'registry' => $this->registryMock,
            'offerRepository' => $this->offerRepositoryMock
        ]);
    }

    public function testEditAction(): void
    {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('offer_id')
            ->willReturn($this->offerId);

        $this->offerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($this->offerId)
            ->willReturn($this->offerMock);

        $this->registryMock->expects($this->once())
            ->method('register')
            ->with('offer', $this->offerMock);

        $this->resultPageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultPageMock);

        $this->messageManagerMock->expects($this->never())
            ->method('addErrorMessage');

        $this->resultRedirectFactoryMock->expects($this->never())
            ->method('create');

        $this->assertSame($this->resultPageMock, $this->controller->execute());
    }

    public function testEditActionNoId(): void
    {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('offer_id')
            ->willReturn(null);

        $this->offerRepositoryMock->expects($this->once())
            ->method('getNewInstance');

        $this->registryMock->expects($this->once())
            ->method('register');

        $this->resultPageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultPageMock);

        $this->messageManagerMock->expects($this->never())
            ->method('addErrorMessage');

        $this->resultRedirectFactoryMock->expects($this->never())
            ->method('create');

        $this->assertSame($this->resultPageMock, $this->controller->execute());
    }

    public function testEditActionNoOffer(): void
    {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->willReturn($this->offerId);

        $this->offerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($this->offerId)
            ->willThrowException(new NoSuchEntityException());

        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create');

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage');

        $this->offerRepositoryMock->expects($this->never())
            ->method('getNewInstance');

        $this->registryMock->expects($this->never())
            ->method('register');

        $this->resultPageFactoryMock->expects($this->never())
            ->method('create');

        $this->assertSame($this->resultRedirectMock, $this->controller->execute());
    }
}
