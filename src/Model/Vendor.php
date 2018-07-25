<?php
namespace App\Model;

class Vendor extends Model
{
    public static $counter = 1;

    public $name;
    public $postcode;
    public $maxCovers;

    public function __construct($name, $postcode, $maxCovers)
    {
        $this->id = self::$counter;
        $this->name = $name;
        $this->postcode = $postcode;
        $this->maxCovers = $maxCovers;
        self::$counter++;
    }

    public function search($postcode, $covers): bool
    {
        $vendorShortPostcode = $this->getShortPostcode();
        $orderShortPostcode = $this->getShortPostcode($postcode);

        if ($vendorShortPostcode != $orderShortPostcode) {
            return false;
        }

        if ($covers > $this->maxCovers) {
            return false;
        }

        return true;
    }

    private function getShortPostcode(string $postcode = null): string
    {
        $postcode = $postcode ?? $this->postcode;

        return mb_substr($postcode, 0, -4);
    }
}
