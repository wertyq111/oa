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
        Schema::table('members', function (Blueprint $table) {
            $table->tinyInteger('dept_id')->index('idx_dept')->default(0)->comment("部门id");
            $table->tinyInteger('position_id')->nullable()->comment("岗位 id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('dept_id');
            $table->dropColumn('position_id');
        });
    }
};
