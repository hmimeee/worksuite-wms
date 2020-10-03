<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {

            if (Schema::hasColumn('articles', 'invoice_id')) {
                Schema::table('articles', function (Blueprint $table) {
                    $table->dropColumn('invoice_id');
                });
            }

            $table->unsignedBigInteger('invoice_id')->nullable()->after('publishing_deadline');
            $table->foreign('invoice_id')->references('id')->on('article_invoices');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('invoice_id');
        });
    }
}
