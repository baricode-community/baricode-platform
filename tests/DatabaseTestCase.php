<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

abstract class DatabaseTestCase extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable foreign key constraints for SQLite during testing
        if (config('database.default') === 'sqlite') {
            Schema::disableForeignKeyConstraints();
        }
    }

    protected function tearDown(): void
    {
        // Re-enable foreign key constraints
        if (config('database.default') === 'sqlite') {
            Schema::enableForeignKeyConstraints();
        }
        
        parent::tearDown();
    }

    /**
     * Create a model instance for testing
     */
    protected function createModel(string $modelClass, array $attributes = []): Model
    {
        return $modelClass::factory()->create($attributes);
    }

    /**
     * Create multiple model instances for testing
     */
    protected function createModels(string $modelClass, int $count, array $attributes = []): \Illuminate\Database\Eloquent\Collection
    {
        return $modelClass::factory()->count($count)->create($attributes);
    }

    /**
     * Assert that a relationship exists and returns the expected type
     */
    protected function assertRelationshipExists(Model $model, string $relationshipName, string $expectedType): void
    {
        $this->assertTrue(
            method_exists($model, $relationshipName),
            "Method {$relationshipName} does not exist on " . get_class($model)
        );

        $relationship = $model->$relationshipName();
        $this->assertInstanceOf(
            $expectedType,
            $relationship,
            "Relationship {$relationshipName} is not of type {$expectedType}"
        );
    }

    /**
     * Assert that a relationship returns expected results
     */
    protected function assertRelationshipWorks(Model $model, string $relationshipName, $expectedData = null): void
    {
        $result = $model->$relationshipName;
        
        if ($expectedData !== null) {
            if (is_array($expectedData) || $expectedData instanceof \Illuminate\Database\Eloquent\Collection) {
                $this->assertNotEmpty($result, "Relationship {$relationshipName} returned empty results");
                $this->assertCount(count($expectedData), $result);
            } else {
                $this->assertNotNull($result, "Relationship {$relationshipName} returned null");
            }
        }
    }
}
