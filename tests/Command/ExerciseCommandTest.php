<?php
namespace App\Tests\Command;

use App\Command\ExerciseCommand;
use App\Service\ParserService;
use App\Service\SearchService;
use App\Service\ValidatorService;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Validator\Validation;

class ExerciseCommandTest extends KernelTestCase
{
    private $validatorService;
    private $searchService;
    private $parserService;
    private $fileDirectory;

    protected function setUp(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->addYamlMapping(__DIR__ . '/../../config/validator/validation.yaml')->getValidator();

        $this->validatorService = new ValidatorService($validator);
        $this->searchService = new SearchService();
        $this->parserService = new ParserService($this->validatorService);
        $this->fileDirectory = __DIR__ . '/../../resources/files';
    }

    public function testExecute()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new ExerciseCommand(
            $this->validatorService,
            $this->parserService,
            $this->searchService,
            $this->fileDirectory
        ));

        $command = $application->find('app:exercise');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'filename'  => 'my-example',
                'day'       => '30/07/18',
                'time'      => '11:00',
                'location'  => 'NW1234',
                'covers'    => 5,
            ]
        );

        $this->assertRegExp('/Pizza/', $commandTester->getDisplay());
    }
}
