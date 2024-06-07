<?php

// database/migrations/create_wallets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletsTable extends Migration
{
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('currency_code')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallets');
    }
}
