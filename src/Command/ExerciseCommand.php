<?php
namespace App\Command;

use App\Service\ParserService;
use App\Service\ValidatorService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class ExerciseCommand extends Command
{
    /**
     * @var ValidatorService
     */
    private $validatorService;
    /**
     * @var ParserService
     */
    private $parserService;

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
    }

    public function __construct(ValidatorService $validatorService, ParserService $parserService)
    {
        parent::__construct();
        $this->validatorService = $validatorService;
        $this->parserService = $parserService;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $task = $this->validatorService->task($input);
            list($vendors, $menuItems) = $this->parserService->parse($input->getArgument('filename'));
        } catch (ValidatorException $exception) {
            $output->writeln($exception->getMessage());
            return;
        }

        foreach($vendors as $key => $vendor) {
            $isValidated = $vendor->validate($task->location, $task->covers);
            if(!$isValidated) {
                unset($vendors[$key]);
            }
        }

        $vendorsId = $this->getIds($vendors);

        foreach($menuItems as $key => $menuItem) {
            $isValidated = $menuItem->validate($task->getPeriodInHours(), $vendorsId);
            if(!$isValidated) {
                unset($menuItems[$key]);
            }
        }

        foreach($menuItems as $menuItem) {
            $output->writeln($menuItem->toString());
        }

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