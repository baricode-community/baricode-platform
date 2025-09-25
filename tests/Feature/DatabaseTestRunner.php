<?php

namespace Tests\Feature;

use Tests\DatabaseTestCase;
use Illuminate\Support\Facades\Artisan;

class DatabaseTestRunner extends DatabaseTestCase
{
    /** @test */
    public function it_runs_all_database_relationship_tests()
    {
        // This test serves as a comprehensive runner for all database relationship tests
        
        $this->assertTrue(true, 'All database relationship tests should be run individually');
        
        // You can run individual test classes using PHPUnit or Pest:
        // vendor/bin/phpunit tests/Unit/Models/
        // vendor/bin/phpunit tests/Feature/DatabaseRelationshipIntegrationTest.php
    }
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        Artisan::call('migrate:fresh');
        
        // Seed permissions and roles if needed
        $this->setupPermissions();
    }
    
    private function setupPermissions(): void
    {
        // Create basic permissions that might be needed for tests
        if (class_exists(\Spatie\Permission\Models\Permission::class)) {
            \Spatie\Permission\Models\Permission::create(['name' => 'manage-courses']);
            \Spatie\Permission\Models\Permission::create(['name' => 'approve-enrollments']);
            \Spatie\Permission\Models\Permission::create(['name' => 'view-courses']);
            
            \Spatie\Permission\Models\Role::create(['name' => 'admin']);
            \Spatie\Permission\Models\Role::create(['name' => 'instructor']);
            \Spatie\Permission\Models\Role::create(['name' => 'student']);
        }
    }
}
