<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_master_item', function (Blueprint $table) {
            // Foreign Key untuk Category
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            // Foreign Key untuk MasterItem (asumsi nama tabel Anda adalah 'master_items')
            $table->foreignId('master_item_id')->constrained('master_items')->onDelete('cascade');
            
            // Menjadikan kombinasi keduanya sebagai Primary Key
            $table->primary(['category_id', 'master_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_master_item');
    }
};
