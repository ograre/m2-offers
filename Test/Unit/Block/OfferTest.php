<?php

namespace Ograre\Offers\Test\Unit\Block;

use DateTime;
use Magento\Catalog\Model\Category;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Ograre\Offers\Block\Offers;
use Ograre\Offers\Model\Offer;
use Ograre\Offers\Model\ResourceModel\Offer\Collection;
use Ograre\Offers\Model\ResourceModel\Offer\CollectionFactory;
use PHPUnit\Framework\TestCase;

class OfferTest extends TestCase
{
    protected $block;
    protected $collectionMock;
    protected $collectionFactoryMock;
    protected $currentCategoryMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->currentCategoryMock = $this->createMock(Category::class);
        $this->currentCategoryMock->method('getId')->willReturn(1);

        $this->collectionMock = $objectManager->getCollectionMock(Collection::class, []);

        $this->collectionFactoryMock = $this->createMock(CollectionFactory::class);
        $this->collectionFactoryMock->method('create')->willReturn($this->collectionMock);

        $timeZone = $this->createMock(TimezoneInterface::class);
        $timeZone->method('date')->willReturn(new DateTime());

        $this->block = $objectManager->getObject(Offers::class, [
            'collectionFactory' => $this->collectionFactoryMock,
            'timezone' => $timeZone,
        ]);
        $this->block->setCurrentCategory($this->currentCategoryMock);
    }

    public function tearDown(): void
    {
        $this->block = null;
    }

    public function testGetOffersWithEmptyCollection(): void
    {
        $this->collectionMock->expects($this->once())->method('getItems')->willReturn([]);
        $result = $this->block->getOffers();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetOffersWithFullCollection(): void
    {
        $this->collectionMock->expects($this->once())->method('getItems')->willReturn([ $this->createMock(Offer::class) ]);
        $result = $this->block->getOffers();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * @param int $size
     * @param bool $result
     * @return void
     *
     * @dataProvider collectionSizeProvider
     */
    public function testHasOffers(int $size, bool $result): void
    {
        $this->collectionMock->expects($this->once())->method('getSize')->willReturn($size);
        $this->assertEquals($this->block->hasOffers(), $result);
    }

    public function testGetIdentities()
    {
        $identities = ['offers_1'];
        $this->currentCategoryMock->expects($this->once())->method('getId')->willReturn(1);
        $this->assertEquals($identities, $this->block->getIdentities());
    }

    /**
     * @return array[]
     */
    public function collectionSizeProvider(): array
    {
        return [
            [0, false],
            [10, true]
        ];
    }
}
