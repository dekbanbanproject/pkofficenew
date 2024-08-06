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
        if (!Schema::hasTable('fire_stock'))
        {
            Schema::create('fire_stock', function (Blueprint $table) {
                $table->bigIncrements('fire_stock_id'); 
                $table->string('fire_id')->nullable();  //  
                $table->string('fire_qty')->nullable(); //
                $table->string('fire_unit')->nullable(); // 
                $table->string('fire_month')->nullable();  //  
                $table->string('fire_year')->nullable();  //  
                $table->decimal('fire_price',total: 12, places: 2)->nullable(); //  
                $table->enum('active', ['N','R','Y','D'])->default('Y'); 
                $table->date('fire_date_pdd')->nullable();  // วันที่ผลิต
                $table->date('fire_date_exp')->nullable();  // วันหมดอายุ
               
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fire_stock');
    }
};
