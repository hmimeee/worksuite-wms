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
    $this->publisher = ArticleSetting::where('type', 'publisher')->first()->value ?? '';
    $this->outreachHead = ArticleSetting::where('type', 'outreach_head')->first()->value ?? '';
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
    $writer = ArticleSetting::where('type', 'writer')->first();
    if($writer == null){
        ArticleSetting::create([
            'name' => 'Remote Writer',
            'type' => 'writer',
            'value' => $request->writer
        ]);
    } else {
        $writer->value =  $request->writer;
        $writer->save();
    }

    $inhouse_writer = ArticleSetting::where('type', 'inhouse_writer')->first();
    if($inhouse_writer == null){
        ArticleSetting::create([
            'name' => 'Inhouse Writer',
            'type' => 'inhouse_writer',
            'value' => $request->inhouse_writer
        ]);
    } else {
        $inhouse_writer->value =  $request->inhouse_writer;
        $inhouse_writer->save();
    }

    $writerHead = ArticleSetting::where('type', 'writer_head')->first();
    if($writerHead == null){
        ArticleSetting::create([
            'name' => 'Writer Head',
            'type' => 'writer_head',
            'value' => $request->writer_head
        ]);
    } else {
        $writerHead->value =  $request->writer_head;
        $writerHead->save();
    }

    $publisher = ArticleSetting::where('type', 'publisher')->first();
    if($publisher == null){
        ArticleSetting::create([
            'name' => 'Publisher Head',
            'type' => 'publisher',
            'value' => $request->publisher
        ]);
    } else {
        $publisher->value =  $request->publisher;
        $publisher->save();
    }

    $outreach_head = ArticleSetting::where('type', 'outreach_head')->first();
    if($outreach_head == null){
        ArticleSetting::create([
            'name' => 'Outreach Head',
            'type' => 'outreach_head',
            'value' => $request->outreach_head
        ]);
    } else {
        $outreach_head->value =  $request->outreach_head;
        $outreach_head->save();
    }

    $outreach_category = ArticleSetting::where('type', 'outreach_category')->first();
    if($outreach_category == null){
        ArticleSetting::create([
            'name' => 'Outreach Category',
            'type' => 'outreach_category',
            'value' => $request->outreach_category
        ]);
    } else {
        $outreach_category->value =  $request->outreach_category;
        $outreach_category->save();
    }

    //Company Address
    $address = ArticleSetting::where('type', 'address')->first();
    if ($address == null) {
        ArticleSetting::create([
            'type' => 'address',
            'name' => 'Comapny Address',
            'value' => str_replace('</p>', '<br/>', str_replace('<p>', '', $request->address))
        ]);
    } else {
        $address->value =  str_replace('</p>', '<br/>', str_replace('<p>', '', $request->address));
        $address->save();
    }

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
