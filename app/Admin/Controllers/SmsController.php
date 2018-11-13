<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\MessageBag;
use App\Helpers\CommonMethod;

class SmsController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Send SMS')
            ->description('New SMS')
            ->body('nothing');
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }



    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function new(Content $content)
    {
        return $content
            ->header('SMS')
            ->description('Send New SMS')
            ->body($this->form());
    }

    public function send(Request $request)
    {   
        
        $valid = request()->validate([
            'message' => 'required'
        ]);
        
        $clients=$request->input('clients');
        if(is_array($clients)){
            $clients=array_filter($clients);
        }
        if (empty($request->input('phones')) && empty($clients)) {
            admin_error('','Please enter phone# or select client');
            return back();
        }
        
        $smslogModel = config('admin.database.smslog_model');
        $smslogModel=new $smslogModel();
        if(!empty($request->input('phones'))){
            $phones=$request->input('phones');
            $phones=explode(',',$phones);
            foreach($phones as $phone){
                $smslogModel->sendAndLogSms(array('phone'=>$phone,'message'=>$request->input('message')));
            }
        }else{
            $clientModel = config('admin.database.client_model');
            foreach($clients as $client){
                $ClientData=$clientModel::find($client);
                if(!empty($ClientData) && $ClientData->phone){
                    $smslogModel->sendAndLogSms(array('client_id'=>$ClientData->id,'phone'=>$ClientData->phone,'message'=>$request->input('message')));
                }
            }
        }
        admin_success('','Sms sent successfully');
        return redirect()->route('Sms.index');
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new YourModel);

        $grid->id('ID')->sortable();
        $grid->created_at('Created at')->sortable()->display(function($date){
            return CommonMethod::formatDateWithTime($date);
        });
        $grid->updated_at('Updated at')->sortable()->display(function($date){
            return CommonMethod::formatDateWithTime($date);
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(YourModel::findOrFail($id));

        $show->id('ID');
        $show->created_at('Created at')->as(function($date){
            return CommonMethod::formatDateWithTime($date);
        });
        $show->updated_at('Updated at')->as(function($date){
            return CommonMethod::formatDateWithTime($date);
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        
        $smslogModel = config('admin.database.smslog_model');
        $clientModel = config('admin.database.client_model');
        $form = new Form(new $smslogModel());
        $form->text('phones', trans('Mobile Number'));
        $form->multipleSelect('clients', trans('Search Client'))->options($clientModel::all()->pluck('name', 'id'));
        $form->textarea('message', trans('Message'))->rules('required');
        $form->setAction('/admin/sms/send');
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
        
            $form->saving(function ($form) {
                if (empty($form->phones) && empty($form->clients)) {
                    $error = new MessageBag([
                        'title'   => 'title...',
                        'message' => 'Please enter phone# or select client',
                    ]);
                    return back()->with(compact('error'));
                }
            });
            $form->saved(function () {
                admin_toastr(trans('admin.update_succeeded'));
                return redirect(admin_base_path('/sms'));
            });
        return $form;
    }
}
