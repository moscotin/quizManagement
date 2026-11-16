<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_categories', function (Blueprint $table) {
            $table->string('period')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('quiz_categories', function (Blueprint $table) {
            $table->dropIndex(['period']);
            $table->dropColumn('period');
        });
    }
};
