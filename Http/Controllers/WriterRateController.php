<?php

namespace Modules\Article\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Http\Controllers\Member\MemberBaseController;
use Modules\Article\Notifications\WriterRate as WriterRateNotification;
use Modules\Article\Notifications\WriterRateAdmin;
use Modules\Article\Entities\WriterRate;
use Modules\Article\Entities\ArticleActivityLog;
use Illuminate\Support\Facades\Notification;
use App\Helper\Reply;
use App\User;
use App\RoleUser;

class WriterRateController extends Controller
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
        //
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
    public function update(Request $request, $writer)
    {
        $this->rate = WriterRate::where('user_id', $writer)->first();
        $this->original = $this->rate ? $this->rate->rate : 0;
        if ($this->rate == null) {
            $this->rate = WriterRate::create([
                'user_id' => $writer,
                'rate' => $request->rate
            ]);
            $old_rate = 0;
        } else {
            $old_rate = $this->rate->rate;
            $this->rate->rate = $request->rate;
            $this->rate->save();
        }

        $notifyTo = User::find($writer);
        Notification::send($notifyTo, new WriterRateNotification($old_rate));

        $notifyTo = RoleUser::where('role_id', 1)->get();
        foreach ($notifyTo as $notifyTo) {
            $notifyTo = User::find($notifyTo->user_id);
            Notification::send($notifyTo, new WriterRateAdmin($writer, $old_rate));
        }

        //Store in log
        ArticleActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Writer',
            'writer_id' => $this->rate->user_id,
            'label' => 'writer_rate',
            'details' => 'updated writer rate from '. $this->original .' BDT to '. $request->rate .' BDT.'
        ]);

        return Reply::success('Writer rate updated!');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
