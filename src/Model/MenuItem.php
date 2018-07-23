<?php
namespace App\Model;

class MenuItem extends Model
{
    public static $counter = 1;

    public $vendorId;
    public $name;
    public $allergies;
    public $advanceTime;

    public function __construct($vendorId, $name, $allergies, $advanceTime)
    {
        $this->id = self::$counter;
        $this->vendorId = $vendorId;
        $this->name = $name;
        $this->allergies = $allergies;
        $this->advanceTime = $advanceTime;
        self::$counter++;
    }

    public static function createFromString(string $line, $vendorId) : ?self
    {

        if(empty($line)){
            return null;
        }

        $data = explode(';', $line);

        if(count($data) != 3) {
            return null;
        }

        $result = ['name', 'allergies', 'advanceTime'];

        preg_match('/[A-Za-z ]*/', $data[0], $result['name']);
        preg_match('/[A-Za-z]*/', $data[1], $result['allergies']);
        preg_match('/\d*/', $data[2], $result['advanceTime']);

        $name = $result['name'][0];
        $allergies = $result['allergies'][0];
        $advanceTime = $result['advanceTime'][0];

        if($name && $advanceTime) {
            return new MenuItem($vendorId, $name, $allergies, $advanceTime);
        }

        return null;
    }

    public function validate($period, array $vendorsId): bool
    {
        if($this->advanceTime > $period) {
            return false;
        }

        if(!in_array($this->vendorId, $vendorsId)) {
            return false;
        }

        return true;
    }

    public function toString() {
        return $this->name . ';' . $this->allergies . ';' . $this->advanceTime . 'h';
    }
}