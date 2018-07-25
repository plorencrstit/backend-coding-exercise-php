<?php
namespace App\Model;

class MenuItem extends Model
{
    public static $counter = 1;

    private $vendorId;
    private $name;
    private $allergies;
    private $advanceTime;

    public function __construct($vendorId, $name, $allergies, $advanceTime)
    {
        $this->id = self::$counter;
        $this->vendorId = $vendorId;
        $this->name = $name;
        $this->allergies = $allergies;
        $this->advanceTime = $advanceTime;
        self::$counter++;
    }

    public function search($period, array $vendorsId): bool
    {
        if ($this->advanceTime > $period) {
            return false;
        }

        if (!in_array($this->vendorId, $vendorsId)) {
            return false;
        }

        return true;
    }

    public function toString(): string
    {
        return $this->name . ';' . $this->allergies . ';' . $this->advanceTime;
    }
}
