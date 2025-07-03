<?php

namespace Ograre\Offers\Test\Unit\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Ograre\Offers\Api\Data\OfferInterface;
use Ograre\Offers\Helper\Image;
use Ograre\Offers\Model\Offer;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    protected $contextMock;
    protected $storeMock;
    protected $storeManagerMock;
    protected $offerMock;
    protected $helper;
    protected $offerImage = 'test_image.jpg';
    protected $baseUrl = 'http://test.com/static/media/';
    protected $offerImageFolder = OfferInterface::IMAGE_FOLDER;

    protected function setUp(): void
    {
        $this->contextMock = $this->createMock(Context::class);

        $this->storeMock = $this->getMockbuilder(Store::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getBaseUrl'])
            ->getMock();
        $this->storeMock->expects($this->any())
            ->method('getBaseUrl')
            ->with(UrlInterface::URL_TYPE_MEDIA)
            ->willReturn($this->baseUrl);

        $this->storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStore'])
            ->getMockForAbstractClass();
        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $this->offerMock = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getImage'])
            ->getMock();
        $this->offerMock->expects($this->any())
            ->method('getImage')
            ->willReturn($this->offerImage);

        $this->helper = (new ObjectManager($this))->getObject(Image::class, [
            'context' => $this->contextMock,
            'storeManager' => $this->storeManagerMock
        ]);
    }

    public function testGetOfferImageUrl(): void
    {
        $resultExpecte = $this->baseUrl.$this->offerImageFolder.'/'.$this->offerImage;
        $this->assertEquals($resultExpecte, $this->helper->getOfferImageUrl($this->offerMock));
    }

    public function testGetImageUrlFromFilename(): void
    {
        $resultExpecte = $this->baseUrl.$this->offerImageFolder.'/'.$this->offerImage;
        $this->assertEquals($resultExpecte, $this->helper->getImageUrlFromFilename($this->offerImage));
    }
}
