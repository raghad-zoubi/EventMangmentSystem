<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->decimal('price');
            $table->text('size')->nullable();
            $table->text('color')->nullable();
            $table->string('capcity')->nullable();
            $table->Double('discount')->default(0);
            $table->date('expration_date')->default(Carbon::now());
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();
            $table->foreignId('subCategory_id')->constrained('sub_categories')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services');
    }
}
