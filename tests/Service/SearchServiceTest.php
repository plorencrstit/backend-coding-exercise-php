<?php
namespace App\Tests\Service;

use App\Model\MenuItem;
use App\Model\Vendor;
use App\Service\SearchService;
use PHPUnit\Framework\TestCase;

class SearchServiceTest extends TestCase
{
    private $searchService;

    protected function setUp(): void
    {
        $this->searchService = new SearchService();
    }

    public function testVendor(): void
    {
        $vendor1 = new Vendor('name', 'NW1234', 5);
        $vendor2 = new Vendor('name2', 'NW4321', 20);
        $vendors = [$vendor1, $vendor2];

        $location = 'NW9876';
        $covers = 10;

        $vendorsId = $this->searchService->vendor($vendors, $location, $covers);

        static::assertInternalType('array', $vendorsId);
    }

    public function testMenuItem(): void
    {
        $menuItem1 = new MenuItem(1, 'item1', null, 5);
        $menuItem2 = new MenuItem(2, 'item2', 'gluten', 5);
        $menuItems = [$menuItem1, $menuItem2];

        $period = 5;
        $vendorsId = [1, 2, 3];

        $menuItems = $this->searchService->menuItem($menuItems, $period, $vendorsId);

        static::assertInternalType('array', $menuItems);
    }
}
