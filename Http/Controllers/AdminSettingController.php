<?php

namespace Modules\Article\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Http\Controllers\Admin\AdminBaseController;
use Modules\Article\Entities\ArticleSetting;
use Modules\Article\Entities\ArticleActivityLog;
use Modules\Article\Entities\ArticleType;
use \ZipArchive;
use App\User;
use App\Role;
use App\Helper\Reply;
use Illuminate\Support\Facades\File;

class AdminSettingController extends AdminBaseController
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
        parent::__construct();
        $this->pageTitle = 'Article Management Settings';
        $this->pageIcon = 'ti-settings';
    }
/**
 * Display a listing of the resource.
 * @return Response
 */
public function index()
{
    $this->temp = File::allFiles(public_path('user-uploads/temp'));
    $this->settings = ArticleSetting::all();
    $this->writerRole = ArticleSetting::where('type', 'writer')->first()->value ?? '';
    $this->inhouseWriterRole = ArticleSetting::where('type', 'inhouse_writer')->first()->value ?? '';
    $this->writerHead = ArticleSetting::where('type', 'writer_head')->first()->value ?? '';
    $this->writerHeadAssistant = ArticleSetting::where('type', 'writer_head_assistant')->first()->value ?? '';
    $this->publisher = ArticleSetting::where('type', 'publisher')->first()->value ?? '';
    $this->outreachHead = ArticleSetting::where('type', 'outreach_head')->first()->value ?? '';
    $this->teamLeaders = ArticleSetting::where('type', 'team_leaders')->first()->value ?? '';
    $this->publishers = ArticleSetting::where('type', 'publishers')->first()->value ?? '';
    $this->outreachCategory = ArticleSetting::where('type', 'outreach_category')->first()->value ?? '';
    $this->roles = Role::all();
    $this->employees = User::allEmployees();
    $this->categories = ArticleType::all();
    $this->types = ArticleType::paginate(10);
    $this->module_update = ArticleActivityLog::where('type', 'WMS')->where('label', 'module_update')->orderByDesc('id')->first();

    return view('article::admin.settings', $this->data);
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
public function update(Request $request)
{
    $updateKeys = $request->only([
            'writer',
            'address',
            'outreach_category',
            'outreach_head',
            'publisher',
            'writer_head',
            'writer_head_assistant',
            'inhouse_writer',
            'team_leaders',
            'publishers',
    ]);

    foreach ($updateKeys as $key => $value) {
        ArticleSetting::updateOrCreate(['type' => $key], [
            'name' => ucwords(str_replace('_', ' ', $key)),
            'value' => is_array($value) ? implode(',', $value) : $value
        ]);
    }

        //Store in log
        ArticleActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'settings_update',
            'label' => 'settings_update',
            'details' => 'updated the settings'
        ]);

    return Reply::success('article::app.storeSettingSuccess');
}

/**
 * Update the specified resource in storage.
 * @param Request $request
 * @param int $id
 * @return Response
 */
public function updateModule(Request $request)
{
    if($request->hasFile('package')) {
        $fileData = $request->file('package');
        $filename = $fileData->getClientOriginalName();
        $fileData->move(public_path('user-uploads/temp'), $filename);
        if(substr($filename,0,5) != 'WMS_v'){
            unlink(public_path('user-uploads/temp/').$filename);
            return Reply::error('Package is not an Article Management Module!');
        }
        $zip = new ZipArchive;
        $res = $zip->open(public_path('user-uploads/temp/').$filename);
        if ($res === TRUE) {
          $zip->extractTo(base_path('/Modules/'));
          $zip->close();
          unlink(public_path('user-uploads/temp/').$filename);

           //Store in log
          ArticleActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'WMS',
            'label' => 'module_update',
            'details' => substr(str_replace('.zip', '', $filename), 6)
        ]);

          return Reply::success('Successfully updated!');
      } else {
        unlink(public_path('user-uploads/temp/').$filename);
        return Reply::error('Something went wrong!');
    }
}
}

/**
 * Remove the specified resource from storage.
 * @param int $id
 * @return Response
 */
public function temp()
{
    $temps = File::allFiles(public_path('user-uploads/temp'));

    foreach ($temps as $temp) {
        File::delete($temp);
    }
    return Reply::success('Files are deleted');
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
