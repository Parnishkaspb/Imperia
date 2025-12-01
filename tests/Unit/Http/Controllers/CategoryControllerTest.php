<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Mockery;

class CategoryControllerTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Мокаем Auth
        $user = User::factory()->make(['id' => 1, 'name' => 'Test User']);
        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('id')->andReturn(1);
        Auth::shouldReceive('user->name')->andReturn('Test User');
    }

    /** @test */
    public function it_creates_category_successfully()
    {
        // Arrange
        $requestData = [
            'name' => 'Test Category',
        ];

        // Mock Category model
        $categoryMock = Mockery::mock('overload:' . Category::class);
        $categoryMock->shouldReceive('create')
            ->once()
            ->with([
                'name' => 'Test Category',
                'namewithout' => 'TestCategory', // результат cleanSearchString
                'parent_id' => 1,
            ])
            ->andReturn((object)['id' => 1]);

        // Mock helper
        $helperMock = Mockery::mock('alias:App\Helpers\Helper');
        $helperMock->shouldReceive('cleanSearchString')
            ->with('Test Category')
            ->andReturn('TestCategory');

        // Mock CategoryRequest
        $requestMock = Mockery::mock(CategoryRequest::class);
        $requestMock->shouldReceive('validated')->andReturn($requestData);

        // Act
        $controller = new \App\Http\Controllers\Edit\CategoryController();
        $response = $controller->store($requestMock);

        // Assert
        $this->assertEquals(302, $response->getStatusCode()); // redirect
        $this->assertTrue($response->isRedirect());
    }

    /** @test */
    public function it_handles_creation_error()
    {
        // Arrange
        $requestData = ['name' => 'Test Category'];

        // Mock Category чтобы бросить исключение
        $categoryMock = Mockery::mock('overload:' . Category::class);
        $categoryMock->shouldReceive('create')
            ->once()
            ->andThrow(new \Exception('Database error'));

        // Mock helper
        $helperMock = Mockery::mock('alias:App\Helpers\Helper');
        $helperMock->shouldReceive('cleanSearchString')
            ->andReturn('TestCategory');

        // Mock CategoryRequest
        $requestMock = Mockery::mock(CategoryRequest::class);
        $requestMock->shouldReceive('validated')->andReturn($requestData);

        // Act
        $controller = new \App\Http\Controllers\Edit\CategoryController();
        $response = $controller->store($requestMock);

        // Assert
        $this->assertEquals(302, $response->getStatusCode()); // redirect back
        $this->assertTrue($response->isRedirect());
    }

    /** @test */
    public function it_cleans_category_name_correctly()
    {
        // Arrange
        $requestData = ['name' => 'Test Category 123!@#'];

        // Mock Category
        $categoryMock = Mockery::mock('overload:' . Category::class);
        $categoryMock->shouldReceive('create')
            ->once()
            ->with([
                'name' => 'Test Category 123!@#',
                'namewithout' => 'TestCategory123', // очищенная строка
                'parent_id' => 1,
            ])
            ->andReturn((object)['id' => 1]);

        // Mock helper с конкретным вызовом
        $helperMock = Mockery::mock('alias:App\Helpers\Helper');
        $helperMock->shouldReceive('cleanSearchString')
            ->with('Test Category 123!@#')
            ->andReturn('TestCategory123');

        // Mock CategoryRequest
        $requestMock = Mockery::mock(CategoryRequest::class);
        $requestMock->shouldReceive('validated')->andReturn($requestData);

        // Act
        $controller = new \App\Http\Controllers\Edit\CategoryController();
        $response = $controller->store($requestMock);

        // Assert
        $this->assertEquals(302, $response->getStatusCode());
    }

    /** @test */
    public function it_sets_parent_id_to_one()
    {
        // Arrange
        $requestData = ['name' => 'Test Category'];

        // Mock Category - проверяем что parent_id = 1
        $categoryMock = Mockery::mock('overload:' . Category::class);
        $categoryMock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['parent_id'] === 1;
            }))
            ->andReturn((object)['id' => 1]);

        // Mock helper
        $helperMock = Mockery::mock('alias:App\Helpers\Helper');
        $helperMock->shouldReceive('cleanSearchString')
            ->andReturn('TestCategory');

        // Mock CategoryRequest
        $requestMock = Mockery::mock(CategoryRequest::class);
        $requestMock->shouldReceive('validated')->andReturn($requestData);

        // Act
        $controller = new \App\Http\Controllers\Edit\CategoryController();
        $response = $controller->store($requestMock);

        // Assert
        $this->assertEquals(302, $response->getStatusCode());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
