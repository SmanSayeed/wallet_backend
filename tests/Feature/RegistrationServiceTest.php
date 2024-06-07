<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\RegistrationService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Mockery;

class RegistrationServiceTest extends TestCase
{
    protected $registrationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registrationService = new RegistrationService();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
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

        // Create a mock of the User model
        $userModelMock = Mockery::mock(User::class);
        // Expect the create method to be called once with the provided data
        $userModelMock->shouldReceive('create')->once()->with($userData)->andReturn($userData);

        // Replace the actual User model with the mock
        $this->app->instance(User::class, $userModelMock);

        // Call the register method of the RegistrationService
        $user = $this->registrationService->register($userData);

        // Assertions
        $this->assertEquals($userData['email'], $user['email']);
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

        $userModelMock = Mockery::mock(User::class);
        $userModelMock->shouldReceive('create')->once()->andReturn($userData);

        $user = $this->registrationService->register($userData);

        $this->assertTrue(Hash::check('secret123456', $user['password']));
    }
}
