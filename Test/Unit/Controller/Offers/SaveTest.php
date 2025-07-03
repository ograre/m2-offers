<?php

namespace Ograre\Offers\Test\Unit\Controller\Offers;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Ograre\Offers\Controller\Adminhtml\Offers\PostDataProcessor;
use Ograre\Offers\Controller\Adminhtml\Offers\Save;
use Ograre\Offers\Model\Offer;
use Ograre\Offers\Model\ResourceModel\OfferRepository;
use PHPUnit\Framework\TestCase;

class SaveTest extends TestCase
{
    protected $offerRepositoryMock;
    protected $dataProcessorMock;
    protected $resultRedirectMock;
    protected $resultRedirectFactoryMock;
    protected $messageManagerMock;
    protected $requestMock;
    protected $contextMock;
    protected $controller;

    protected $offerId = 1;
    protected $newOfferId = 2;

    protected function setUp(): void
    {
        $this->offerRepositoryMock = $this->getMockBuilder(OfferRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getById', 'getNewInstance', 'save'])
            ->getMock();

        $this->dataProcessorMock = $this->getMockBuilder(PostDataProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultRedirectMock = $this->createMock(Redirect::class);

        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultRedirectFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->resultRedirectMock);

        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);

        $this->requestMock = $this->getMockBuilder(RequestInterface::class)
            ->addMethods(['getPostValue'])
            ->onlyMethods(['getParam'])
            ->getMockForAbstractClass();

        $this->contextMock = $this->createMock(Context::class);
        $this->contextMock->method('getMessageManager')->willReturn($this->messageManagerMock);
        $this->contextMock->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->method('getResultRedirectFactory')->willReturn($this->resultRedirectFactoryMock);

        $this->controller = (new ObjectManager($this))->getObject(Save::class, [
            'context' => $this->contextMock,
            'offerRepository' => $this->offerRepositoryMock,
            'dataProcessor' => $this->dataProcessorMock
        ]);
    }

