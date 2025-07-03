<?php

namespace Ograre\Offers\Test\Unit\Helper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Ograre\Offers\Helper\OfferLink;
use Ograre\Offers\Api\OfferLinkProcessorInterface;
use Magento\Framework\App\Helper\Context;

class OfferLinkTest extends TestCase
{
    protected $firstProcessor;
    protected $secondProcessor;
    protected $contextMock;
    protected $helper;

    protected function setUp(): void
    {
        $this->firstProcessor = $this->getMockForAbstractClass(OfferLinkProcessorInterface::class);

        $this->secondProcessor = $this->getMockForAbstractClass(OfferLinkProcessorInterface::class);

        $processorsArgument = [
            'first' => $this->firstProcessor,
            'second' => $this->secondProcessor
        ];

        $this->contextMock = $this->createMock(Context::class);

        $this->helper = (new ObjectManager($this))->getObject(OfferLink::class, [
            'context' => $this->contextMock,
            'processors' => $processorsArgument
        ]);
    }

    public function testProcessLink()
    {
        $expected = 'http://test.com/5';
        $this->firstProcessor->expects($this->any())
            ->method('process')
            ->with(5)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->helper->processLink('first/5'));
    }

    public function testWrongLinkTypeThrowsException()
    {
        $this->expectException(LocalizedException::class);
        $this->helper->processLink('wrong/5');
    }

    public function testNoLinkTypeThrowsException()
    {
        $this->expectException(LocalizedException::class);
        $this->helper->processLink('/5');
    }

    public function testNoLinkValueThrowsException()
    {
        $this->expectException(LocalizedException::class);
        $this->helper->processLink('first/');
    }

    /**
     * @param string $link
     * @param int $value
     * @param bool $expected
     * @return void
     *
     * @dataProvider validLinkDataProvider
     */
    public function testIsValidLink(string $link, int $value, bool $expected)
    {
        $this->firstProcessor->expects($this->once())
            ->method('validate')
            ->with($value)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->helper->isValidLink($link));
    }

    /**
     * @return array[]
     */
    protected function validLinkDataProvider()
    {
        return [
            ['first/5', 5, true],
            ['first/5', 5, false]
        ];
    }

    public function testGetAllLinks()
    {
        $expected = ['first','second'];
        $this->assertEquals($expected, $this->helper->getAllLinks());
    }

    public function testGetLinkType()
    {
        $expected = 'first';
        $this->assertEquals($expected, $this->helper->getLinkType('first/5'));
    }

    public function testGetLinkValue()
    {
        $expected = '5';
        $this->assertEquals($expected, $this->helper->getLinkValue('first/5'));
    }
}
