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
        if (!Schema::hasTable('wh_recieve_sub'))
        {
            Schema::create('wh_recieve_sub', function (Blueprint $table) {
                $table->bigIncrements('wh_recieve_sub_id'); 
                $table->string('wh_recieve_id')->nullable(); //   
                $table->string('pro_id')->nullable();  //  
                $table->string('praman_chay')->nullable();  //   
                $table->string('wh_total')->nullable();  //  
                $table->string('praman_buy')->nullable();  //  
                $table->decimal('one_price',total: 12, places: 2)->nullable(); //   
                $table->decimal('total_price',total: 12, places: 2)->nullable(); //  
                
                $table->string('user_id')->nullable(); //                                 
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wh_recieve_sub');
    }
};
