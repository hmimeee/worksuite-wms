<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Articles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description');
            $table->string('type');
            $table->unsignedInteger('project_id')->nullable();
            $table->unsignedInteger('parent_task')->nullable();
            $table->unsignedInteger('assignee');
            $table->unsignedInteger('creator');
            $table->boolean('publishing');
            $table->unsignedInteger('publisher')->nullable();
            $table->integer('word_count')->nullable();
            $table->integer('rate')->nullable();
            $table->string('priority')->nullable();
            $table->integer('working_status')->nullable();
            $table->integer('writing_status')->nullable();
            $table->integer('publishing_status')->nullable();
            $table->integer('article_status')->nullable();
            $table->integer('rating')->nullable();
            $table->string('publish_link')->nullable();
            $table->date('writing_deadline')->nullable();
            $table->date('publishing_deadline')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('parent_task')->references('id')->on('tasks');
            $table->foreign('assignee')->references('id')->on('users');
            $table->foreign('creator')->references('id')->on('users');
            $table->foreign('publisher')->references('id')->on('users');
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
        Schema::dropIfExists('articles');
    }
}
