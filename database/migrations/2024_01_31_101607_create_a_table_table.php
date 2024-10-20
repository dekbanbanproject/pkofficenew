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
        if (!Schema::hasTable('a_table'))
        {
            Schema::connection('mysql')->create('a_table', function (Blueprint $table) { 
                $table->bigIncrements('a_table_id');//   
                $table->string('vn')->nullable();//      
                $table->string('an')->nullable();//  
                $table->string('cid')->nullable();//  
                $table->date('vstdate')->nullable();//  
                $table->date('dchdate')->nullable();// 
                $table->timestamps();
            });    
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a_table');
    }
};
