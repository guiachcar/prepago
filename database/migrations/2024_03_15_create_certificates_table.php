<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('document');
            $table->string('service');
            $table->json('params')->nullable();
            $table->string('status');
            $table->text('message')->nullable();
            $table->json('result')->nullable();
            $table->string('region')->nullable();
            $table->text('url_certificate')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
