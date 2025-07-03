<?php

namespace Ograre\Offers\Test\Unit\Model\OfferLink\Processor;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Ograre\Offers\Model\OfferLink\Processor\Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    protected $urlProcessor;

    protected function setUp(): void
    {
        $this->urlProcessor = (new ObjectManager($this))->getObject(Url::class);
    }

    public function testProcess(): void
    {
        $url = 'http://test.com';
        $this->assertEquals($url, $this->urlProcessor->process($url));
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
        $this->assertEquals($expected, $this->urlProcessor->validate($value));
    }

    protected function validateDataProvider(): array
    {
        return [
            ['http://test.com', true],
            ['https://test.com', true],
            ['test.com', false]
        ];
    }
}
