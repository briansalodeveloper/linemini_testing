<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('M_Message', function (Blueprint $table) {
            $table->bigIncrements('messageId');
            $table->tinyInteger('sendTargetFlg');
            $table->tinyInteger('sendFlg')->default(0)->comment('0=Not sent yet, 1=Already sent');
            $table->dateTime('sendDateTime');
            $table->string('messageName', 255)->charset('utf8mb4')->collation('utf8mb4_general_ci');
            $table->text('thumbnail')->charset('utf8mb4')->collation('utf8mb4_general_ci');
            $table->text('thumbnailPreview')->charset('utf8mb4')->collation('utf8mb4_general_ci')->default('');
            $table->text('contents')->charset('utf8mb4')->collation('utf8mb4_general_ci');
            $table->string('kumicd', 8)->default('');
            $table->tinyInteger('ubId')->default(0);
            $table->tinyInteger('aoId')->default(0);
            $table->tinyInteger('storeId')->default(0);
            $table->timeStamp('updateDate');
            $table->string('updateUser', 11)->charset('utf8mb4')->collation('utf8mb4_general_ci');
            $table->tinyInteger('delFlg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('M_Message');
    }
}
