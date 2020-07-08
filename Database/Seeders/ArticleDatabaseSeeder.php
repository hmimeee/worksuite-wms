<?php

namespace Modules\Article\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ArticleDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

         \DB::table('article_settings')->insert([
                [
                    'name' => 'Freelance Writer Role', 'type' => 'writer', 'value' => 'Writer'
                ],
                [
                    'name' => 'Inhouse Writer Role', 'type' => 'inhouse_writer', 'value' => 'Inhouse_writer'
                ],
                [
                    'name' => 'Writer Head', 'type' => 'writer_head', 'value' => 'Writer Head'
                ],
                [
                    'name' => 'Publisher Head', 'type' => 'publisher', 'value' => 'Publisher'
                ],
                [
                    'name' => 'Outreach Head', 'type' => 'outreach_head', 'value' => 'Outreach Head'
                ],
                [
                    'name' => 'Outreach Article Category', 'type' => 'outreach_category', 'value' => 'Guest Post'
                ]
            ]);
         \DB::table('article_types')->insert([
                [
                    'name' => 'Review Article', 'description' => 'Review Article Description Here'
                ]
            ]);
         // \DB::table('articles')->insert([
         //          [
         //            'title' => 'Test Article Title', 'creator' => '1', 'creator' => '1', 'publishing' => 'true', 'wordcount' => '200', 'parent_task' => '1'
         //        ]
         //    ]);
    }
}
