<?php

namespace App\Admin\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Smsscheduletype;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
class SmsscheduleController extends Controller
{
    
    public function index(Content $content)
    {
         $content
            ->header('SMS Contents')
            ->description('Manage ...')
            ->body($this->grid()->render());
        $content->breadcrumb(
                ['text' => 'SMS Contents']
            );
        return $content;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Content $content)
    { 
        //return view('Clients.create');
         $content
            ->header('SMS Contents')
            ->description('Create...')
            ->body($this->form('/admin/smsschedule/store'));
            $content->breadcrumb(
                ['text' => 'SMS Contents', 'url' => '/admin/smsschedule'],
                ['text' => 'Create']
            );
            return $content;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        request()->validate([
			'username' => 'required',
			'password' => 'required',
            'apiurl' => 'required',
            'title' => 'required',
            'frequency' => 'required',
            'en_smsbody' => 'required'
        ]);
        if(Smsscheduletype::create($request->all())){
            admin_success('Success','Schedule has been successfully added!');
            return redirect()->route('Smsschedule.index');
        }else{
            admin_error('Error','Something went wrong! Please Try Again.');
            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request){
        $client = request()->validate([
            'username' => 'required',
            'password' => 'required',
            'apiurl' => 'required',
            'title' => 'required',
            'frequency' => 'required',
            'en_smsbody' => 'required'
        ]);
        if(Smsscheduletype::findOrFail($id)->update($request->all())){
            admin_success('Success','Schedule has been successfully updated!');
            return redirect()->route('Smsschedule.index');
        }else{
            admin_error('Error','Something went wrong! Please Try Again.');
            return back();
        }
    }
    


    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     *
     * @return Content
     */
    public function show($id, Content $content)
    {
         $content
            ->header('SMS Contents')
            ->description('...')
            ->body($this->detail($id));
            $content->breadcrumb(
                ['text' => 'SMS Contents', 'url' => '/admin/smsschedule'],
                ['text' => Smsscheduletype::find($id)->title]
            );
            return $content;
    }

    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
         $content
             ->header('SMS Contents')
            ->description('...')
            ->body($this->form()->edit($id));
            $content->breadcrumb(
                ['text' => 'SMS Contents', 'url' => '/admin/smsschedule'],
                ['text' => Smsscheduletype::find($id)->title]
            );
            return $content;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Smsscheduletype::destroy($id)){
            $res = ['status' => true, 'message' => 'Schedule has been removed.'];
        }else{
            $res = ['status' => false, 'message' => 'Something went wrong!'];
        }
        return response()->JSON($res);
    }    

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        
        $show = new Show(Smsscheduletype::findOrFail($id));

        //$show->id('ID');
        $show->title(trans('Title'));
        $show->username(trans('API Username'));
        $show->password(trans('API Password'));
        $show->apiurl(trans('API URL'));
        $show->en_smsbody(trans('SMS Template'));
        $show->frequency(trans('Frequency'))->using(['1'=>"Daily",'5'=>"5 Days Before",'10'=>'10 Days Before','15'=>'15 Days Before','30'=>'30 Days Before','60'=>'60 Days Before']);
        $show->lastrundatetime(trans('Last Run'));
        $show->lastrunsms(trans('Last SMS Sent'));
        $show->status(trans('Status'))->using(['0' => 'Inactive', '1' => 'Active']);
       // $show->created_at(trans('admin.created_at'));
        //$show->updated_at(trans('admin.updated_at'));

        return $show;
    }

    protected function grid()
    {
        
        $grid = new Grid(new Smsscheduletype());
        $grid->disableExport();
        $grid->id('ID')->sortable();
        $grid->title(trans('Title'));
        //$grid->username(trans('API Username'));
        //$grid->password(trans('API Password'));
        $grid->status(trans('Status'))->display(function($status){
            return ($status) ? 'Active' : 'Inative';
        });
        $grid->frequency(trans('Frequency'))->sortable()->display(function($frequency){
            return array('1'=>"Daily",'5'=>"5 Days Before",'10'=>'10 Days Before','15'=>'15 Days Before','30'=>'30 Days Before','60'=>'60 Days Before')[$frequency];
        });
        $grid->lastrundatetime(trans('Last Run'))->sortable();    
        $grid->lastrunsms(trans('Last SMS Sent'))->sortable();
        //$grid->created_at(trans('Created_at'))->sortable();
        //$grid->updated_at(trans('Updated_at'))->sortable();
        $grid->disableFilter();
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            
        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });
        });

        return $grid;
    }

    public function form($action = null)
    {
    
        $form = new Form(new Smsscheduletype());
        
        if($action) $form->setAction($action);
        
        $form->hidden('id');

        $form->text('title', 'Title')->rules('required');
        $form->text('username', 'API Username')->rules('required');
        $form->text('password', 'API Password')->rules('required');
        $form->url('apiurl', 'API URL')->rules('required');
        $form->text('en_smsbody', 'SMS Template')->rules('required');
        $form->select('frequency', 'Frequency')->options(array('1'=>"Daily",'5'=>"5 Days Before",'10'=>'10 Days Before','15'=>'15 Days Before','30'=>'30 Days Before','60'=>'60 Days Before'));
        $form->select('status', 'Status')->options(array('0'=>"Inactive",'1'=>"Active"));
        //$form->display('created_at', trans('admin.created_at'));
        //$form->display('updated_at', trans('admin.updated_at'));
        $form->footer(function ($footer) {
            // disable reset btn
            $footer->disableReset();
            // disable `View` checkbox
            $footer->disableViewCheck();
            // disable `Continue editing` checkbox
            $footer->disableEditingCheck();
            // disable `Continue Creating` checkbox
            $footer->disableCreatingCheck();

        });
        $form->saving(function (Form $form) {
            
        });

        return $form;
    }
}
