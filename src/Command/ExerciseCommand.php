<?php
namespace App\Command;

use App\Service\ParserService;
use App\Service\SearchService;
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
    /**
     * @var SearchService
     */
    private $searchService;

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
            ->addArgument('covers', InputArgument::REQUIRED, 'Covers');
    }

    public function __construct(ValidatorService $validatorService, ParserService $parserService, SearchService $searchService)
    {
        parent::__construct();
        $this->validatorService = $validatorService;
        $this->parserService = $parserService;
        $this->searchService = $searchService;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $task = $this->validatorService->task($input);
            [$vendors, $menuItems] = $this->parserService->parse($input->getArgument('filename'));
            $vendorsId = $this->searchService->vendor($vendors, $task->getLocation(), $task->getCovers());
            $menuItems = $this->searchService->menuItem($menuItems, $task->getPeriodInHours(), $vendorsId);
        } catch (ValidatorException $exception) {
            $output->writeln($exception->getMessage());

            return;
        }

        foreach ($menuItems as $menuItem) {
            $output->writeln($menuItem->toString());
        }
    }
}
