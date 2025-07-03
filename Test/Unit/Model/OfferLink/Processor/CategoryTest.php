<?php

namespace Ograre\Offers\Test\Unit\Model\OfferLink\Processor;

use Exception;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category as CategoryModel;
use Ograre\Offers\Model\OfferLink\Processor\Category as CategoryProcessor;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    protected $categoryRepositoryMock;
    protected $categoryMock;
    protected $categoryProcessor;

    protected function setUp(): void
    {
        $this->categoryRepositoryMock = $this->getMockBuilder(CategoryRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMockForAbstractClass();

        $this->categoryMock = $this->getMockBuilder(CategoryModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUrl'])
            ->getMock();

        $this->categoryRepositoryMock->expects($this->any())
            ->method('get')
            ->with('5')
            ->willReturn($this->categoryMock);

        $this->categoryProcessor = (new ObjectManager($this))->getObject(CategoryProcessor::class, [
            'categoryRepository' => $this->categoryRepositoryMock
        ]);
    }

    public function testProcess(): void
    {
        $expectedUrl = 'http://test.com/category.html';
        $this->categoryMock->expects($this->once())
            ->method('getUrl')
            ->willReturn($expectedUrl);

        $this->assertEquals($expectedUrl, $this->categoryProcessor->process('5'));
    }

    public function testProcessThrowsException(): void
    {
        $this->categoryRepositoryMock->expects($this->once())
            ->method('get')
            ->with('5')
            ->willThrowException(new Exception());

        $this->categoryMock->expects($this->never())
            ->method('getUrl');

        $this->expectException(Exception::class);
        $this->categoryProcessor->process('5');
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
        $this->assertEquals($expected, $this->categoryProcessor->validate($value));
    }

    /**
     * @return array[]
     */
    protected function validateDataProvider(): array
    {
        return [
            ['5', true],
            ['test', false],
            ['', false]
        ];
    }
}
