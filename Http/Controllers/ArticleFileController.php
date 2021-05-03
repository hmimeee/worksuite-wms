<?php

namespace Modules\Article\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Helper\Reply;
use Modules\Article\Entities\ArticleFile;
use Modules\Article\Entities\ArticleActivityLog;
use Illuminate\Http\File;

class ArticleFileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('article::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('article::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'files.*' => 'max:5120'
        ]);

        $articles = explode(',',$request->articles);
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
            $fileData = $file;
            $filename = $fileData->getClientOriginalName();
            $ext = strtolower(\File::extension($filename));
            $hashname = md5(microtime());
            $rename = $this->slugify($hashname) . '.' . $ext;

            for ($i = 0; $i < count($articles) ; $i++) {
                $artfile = ArticleFile::create([
                    'user_id' => auth()->id(),
                    'article_id' => $articles[$i],
                    'filename' => $filename,
                    'hashname' => $rename,
                    'size' => $fileData->getSize()
                ]);
                $artfile->save();
            }

            $fileData->move(public_path('user-uploads/article-files'), $rename);
            $getFile[] = $filename;
            $getFileId[] = $artfile->id;
        }

            return Reply::successWithData('Files uploded!', ['file' => $getFile, 'fileId' => $getFileId]);
        }

        return Reply::success('No files to upload!');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('article::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('article::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

        /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
        public function download($id)
        {
            $file = ArticleFile::findOrFail($id);
            $filePath = 'user-uploads/article-files/'.$file->hashname;
            return response()->download($filePath, $file->filename);
        }

        /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function removeArticle($id)
    {
        $file = ArticleFile::findOrFail($id);

        //Store in log
        ArticleActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Article',
            'article_id' => $file->article_id,
            'label' => 'article_file',
            'details' => 'deleted a file of the article.'
        ]);

        $file->article_id = 0;
        $file->save();

        return Reply::success('File deleted!');
            
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $file = ArticleFile::findOrFail($id);
        unlink(public_path('/user-uploads/article-files/'.$file->hashname));
        $file->delete();
        return Reply::success('File deleted!');
            
    }

    /**
     * Make slug from string
     * 
     * @param string $text
     * @return string
     */
    public static function slugify(string $text)
    {
        $text = preg_replace("/[~`{}.'\"\!\@\#\$\%\^\&\*\(\)\=\+\/\?\>\<\,\[\]\:\;\|\\\]/", "", $text);
        $text = preg_replace('/\s+/u', '-', trim($text));

        return $text;
    }
}
