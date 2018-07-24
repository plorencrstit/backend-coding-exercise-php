<?php
namespace App\Service;

class SearchService
{

    public function vendor(array $vendors, string $location, int $covers): array
    {
        foreach($vendors as $key => $vendor) {
            $isValidated = $vendor->search($location, $covers);
            if(!$isValidated) {
                unset($vendors[$key]);
            }
        }

        $vendorsId = $this->getIds($vendors);

        return $vendorsId;
    }

    public function menuItem(array $menuItems, int $period, array $vendorsId): array
    {
        foreach($menuItems as $key => $menuItem) {
            $isValidated = $menuItem->search($period, $vendorsId);
            if(!$isValidated) {
                unset($menuItems[$key]);
            }
        }

        return $menuItems;
    }

    private function getIds(array $data): array
    {
        $result = [];
        foreach($data as $object) {
            $result[] = $object->getId();
        }

        return $result;
    }

}