    public function testSaveActionWithId(): void
    {
        $postData = [
            'entity_id' => $this->offerId,
            'title' => 'test title',
            'image' => [[
                'name' => 'image.jpg',
                'file' => 'image.jpg'
            ]],
            'link_type' => 'product',
            'link_value' => 5,
            'start_date' => '2019-01-01',
            'end_date' => '2019-01-02',
            'category_ids' => [1, 2, 3]
        ];

        $filteredData = [
            'entity_id' => $this->offerId,
            'title' => 'test title',
            'image' => 'image.jpg',
            'link' => 'product/5',
            'start_date' => '2019-01-01',
            'end_date' => '2019-01-02',
            'category_ids' => [1, 2, 3]
        ];

        $this->dataProcessorMock->expects($this->any())
            ->method('filter')
            ->with($postData)
            ->willReturn($filteredData);

        $this->dataProcessorMock->expects($this->any())
            ->method('validate')
            ->with($filteredData)
            ->willReturn([]);

        $this->requestMock->expects($this->any())
            ->method('getPostValue')
            ->willReturn($postData);

        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->with('back')
            ->willReturn(false);

        $offer = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->offerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($this->offerId)
            ->willReturn($offer);

        $this->offerRepositoryMock->expects($this->never())
            ->method('getNewInstance');

        $offer->expects($this->once())->method('setData');
        $offer->expects($this->any())->method('getId')->willReturn($this->offerId);

        $this->offerRepositoryMock->expects($this->once())
            ->method('save')
            ->with($offer);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage');
        $this->messageManagerMock->expects($this->never())
            ->method('addErrorMessage');

        $this->resultRedirectMock->expects($this->atLeastOnce())
            ->method('setPath')
            ->with('*/*/edit')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->controller->execute());
    }

    public function testSaveActionNoId(): void
    {
        $postData = [
            'title' => 'test title',
            'image' => [[
                'name' => 'image.jpg',
                'file' => 'image.jpg'
            ]],
            'link_type' => 'product',
            'link_value' => 5,
            'start_date' => '2019-01-01',
            'end_date' => '2019-01-02',
            'category_ids' => [1, 2, 3]
        ];

        $filteredData = [
            'title' => 'test title',
            'image' => 'image.jpg',
            'link' => 'product/5',
            'start_date' => '2019-01-01',
            'end_date' => '2019-01-02',
            'category_ids' => [1, 2, 3]
        ];

        $this->dataProcessorMock->expects($this->any())
            ->method('filter')
            ->with($postData)
            ->willReturn($filteredData);

        $this->dataProcessorMock->expects($this->any())
            ->method('validate')
            ->with($filteredData)
            ->willReturn([]);

        $this->requestMock->expects($this->any())
            ->method('getPostValue')
            ->willReturn($postData);

        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->with('back')
            ->willReturn(false);

        $offer = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->offerRepositoryMock->expects($this->never())
            ->method('getById');

        $this->offerRepositoryMock->expects($this->once())
            ->method('getNewInstance')
            ->willReturn($offer);

        $offer->expects($this->once())->method('setData');

        $this->offerRepositoryMock->expects($this->once())
            ->method('save')
            ->with($offer);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage');
        $this->messageManagerMock->expects($this->never())
            ->method('addErrorMessage');

        $this->resultRedirectMock->expects($this->atLeastOnce())
            ->method('setPath')
            ->with('*/*/edit')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->controller->execute());
    }

    public function testSaveActionAndDuplicate(): void
    {
        $postData = [
            'entity_id' => $this->offerId,
            'title' => 'test title',
            'image' => [[
                'name' => 'image.jpg',
                'file' => 'image.jpg'
            ]],
            'link_type' => 'product',
            'link_value' => 5,
            'start_date' => '2019-01-01',
            'end_date' => '2019-01-02',
            'category_ids' => [1, 2, 3]
        ];

        $filteredData = [
            'entity_id' => $this->offerId,
            'title' => 'test title',
            'image' => 'image.jpg',
            'link' => 'product/5',
            'start_date' => '2019-01-01',
            'end_date' => '2019-01-02',
            'category_ids' => [1, 2, 3]
        ];

        $newOfferData = [
            'entity_id' => $this->newOfferId,
            'title' => 'test title',
            'image' => 'image.jpg',
            'link' => 'product/5',
            'start_date' => '2019-01-01',
            'end_date' => '2019-01-02',
            'category_ids' => [1, 2, 3]
        ];

        $this->dataProcessorMock->expects($this->any())
            ->method('filter')
            ->with($postData)
            ->willReturn($filteredData);

        $this->dataProcessorMock->expects($this->any())
            ->method('validate')
            ->with($filteredData)
            ->willReturn([]);

        $this->requestMock->expects($this->any())
            ->method('getPostValue')
            ->willReturn($postData);

        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->with('back', false)
            ->willReturn('duplicate');

        $offer = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $newOffer = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->offerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($this->offerId)
            ->willReturn($offer);

        $this->offerRepositoryMock->expects($this->once())
            ->method('getNewInstance')
            ->willReturn($newOffer);

        $offer->expects($this->atLeastOnce())->method('setData');
        $this->offerRepositoryMock->expects($this->atLeastOnce())
            ->method('save')
            ->with($offer);

        $offer->expects($this->once())->method('getData')->willReturn($newOfferData);
        $newOffer->expects($this->atLeastOnce())->method('setData');
        $this->offerRepositoryMock->expects($this->atLeastOnce())
            ->method('save')
            ->with($newOffer);

        $this->messageManagerMock->expects($this->atLeastOnce())
            ->method('addSuccessMessage');
        $this->messageManagerMock->expects($this->never())
            ->method('addErrorMessage');

        $newOffer->expects($this->any())->method('getId')->willReturn($this->newOfferId);
        $this->resultRedirectMock->expects($this->atLeastOnce())
            ->method('setPath')
            ->with('*/*/edit', ['offer_id' => $this->newOfferId])
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->controller->execute());
    }

    public function testSaveActionWithoutData(): void
    {
        $postData = [];

        $filteredData = [];

        $this->dataProcessorMock->expects($this->any())
            ->method('filter')
            ->with($postData)
            ->willReturn($filteredData);

        $this->dataProcessorMock->expects($this->any())
            ->method('validate')
            ->with($filteredData)
            ->willReturn([__('ERROR')]);

        $this->requestMock->expects($this->any())
            ->method('getPostValue')
            ->willReturn($postData);

        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->with('back')
            ->willReturn(false);

        $this->offerRepositoryMock->expects($this->never())
            ->method('getById');

        $this->offerRepositoryMock->expects($this->never())
            ->method('getNewInstance');


        $this->offerRepositoryMock->expects($this->never())
            ->method('save');

        $this->messageManagerMock->expects($this->never())
            ->method('addSuccessMessage');
        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage');

        $this->resultRedirectMock->expects($this->atLeastOnce())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->controller->execute());
    }

    public function testSaveActionThrowsException(): void
    {
        $postData = [
            'entity_id' => $this->offerId,
            'title' => 'test title',
            'image' => [[
                'name' => 'image.jpg',
                'file' => 'image.jpg'
            ]],
            'link_type' => 'product',
            'link_value' => 5,
            'start_date' => '2019-01-01',
            'end_date' => '2019-01-02',
            'category_ids' => [1, 2, 3]
        ];

        $filteredData = [
            'entity_id' => $this->offerId,
            'title' => 'test title',
            'image' => 'image.jpg',
            'link' => 'product/5',
            'start_date' => '2019-01-01',
            'end_date' => '2019-01-02',
            'category_ids' => [1, 2, 3]
        ];

        $this->dataProcessorMock->expects($this->any())
            ->method('filter')
            ->with($postData)
            ->willReturn($filteredData);

        $this->dataProcessorMock->expects($this->any())
            ->method('validate')
            ->with($filteredData)
            ->willReturn([]);

        $this->requestMock->expects($this->any())
            ->method('getPostValue')
            ->willReturn($postData);

        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->with('back')
            ->willReturn(false);

        $offer = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->offerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($this->offerId)
            ->willReturn($offer);

        $this->offerRepositoryMock->expects($this->never())
            ->method('getNewInstance');

        $offer->expects($this->once())->method('setData');
        $offer->expects($this->any())->method('getId')->willReturn($this->offerId);

        $this->offerRepositoryMock->expects($this->once())
            ->method('save')
            ->with($offer)
            ->willThrowException(new Exception());

        $this->messageManagerMock->expects($this->never())
            ->method('addSuccessMessage');
        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage');

        $this->resultRedirectMock->expects($this->atLeastOnce())
            ->method('setPath')
            ->with('*/*/edit', ['offer_id' => $this->offerId])
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->controller->execute());
    }
}
