<?php

use Illuminate\Support\Facades\Cache;
use Modules\Article\Entities\ArticleSetting;

/*
|--------------------------------------------------------------------------
| Register Namespaces And Routes
|--------------------------------------------------------------------------
|
| When a module starting, this file will executed automatically. This helps
| to register some namespaces like translator or view. Also this file
| will load the routes file for each module. You may also modify
| this file as you want.
|
*/

require __DIR__ . '/Routes/web.php';

function article_settings()
{
    if (!cache()->has('article_settings')) {
        $outreachHead = ArticleSetting::where('type', 'outreach_head')->first()->value ?? '';
        $outreachAssistants = ArticleSetting::where('type', 'outreach_assistants')->first()->value ?? '';
        $writerHead = ArticleSetting::where('type', 'writer_head')->first()->value ?? '';
        $writerHeadAssistant = ArticleSetting::where('type', 'writer_head_assistant')->first()->value ?? '';
        $publisher = ArticleSetting::where('type', 'publisher')->first()->value ?? '';
        $publishers = ArticleSetting::where('type', 'publishers')->first()->value ?? '';
        $writerRole = ArticleSetting::where('type', 'writer')->first()->value ?? '';
        $inhouseWriterRole = ArticleSetting::where('type', 'inhouse_writer')->first()->value ?? '';

        $data = (object) [
            'outreachHead' => $outreachHead,
            'outreachAssistants' => $outreachAssistants,
            'writerHead' => $writerHead,
            'writerHeadAssistant' => $writerHeadAssistant,
            'publisher' => $publisher,
            'publishers' => $publishers,
            'writerRole' => $writerRole,
            'inhouseWriterRole' => $inhouseWriterRole,
        ];

        cache()->put('article_settings', $data, 10080);
    } else {
        $data = cache()->get('article_settings');
    }

    return $data;
}
