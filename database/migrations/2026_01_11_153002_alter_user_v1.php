<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('mobile')->nullable()->after('password')->comment('User mobile number, nullable if not provided');
            $table->text('address')->nullable()->after('mobile')->comment('User address, can be null');
            $table->date('date_of_birth')->nullable()->after('address')->comment('User date of birth, nullable if not provided');
            $table->boolean('is_active')->default(true)->after('date_of_birth')->comment('Indicates if the user account is active');
            $table->timestamp('last_login_at')->nullable()->after('remember_token')->comment('Timestamp of the user\'s last login');

            // Adding an index to looking more faster
            $table->index('email');
            $table->index('mobile');
            $table->index('is_active');
            $table->index('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_name',
                'mobile',
                'address',
                'date_of_birth',
                'is_active',
                'last_login_at',
            ]);

            // Dropping the indexes
            $table->dropIndex(['email']);
            $table->dropIndex(['mobile']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['last_login_at']);
        });
    }
};
