<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToDisplayContentTargetUbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('T_DisplayTargetContentUB', function (Blueprint $table) {
            $table->integer('displayTargetContentUBId', 11)->comment('ID')->change();
            $table->integer('contentDraftId')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('T_DisplayTargetContentUB', function (Blueprint $table) {
            $table->integer('displayTargetContentUBId')->change();
            $table->dropColumn('contentDraftId');
        });
    }
}
