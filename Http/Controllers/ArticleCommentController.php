<?php

namespace Modules\Article\Http\Controllers;

use App\User;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Article\Entities\Article;
use Illuminate\Support\Facades\Notification;
use Modules\Article\Entities\ArticleComment;
use Modules\Article\Entities\ArticleSetting;
use Modules\Article\Entities\ArticleActivityLog;
use Modules\Article\Notifications\ArticleComment as ArticleCommentNotification;



class ArticleCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->writerHead = ArticleSetting::where('type', 'writer_head')->first()->value ?? '';
        $this->writerHeadAssistant = ArticleSetting::where('type', 'writer_head_assistant')->first()->value ?? '';
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
        if ($request->comment == null || $request->comment == '') {
            return Reply::error('Please write your comment!');
        }

        $files = explode(',', $request->uploadedFiles);

        if (isset($request->uploadedFiles)) {
            for ($i = 0; $i < count($files); $i++) {
                $getFile = public_path('user-uploads/temp/') . $files[$i];
                if (!file_exists(public_path('user-uploads/article-comment-files/'))) {
                    mkdir(public_path('user-uploads/article-comment-files/', '0777'));
                }
                rename($getFile, public_path('user-uploads/article-comment-files/') . $files[$i]);
            }
        }

        $comment = ArticleComment::create([
            'comment' => $request->comment,
            'user_id' => auth()->id(),
            'article_id' => $request->article_id,
            'files' => $request->uploadedFiles
        ]);

        //Notify the creator
        $notifiableIds = array_merge(explode(',', $this->writerHeadAssistant), explode(',', $this->writerHead));
        $notifiables = User::find($notifiableIds);
        Notification::send($notifiables, new ArticleCommentNotification($comment));

        //Notify the writer
        $writer = Article::find($request->article_id)->getAssignee;
        if ($writer->id != auth()->id())
            Notification::send($writer, new ArticleCommentNotification($comment));

        $comment->save();

        //Store in log
        ArticleActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Article',
            'article_id' => $request->article_id,
            'label' => 'article_comment',
            'details' => 'posted a comment.'
        ]);


        return Reply::successWithData('Comment posted!', ['comment' => $comment->id]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function storeFiles(Request $request)
    {
        $request->validate([
            'files.*' => 'max:5120'
        ]);

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            foreach ($files as $file) {
                $filename = $file->getClientOriginalName();
                $ext = strtolower(\File::extension($filename));
                $hashname = str_replace('.' . $ext, '', $filename) . '_' . date('dmys');
                $rename = $this->slugify($hashname) . '.' . $ext;
                $file->move(public_path('user-uploads/temp/'), $rename);
                $getFiles[] = $rename;
            }

            $count = count($getFiles);

            return Reply::successWithData('Files uploaded!', ['files' => $getFiles, 'count' => $count]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show(ArticleComment $comment)
    {
        $this->comment = $comment;
        $this->files = explode(',', $comment);
        $view = view('article::comment.show', ['comment' => $this->comment, 'files' => $this->files])->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    /**
     * Download the specified resource.
     * @param int $file
     * @return Response
     */
    public function download($file)
    {
        $filePath = public_path('user-uploads/article-comment-files/' . $file);
        return file_exists($filePath) ? response()->download($filePath, $file) : abort(404);
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
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(ArticleComment $comment)
    {
        if ($comment->files != null) {
            $files = explode(',', $comment->files);
            for ($i = 0; $i < count($files); $i++) {
                unlink(public_path('/user-uploads/article-comment-files/' . $files[$i]));
            }
        }

        //Store in log
        ArticleActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Article',
            'article_id' => $comment->article->id,
            'label' => 'article_comment',
            'details' => 'deleted a comment.'
        ]);

        $comment->delete();

        return Reply::success('Comment deleted!');
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
