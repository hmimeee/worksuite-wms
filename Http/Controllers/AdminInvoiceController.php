<?php

namespace Modules\Article\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Http\Controllers\Admin\AdminBaseController;
use Modules\Article\Entities\Writer;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\Invoice;
use Modules\Article\Entities\ArticleSetting;
use App\Helper\Reply;
use Carbon\Carbon;

class AdminInvoiceController extends AdminBaseController
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
        parent::__construct();
        $this->pageTitle = 'Article Payslips';
        $this->pageIcon = 'ti-receipt';
        $this->user = auth()->user();
        $this->writerRole = ArticleSetting::where('type', 'writer')->first()->value;
        $this->inhouseWriterRole = ArticleSetting::where('type', 'inhouse_writer')->first()->value;
        $this->writerHead = ArticleSetting::where('type', 'writer_head')->first()->value;
        $this->publisher = ArticleSetting::where('type', 'publisher')->first()->value;
    }

    //Writers
    public function getWriters()
    {
        $this->roleName = ArticleSetting::where('type', 'writer')->first()->value;
        $this->writers = Writer::withoutGlobalScope('active')->with('unavailable')->join('role_user', 'role_user.user_id', '=', 'users.id')
        ->join('roles', 'roles.id', '=', 'role_user.role_id')
        ->select('users.id', 'users.name', 'users.image', 'users.email', 'users.created_at')
        ->where('roles.name',$this->roleName)->get();
        return $this->writers;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request, Invoice $invoices)
    {
        if ($request->status == 'paid' || $request->status == 'unpaid') {
            $status = $request->status == 'paid' ? '1' : '0';

            $invoices = $invoices->where('status', $status);
        }

        if ($request->startDate != null && $request->endDate != null) {
            $startDate = Carbon::create($request->startDate);
            $endDate = Carbon::create($request->endDate);

            $invoices = $invoices->whereBetween('created_at', [$request->startDate, $request->endDate]);
        }

        $this->invoices = $invoices->orderBy('created_at', 'DESC')->paginate(is_numeric($request->entries) ? $request->entries : 10);

        return view('article::admin.invoices', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $this->writers = $this->getWriters()->whereNotNull('unavailable');
        return view('article::createInvoice', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $articles = Article::where('assignee', $request->writer)->where('writing_status', 2)->where('invoice_id', null)->get();
        $amount = 0;
        $words = 0;
        foreach ($articles as $article) {
            $amount += $article->rate/1000*$article->word_count;
            $words += $article->word_count;
        }

        $writer = Writer::findOrFail($request->writer);
        $name = 'Invoice_'.date('Y-m-d').'_'.$writer->id.'_('.$writer->name.')';
        if (Invoice::where('name', $name)->first() != null) {
            return Reply::error("Payslip already generated today for this writer");
        }
        $this->invoice = Invoice::create([
            'name' => $name,
            'paid_to' => $writer->id,
            'amount' => $amount,
            'words' => $words,
            'status' => 0
        ]);
        $this->invoice->save();

        foreach ($articles as $article) {
            $status = Article::find($article->id);
            $status->invoice_id = $this->invoice->id;
            $status->article_status = 1;
            $status->save();
        }
        return Reply::successWithData('Payslip generated!', ['id' => $this->invoice->id]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function data($writer)
    {
        $this->articles = Article::with('project')
        ->where('assignee', $writer)
        ->where('writing_status', 2)
        ->whereNull('invoice_id')
        ->whereNotNull('rate')
        ->get();
        return Reply::dataOnly(['articles' => $this->articles, 'count' => count($this->articles)]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $this->invoice = Invoice::findOrFail($id);
        $this->articles = Article::where('invoice_id', $this->invoice->id)->get();
        $this->words = 0;
        $this->amount = 0;
        foreach ($this->articles as $article) {
            $this->words += $article->word_count;
            $this->amount += $article->rate/1000*$article->word_count;
        }
        return view('article::showInvoice', $this->data);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function showModal($id)
    {
        $this->invoice = Invoice::findOrFail($id);
        $this->articles = Article::where('invoice_id', $this->invoice->id)->get();
        $this->words = 0;
        $this->amount = 0;
        foreach ($this->articles as $article) {
            $this->words += $article->word_count;
            $this->amount += $article->rate/1000*$article->word_count;
        }
        return view('article::modalInvoice', $this->data);
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
    public function update(Request $request, Invoice $invoice)
    {
        $invoice->status = $request->status;
        if ($request->status == 1) {
            $invoice->paid_date = date('Y-m-d');
        } else {
            $invoice->paid_date = null;
        }
        $invoice->save();
        return Reply::success('Status updated!');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $articles = Article::where('invoice_id', $invoice->id)->get();
        foreach ($articles as $article) {
            $change = Article::find($article->id);
            $change->article_status = null;
            $change->invoice_id = null;
            $change->save();
        }
        $invoice->delete();
        return Reply::success('Invoice deleted!');
    }
}
