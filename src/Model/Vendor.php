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

    public static function createFromString(string $line) : ?self
    {

        if(empty($line)){
            return null;
        }

        $data = explode(';', $line);
//        var_dump($data);

        if(count($data) != 3) {
            return null;
        }

        $result = ['name', 'postcode', 'maxCovers'];

        preg_match('/[A-Za-z ]*/', $data[0], $result['name']);
        preg_match('/[A-Za-z][A-Za-z0-9]*/', $data[1], $result['postcode']);
        preg_match('/\d*/', $data[2], $result['maxCovers']);

        $name = $result['name'][0];
        $postcode = $result['postcode'][0];
        $maxCovers = $result['maxCovers'][0];

//        var_dump('name: ' . $name);
//        var_dump('postcode: ' . $postcode);
//        var_dump('maxCovers: ' . $maxCovers);

        if($name && $postcode && $maxCovers) {
            return new Vendor($name, $postcode, $maxCovers);
        }

        return null;
    }

    public function validate($postcode, $covers): bool
    {

        $vendorShortPostcode = $this->getShortPostcode();
        $orderShortPostcode = $this->getShortPostcode($postcode);

//        var_dump($vendorShortPostcode);
//        var_dump($orderShortPostcode);

        if($vendorShortPostcode != $orderShortPostcode) {
            return false;
        }

        if($covers > $this->maxCovers) {
            return false;
        }

        return true;
    }

    private function getShortPostcode($postcode = null)
    {
        $postcode = $postcode ?? $this->postcode;

        return substr($postcode, 0, -4);
    }

}