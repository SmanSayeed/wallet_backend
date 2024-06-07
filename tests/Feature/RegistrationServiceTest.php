<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Services\RegistrationService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegistrationServiceTest extends TestCase
{
    protected $registrationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registrationService = new RegistrationService();
    }

    public function testUserCanRegister()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'secret123456',
            'phone' => '000-123-4567',
            'address' => '123 Main St',
            'country' => 'ExampleLand',
            'post_code' => 'EL123',
            'nid' => '1234567890',
        ];

        $user = $this->registrationService->register($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', ['email' => 'john.doe@example.com']);
    }

    public function testRegisterValidatesData()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'secret',
            'phone' => '000-123-4567',
            'address' => '123 Main St',
            'country' => 'ExampleLand',
            'post_code' => 'EL123',
            'nid' => '1234567890',
        ];

        $validator = Validator::make($userData, $this->registrationService->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('email'));
        $this->assertTrue($validator->errors()->has('password'));
    }

    public function testUserCannotRegisterWithDuplicateEmail()
    {
        User::factory()->create(['email' => 'john.doe@example.com']);

        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'secret123456',
            'phone' => '000-123-4567',
            'address' => '123 Main St',
            'country' => 'ExampleLand',
            'post_code' => 'EL123',
            'nid' => '1234567890',
        ];

        $validator = Validator::make($userData, $this->registrationService->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('email'));
    }

    public function testUserPasswordIsHashed()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'secret123456',
            'phone' => '000-123-4567',
            'address' => '123 Main St',
            'country' => 'ExampleLand',
            'post_code' => 'EL123',
            'nid' => '1234567890',
        ];

        $user = $this->registrationService->register($userData);

        $this->assertTrue(Hash::check('secret123456', $user->password));
    }
}
