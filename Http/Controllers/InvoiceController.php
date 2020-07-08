<?php

namespace Modules\Article\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Member\MemberBaseController;
use Modules\Article\Entities\Writer;
use Modules\Article\Entities\ArticleActivityLog;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\Invoice;
use Modules\Article\Entities\ArticleSetting;
use Illuminate\Support\Facades\Notification;
use Modules\Article\Notifications\NewInvoice;
use Modules\Article\Notifications\InvoicePaid;
use Modules\Article\Notifications\InvoiceUnpaid;
use Modules\Article\Notifications\InvoiceDelete;
use App\Helper\Reply;
use App\RoleUser;
use App\User;
use App\Setting;

class InvoiceController extends MemberBaseController
{
    public function __construct()
    {
        $this->middleware('auth');
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
        $this->writers = Writer::withoutGlobalScope('active')->join('role_user', 'role_user.user_id', '=', 'users.id')
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
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole($this->writerRole) && !auth()->user()->hasRole($this->inhouseWriterRole) && auth()->id() != $this->writerHead) {
            return abort(403);
        }

        $this->invoices = $invoices;
        
        if (auth()->user()->hasRole($this->writerRole) || auth()->user()->hasRole($this->inhouseWriterRole)) {
            $this->invoices = $this->invoices->where('paid_to', auth()->id());
        }

        if ($request->hide == 'on') {
            $this->invoices = $this->invoices->where('status', '<>', 1);
        }

        $this->invoices = $this->invoices->orderBy('status', 'ASC')->paginate(is_numeric($request->entries) ? $request->entries : 10);

        return view('article::invoices', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $this->writers = $this->getWriters();
        return view('article::createInvoice', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $writer = Writer::findOrFail($request->writer);
        $articles = $writer->articles->where('assignee', $request->writer)->where('writing_status', 2)->where('invoice_id', null);
        $amount = 0;
        $words = 0;
        foreach ($articles as $article) {
            $amount += $article->rate/1000*$article->word_count;
            $words += $article->word_count;
        }

        $name = 'Invoice_'.date('Y-m-d').'_'.$writer->id.'_('.$writer->name.')';
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

        $this->invoice->name = '#VXAR'.str_pad($this->invoice->id, 6, 0, STR_PAD_LEFT);
        $this->invoice->save();

        //Store in log
        ArticleActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Payslip',
            'invoice_id' => $this->invoice->id,
            'label' => 'payslip_generate',
            'details' => 'generated the Payslip.'
        ]);

        $notifyTo = Writer::find($this->invoice->paid_to);
        Notification::send($notifyTo, new NewInvoice($this->invoice));

        return Reply::successWithData('Payslip generated!', ['id' => $this->invoice->id]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function data($writer)
    {
        $this->articles = Article::where('assignee', $writer)->where('writing_status', 2)->where('invoice_id', null)->with('project')->get();
        $this->setting = Setting::first();
        return Reply::dataOnly(['articles' => $this->articles, 'count' => count($this->articles)]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $this->setting = Setting::first();
        $this->address = ArticleSetting::where('type', 'address')->first();
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
        $this->setting = Setting::first();
        $this->address = ArticleSetting::where('type', 'address')->first();
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
        if (!Hash::check($request->password, auth()->user()->password)) {
            return Reply::error('Your password is incorrect!');
        }
        $invoice->status = $request->status;
        if ($request->status == 1) {
            $invoice->paid_date = date('Y-m-d');
        } else {
            $invoice->paid_date = null;
        }
        $invoice->save();

        if ($request->status == 0) {
            $message = 'changed the payslip status to unpaid.';
        } elseif ($request->status == 1) {
            $message = 'changed the payslip status to paid.';
        }

         //Store in log
        ArticleActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Payslip',
            'invoice_id' => $invoice->id,
            'label' => 'payslip_status',
            'details' => $message
        ]);

        if ($request->status == 1) {
            $notifyTo = Writer::find($invoice->paid_to);
            Notification::send($notifyTo, new InvoicePaid($invoice));
        } elseif ($request->status == 0) {
            $notifyTo = Writer::find($invoice->paid_to);
            Notification::send($notifyTo, new InvoiceUnpaid($invoice));

            $notifyTo = RoleUser::where('role_id', 1)->get();
            foreach ($notifyTo as $notifyTo) {
                $notifyTo = User::find($notifyTo->user_id);
                Notification::send($notifyTo, new InvoiceUnpaid($invoice));
            }
        }

        return Reply::success('Status updated!');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        if (!Hash::check($request->password, auth()->user()->password)) {
            return Reply::error('Your password is incorrect!');
        }

        $invoice = Invoice::findOrFail($id);
        
        if ($invoice->status == 1) {
            return Reply::error("You can't delete paid payslip!");
        }

        $articles = Article::where('invoice_id', $invoice->id)->get();
        foreach ($articles as $article) {
            $change = Article::find($article->id);
            $change->article_status = null;
            $change->invoice_id = null;
            $change->save();
        }

        $notifyTo = Writer::find($invoice->paid_to);
        Notification::send($notifyTo, new InvoiceDelete($invoice));

        $notifyTo = RoleUser::where('role_id', 1)->get();
        foreach ($notifyTo as $notifyTo) {
            $notifyTo = User::find($notifyTo->user_id);
            Notification::send($notifyTo, new InvoiceDelete($invoice));
        }

        $invoice->delete();
        return Reply::success('Payslip deleted!');
    }
}
