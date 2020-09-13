<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * Column	Type	Null	Default
id	bigint(20)	No	
name	varchar(255)	Yes	NULL
account_code	varchar(256)	Yes	NULL
remarks	varchar(255)	Yes	NULL
user_id	bigint(20)	No	
created_at	timestamp	Yes	NULL
updated_at	timestamp	Yes	NULL
deleted_by	int(11)	No	0

     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('account_code')->nullable();
            $table->string('remarks')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('deleted_by')->default(0);
            
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
}
