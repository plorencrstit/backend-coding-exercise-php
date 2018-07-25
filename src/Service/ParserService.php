<?php
namespace App\Service;

use App\Model\MenuItem;
use App\Model\Pointer;
use App\Model\Vendor;

class ParserService
{
    private $pointer;
    private $vendorIdPointer;
    /**
     * @var ValidatorService
     */
    private $validatorService;

    public function __construct(ValidatorService $validatorService)
    {
        $this->pointer = Pointer::NEW_LINE;
        $this->vendorIdPointer = null;
        $this->validatorService = $validatorService;
    }

    public function parse(string $filename, string $directory): array
    {
        $vendors = [];
        $menuItems = [];

        $file = file($directory . '/' . $filename, FILE_IGNORE_NEW_LINES);

        foreach ($file as $line) {
            switch ($this->pointer) {
                case Pointer::NEW_LINE:
                    if (empty($line)) {
                        break;
                    }
                    // no break
                case Pointer::VENDOR:
                    $vendor = $this->getVendor($line);
                    if ($vendor) {
                        $vendors[] = $vendor;
                        break;
                    }
                    // no break
                case Pointer::MENU_ITEM:
                    $menuItems[] = $this->getMenuItem($line);
                    // no break
                default:
                    break;
            }
        }

        $vendors = array_filter($vendors);
        $menuItems = array_filter($menuItems);

        return [$vendors, $menuItems];
    }

    private function getVendor(string $line): ?Vendor
    {
        $data = $this->parseLineOfVendor($line);
        $vendor = $this->validatorService->vendor($data);

        if ($vendor) {
            $this->pointer = Pointer::MENU_ITEM;
            $this->vendorIdPointer = $vendor->getId();

            return $vendor;
        }

        return null;
    }

    private function getMenuItem(string $line): ?MenuItem
    {
        $this->pointer = Pointer::NEW_LINE;

        if (!$this->vendorIdPointer) {
            return null;
        }

        $data = $this->parseLineOfMenuItem($line);

        if (!$data) {
            return null;
        }

        $menuItem = $this->validatorService->menuItem($data, $this->vendorIdPointer);
        $this->pointer = ($menuItem) ? Pointer::MENU_ITEM : Pointer::NEW_LINE;

        return $menuItem;
    }

    private function parseLineOfVendor(string $line): ?array
    {
        if (empty($line)) {
            return null;
        }

        $data = explode(';', $line);

        if (count($data) != 3) {
            return null;
        }

        return [
            'name'      => $data[0],
            'postcode'  => $data[1],
            'maxCovers' => (int) $data[2],
        ];
    }

    private function parseLineOfMenuItem(string $line): ?array
    {
        if (empty($line)) {
            return null;
        }

        $data = explode(';', $line);

        if (count($data) != 3) {
            return null;
        }

        return [
            'name'        => $data[0],
            'allergies'   => $data[1],
            'advanceTime' => $data[2],
        ];
    }
}
