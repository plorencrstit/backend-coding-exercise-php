<?php
namespace App\Model;

use DateTime;

class Task extends Model
{
    public $day;
    public $time;
    public $location;
    public $covers;
    public $orderedAt;

    public function __construct($day, $time, $location, $covers)
    {
        $this->day = $day;
        $this->time = $time;
        $this->location = $location;
        $this->covers = $covers;
        $this->orderedAt = new DateTime('now');
    }

    public function getSearchDateTime()
    {
        return DateTime::createFromFormat('d/m/y H:i', $this->day . ' ' . $this->time);
    }

    public function getPeriod()
    {
        return $this->getSearchDateTime()->getTimestamp() - $this->orderedAt->getTimestamp();
    }

    public function isOrderDateValid()
    {
        return $this->getSearchDateTime()->getTimestamp() > $this->orderedAt->getTimestamp();
    }

    public function getPeriodInHours()
    {
        return floor($this->getPeriod() / 3660);
    }

}