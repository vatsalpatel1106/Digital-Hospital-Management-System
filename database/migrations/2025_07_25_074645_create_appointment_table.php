<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('appointment', function (Blueprint $table) {
        $table->id('apid'); // Appointment ID
        $table->unsignedBigInteger('pid'); // Foreign key to patient
        $table->unsignedBigInteger('did'); // Foreign key to doctor
        $table->date('appointment_date');
        $table->time('appointment_time');
        $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
        $table->string('medicine_file')->nullable(); // optional medicine file
        $table->timestamps();

        // You can add foreign keys if needed:
         $table->foreign('pid')->references('pid')->on('patient')->onDelete('cascade');
         $table->foreign('did')->references('did')->on('doctor')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment');
    }
};