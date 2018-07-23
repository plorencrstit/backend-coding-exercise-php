<?php
namespace App\Command;

use App\Model\MenuItem;
use App\Model\Pointer;
use App\Model\Task;
use App\Model\Vendor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ExerciseCommand extends Command
{
    public $pointer;
    public $vendorIdPointer;
    private $validator;

    protected function configure()
    {
        $this
            ->setName('app:exercise')
            ->setDescription('Run CityPantry exercise')
            ->setHelp('This command runs CityPantry exercise')

            ->addArgument('filename', InputArgument::REQUIRED, 'Filename')
            ->addArgument('day', InputArgument::REQUIRED, 'Day')
            ->addArgument('time', InputArgument::REQUIRED, 'Time')
            ->addArgument('location', InputArgument::REQUIRED, 'Location')
            ->addArgument('covers', InputArgument::REQUIRED, 'Covers')
        ;

        $this->pointer = Pointer::VENDOR;
        $this->vendorIdPointer = null;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename');
        $day = $input->getArgument('day');
        $time = $input->getArgument('time');
        $location = $input->getArgument('location');
        $covers = $input->getArgument('covers');

//        $output->writeln('Filename: ' . $filename);
//        $output->writeln('Day: ' . $day);
//        $output->writeln('Time: ' . $time);
//        $output->writeln('Location: ' . $location);
//        $output->writeln('Covers: ' . $covers);

        $task = new Task($day, $time, $location, $covers);

        $errors = $this->validator->validate($task);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            $output->writeln($errorsString);
            return;
        }

        if(!$task->isOrderDateValid()){
            $output->writeln('Your order date is past!');
            return;
        }

        $vendors = [];
        $menuItems = [];

        $file = file(__DIR__ . '/' . $filename, FILE_IGNORE_NEW_LINES);

//        foreach ($file as $line_num => $line) {
//            $output->writeln('Line ' . $line_num . ': '. $line);
//        }

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

//        var_dump('Number of vendors BEFORE validation: ' . count($vendors));

        foreach($vendors as $key => $vendor) {
            $isValidated = $vendor->validate($location, $covers);
            if(!$isValidated) {
                unset($vendors[$key]);
            }
        }

//        var_dump('Number of vendors AFTER validation: ' . count($vendors));

        $vendorsId = $this->getIds($vendors);

//        var_dump($vendorsId);
//        var_dump($vendors);

//        var_dump('Number of items BEFORE validation: ' . count($menuItems));

        foreach($menuItems as $key => $menuItem) {
            $isValidated = $menuItem->validate($task->getPeriodInHours(), $vendorsId);
            if(!$isValidated) {
                unset($menuItems[$key]);
            }
        }

//        var_dump('Number of items AFTER validation: ' . count($menuItems));

        foreach($menuItems as $menuItem) {
            $output->writeln($menuItem->toString());
        }

    }

    public function getVendor($line): ?Vendor
    {
        $vendor = Vendor::createFromString($line);

        if($vendor){
            $this->pointer = Pointer::MENU_ITEM;
            $this->vendorIdPointer = $vendor->getId();
            return $vendor;
        }

        return null;
    }

    public function getMenuItem($line): ?MenuItem
    {
        $menuItem = MenuItem::createFromString($line, $this->vendorIdPointer);
        $this->pointer = ($menuItem) ? Pointer::MENU_ITEM : Pointer::NEW_LINE;

        return $menuItem;
    }

    /**
     * @param ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function getIds(array $data): array
    {
        $result = [];
        foreach($data as $object) {
            $result[] = $object->getId();
        }

        return $result;
    }

}