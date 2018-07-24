<?php
namespace App\Tests\Service;

use App\Model\Vendor;
use App\Service\ValidatorService;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorServiceTest extends TestCase
{
    private $validator;
    private $console;
    private $validatorService;

    protected function setUp()
    {
        $this->validator = Mockery::mock(ValidatorInterface::class);
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

        $this->validator->shouldReceive('validate');
        $vendor = $this->validatorService->vendor($data);

        static::assertTrue($vendor instanceof Vendor);
    }
}