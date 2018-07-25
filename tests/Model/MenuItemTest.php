<?php
namespace App\Tests\Service;

use App\Model\MenuItem;
use PHPUnit\Framework\TestCase;

class MenuItemTest extends TestCase
{
    protected function setUp(): void
    {
    }

    public function testSearchIfFalseWhenAdvanceTimeBiggerThanPeriod(): void
    {
        $period = 4;
        $menuItem = new MenuItem(1, 'item1', null, 5);
        $vendorsId = [1, 2, 3];

        $result = $menuItem->search($period, $vendorsId);

        static::assertFalse($result);
    }

}
