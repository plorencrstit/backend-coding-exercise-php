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
        $this->pointer = Pointer::VENDOR;
        $this->vendorIdPointer = null;
        $this->validatorService = $validatorService;
    }

    public function parse(string $filename): array
    {
        $vendors = [];
        $menuItems = [];

        $file = file(__DIR__ . '/' . $filename, FILE_IGNORE_NEW_LINES);

        foreach ($file as $line) {

            switch($this->pointer){
                case Pointer::VENDOR:
                    $vendor = $this->getVendor($line);
                    if($vendor) {
                        $vendors[] = $vendor;
                        break;
                    }
                case Pointer::MENU_ITEM:
                    $menuItems[] = $this->getMenuItem($line);
                case Pointer::NEW_LINE:
                    $this->pointer = (empty($line)) ? Pointer::VENDOR : Pointer::MENU_ITEM;
                    break;
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

//        $vendor = Vendor::createFromString($line);

        if($vendor){
            $this->pointer = Pointer::MENU_ITEM;
            $this->vendorIdPointer = $vendor->getId();
            return $vendor;
        }

        return null;
    }

    private function getMenuItem(string $line): ?MenuItem
    {
        $menuItem = MenuItem::createFromString($line, $this->vendorIdPointer);
        $this->pointer = ($menuItem) ? Pointer::MENU_ITEM : Pointer::NEW_LINE;

        return $menuItem;
    }

    private function parseLineOfVendor(string $line): ?array
    {
        if(empty($line)){
            return null;
        }

        $data = explode(';', $line);

        if(count($data) != 3) {
            return null;
        }

        return [
            'name' => $data[0],
            'postcode' => $data[1],
            'maxCovers' => $data[2]
        ];
    }
}