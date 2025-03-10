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
        Schema::create('depts', function (Blueprint $table) {
            $table->increments('id')->comment("主键ID");
            $table->string('name', 50)->index('index_name')->comment("部门名称");
            $table->string('code', 150)->comment("部门编码");
            $table->string('fullname', 150)->comment("部门全称");
            $table->unsignedTinyInteger('type')->default(0)->comment("类型：1公司 2子公司 3部门 4小组");
            $table->unsignedInteger('pid')->default(0)->index('index_pid')->comment("上级ID");
            $table->unsignedSmallInteger('sort')->default(125)->comment("排序");
            $table->string('note')->nullable()->comment("备注");
            $table->integer('create_user')->default(0)->comment("添加人");
            $table->unsignedInteger('created_at')->default(0)->comment("创建时间");
            $table->integer('update_user')->default(0)->comment("更新人");
            $table->unsignedInteger('updated_at')->default(0)->comment("更新时间");
            $table->unsignedInteger('deleted_at')->default(0)->comment("删除时间");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depts');
    }
};
