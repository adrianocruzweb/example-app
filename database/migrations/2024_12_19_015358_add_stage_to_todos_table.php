<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStageToTodosTable extends Migration
{
    public function up()
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->enum('stage', ['to-do', 'doing', 'test', 'done'])->default('to-do')->after('responsible'); // Adiciona o campo stage
        });
    }

    public function down()
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropColumn('stage'); // Remove o campo stage se necessário
        });
    }
}
