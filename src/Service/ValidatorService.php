<?php
namespace App\Service;

use App\Model\MenuItem;
use App\Model\Task;
use App\Model\Vendor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorService
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function task(InputInterface $input): Task
    {
        $day = $input->getArgument('day');
        $time = $input->getArgument('time');
        $location = $input->getArgument('location');
        $covers = (int) $input->getArgument('covers');

        $task = new Task($day, $time, $location, $covers);

        $errors = $this->validator->validate($task);
        $this->checkErrors($errors);

        if (!$task->isOrderDateValid()) {
            throw new ValidatorException('Your order date is past!');
        }

        return $task;
    }

    public function vendor(array $data): Vendor
    {
        $vendor = new Vendor($data['name'], $data['postcode'], $data['maxCovers']);

        $errors = $this->validator->validate($vendor);
        $this->checkErrors($errors);

        return $vendor;
    }

    public function menuItem(array $data, $vendorId): MenuItem
    {
        $menuItem = new MenuItem($vendorId, $data['name'], $data['allergies'], $data['advanceTime']);

        $errors = $this->validator->validate($menuItem);
        $this->checkErrors($errors);

        return $menuItem;
    }

    private function checkErrors($errors): void
    {
        if ($errors && count($errors) > 0) {
            throw new ValidatorException((string) $errors);
        }
    }
}
