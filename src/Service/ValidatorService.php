<?php
namespace App\Service;

use App\Model\MenuItem;
use App\Model\Task;
use App\Model\Vendor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorService {

    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var ConsoleOutputInterface
     */
    private $console;

    public function __construct(ValidatorInterface $validator, ConsoleOutputInterface $console) {

        $this->validator = $validator;
        $this->console = $console;
    }

    public function task(InputInterface $input): Task
    {
        $day = $input->getArgument('day');
        $time = $input->getArgument('time');
        $location = $input->getArgument('location');
        $covers = (int) $input->getArgument('covers');

        $task = new Task($day, $time, $location, $covers);

        $errors = $this->validator->validate($task);
        $is_valid = $this->checkErrors($errors);

        if(!$is_valid) {
            throw new ValidatorException($errors);
        }

        if(!$task->isOrderDateValid()){
            throw new ValidatorException('Your order date is past!');
        }

        return $task;
    }

    public function vendor(array $data): ?Vendor
    {
        $vendor = new Vendor($data['name'], $data['postcode'], $data['maxCovers']);

        $errors = $this->validator->validate($vendor);
        $is_valid = $this->checkErrors($errors);

        return ($is_valid) ? $vendor : null;
    }

    public function menuItem(array $data, $vendorId): ?MenuItem
    {
        $menuItem = new MenuItem($vendorId, $data['name'], $data['allergies'], $data['advanceTime']);

        $errors = $this->validator->validate($menuItem);
        $is_valid = $this->checkErrors($errors);

        return ($is_valid) ? $menuItem : null;
    }

    private function checkErrors($errors): bool
    {
        if (count($errors) > 0) {
            $errorsString = (string)$errors;
            $this->console->writeln($errorsString);
            return false;
        }

        return true;
    }
}