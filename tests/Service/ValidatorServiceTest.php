<?php
namespace App\Tests\Service;

use App\Model\MenuItem;
use App\Model\Vendor;
use App\Service\ValidatorService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validation;

class ValidatorServiceTest extends TestCase
{
    private $validator;
    private $validatorService;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->addYamlMapping(__DIR__ . '/../../config/validator/validation.yaml')->getValidator();

        $this->validatorService = new ValidatorService($this->validator);
    }

    public function testVendor(): void
    {
        $data = [
            'name'      => 'name-test',
            'postcode'  => 'NW12345',
            'maxCovers' => 5,
        ];

        $vendor = $this->validatorService->vendor($data);

        static::assertTrue($vendor instanceof Vendor);
    }

    public function testVendorWithErrors(): void
    {
        $data = [
            'name'      => 'name-test',
            'postcode'  => 111,
            'maxCovers' => 5,
        ];

        static::expectException(ValidatorException::class);

        $vendor = $this->validatorService->vendor($data);
    }

    public function testMenuItem(): void
    {
        $data = [
            'name'        => 'name-test',
            'allergies'   => 'NW12345',
            'advanceTime' => '5h',
        ];

        $vendorId = 1;

        $menuItem = $this->validatorService->menuItem($data, $vendorId);

        static::assertTrue($menuItem instanceof MenuItem);
    }
}
