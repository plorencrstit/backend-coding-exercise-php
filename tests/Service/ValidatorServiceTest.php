<?php
namespace App\Tests\Service;

use App\Model\MenuItem;
use App\Model\Task;
use App\Model\Vendor;
use App\Service\ValidatorService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Validator\Validation;

class ValidatorServiceTest extends TestCase
{
    private $validator;
    private $console;
    private $validatorService;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->addYamlMapping(__DIR__ . '/../../config/validator/validation.yaml')->getValidator();
        $this->console = new ConsoleOutput();

        $this->validatorService = new ValidatorService($this->validator, $this->console);
    }

    public function testVendor(): void
    {
        $data = [
            'name' => 'name-test',
            'postcode' => 'NW12345',
            'maxCovers' => 5
        ];

        $vendor = $this->validatorService->vendor($data);

        static::assertTrue($vendor instanceof Vendor);
    }

    public function testVendorWithErrors(): void
    {
        $data = [
            'name' => 'name-test',
            'postcode' => 111,
            'maxCovers' => 5
        ];

        $vendor = $this->validatorService->vendor($data);

        static::assertNull($vendor);
    }

    public function testMenuItem(): void
    {
        $data = [
            'name' => 'name-test',
            'allergies' => 'NW12345',
            'advanceTime' => '5h'
        ];

        $vendorId = 1;

        $menuItem = $this->validatorService->menuItem($data, $vendorId);

        static::assertTrue($menuItem instanceof MenuItem);
    }

}