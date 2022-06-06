<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterKumicdColumnInTDisplayTargetContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('T_DisplayTargetContent', function (Blueprint $table) {
            $table->increments('displayTargetContentId')->change();
            $table->string('kumicd', 8)->unsigned()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('T_DisplayTargetContent', function (Blueprint $table) {
            //
        });
    }
}
