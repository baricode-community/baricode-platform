<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User\User;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add the username column as nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 30)->nullable()->after('name');
        });

        // Generate usernames for existing users
        $this->generateUsernamesForExistingUsers();

        // Now make the username column unique and not nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 30)->unique()->nullable(false)->change();
        });
    }

    /**
     * Generate usernames for existing users based on their names
     */
    private function generateUsernamesForExistingUsers(): void
    {
        $users = User::whereNull('username')->get();
        
        foreach ($users as $user) {
            $baseUsername = $this->generateBaseUsername($user->name);
            $username = $this->ensureUniqueUsername($baseUsername);
            
            $user->update(['username' => $username]);
        }
    }

    /**
     * Generate base username from name
     */
    private function generateBaseUsername(string $name): string
    {
        // Remove special characters and convert to lowercase
        $username = Str::slug($name, '');
        
        // Ensure minimum length of 3 characters
        if (strlen($username) < 3) {
            $username = $username . 'usr';
        }
        
        // Ensure maximum length of 30 characters
        if (strlen($username) > 30) {
            $username = substr($username, 0, 30);
        }
        
        return $username;
    }

    /**
     * Ensure username is unique by adding numbers if necessary
     */
    private function ensureUniqueUsername(string $baseUsername): string
    {
        $username = $baseUsername;
        $counter = 1;
        
        while (User::where('username', $username)->exists()) {
            $suffix = (string) $counter;
            $maxBaseLength = 30 - strlen($suffix);
            
            if (strlen($baseUsername) > $maxBaseLength) {
                $username = substr($baseUsername, 0, $maxBaseLength) . $suffix;
            } else {
                $username = $baseUsername . $suffix;
            }
            
            $counter++;
        }
        
        return $username;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
