<?php

namespace Ograre\Offers\Test\Unit\Model\Offer\Source;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Ograre\Offers\Helper\OfferLink as OfferLinkHelper;
use Ograre\Offers\Model\Offer\Source\LinkType;

class LinkTypeTest extends TestCase
{
    protected $offerLinkHelper;
    protected $source;

    protected function setUp(): void
    {
        $this->offerLinkHelper = $this->getMockBuilder(OfferLinkHelper::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAllLinks'])
            ->getMock();

        $this->source = (new ObjectManager($this))->getObject(LinkType::class, [
            'offerLinkHelper' => $this->offerLinkHelper
        ]);
    }

    public function testToOptionArray()
    {
        $expected = [
            ['label' => 'first', 'value' => 'first'],
            ['label' => 'second', 'value' => 'second']
        ];
        $this->offerLinkHelper->expects($this->once())
            ->method('getAllLinks')
            ->willReturn(['first', 'second']);

        $this->assertEquals($expected, $this->source->toOptionArray());
    }
}
