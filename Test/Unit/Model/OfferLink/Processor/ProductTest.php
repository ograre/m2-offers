<?php

namespace Ograre\Offers\Test\Unit\Model\OfferLink\Processor;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Ograre\Offers\Model\OfferLink\Processor\Product as ProductProcessor;

class ProductTest extends TestCase
{
    protected $productMock;
    protected $productRepositoryMock;
    protected $productProcessor;

    protected function setUp(): void
    {
        $this->productMock = $this->createMock(Product::class);

        $this->productRepositoryMock = $this->getMockBuilder(ProductRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getById'])
            ->getMockForAbstractClass();
        $this->productRepositoryMock->expects($this->any())
            ->method('getById')
            ->with('5')
            ->willReturn($this->productMock);

        $this->productProcessor = (new ObjectManager($this))->getObject(ProductProcessor::class, [
            'productRepository' => $this->productRepositoryMock,
        ]);
    }

    public function testProcess(): void
    {
        $expectedUrl = 'http://test.com/product.html';
        $this->productMock->expects($this->once())
            ->method('getProductUrl')
            ->willReturn($expectedUrl);

        $this->assertEquals($expectedUrl, $this->productProcessor->process('5'));
    }

    public function testProcessThrowsException(): void
    {
        $this->productRepositoryMock->expects($this->any())
            ->method('getById')
            ->with('5')
            ->willThrowException(new Exception());

        $this->productMock->expects($this->never())
            ->method('getProductUrl');

        $this->expectException(Exception::class);
        $this->productProcessor->process('5');
    }

    /**
     * @param string $value
     * @param bool $expected
     * @return void
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate(string $value, bool $expected): void
    {
        $this->assertEquals($expected, $this->productProcessor->validate($value));
    }

    protected function validateDataProvider(): array
    {
        return [
            ['5', true],
            ['wrong', false],
            ['', false]
        ];
    }
}
