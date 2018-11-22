<?php

namespace App\Admin\Controllers;
use Encore\Admin\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\MessageBag;
use App\Helpers\CommonMethod;
use App\Models\Client;
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
        return redirect()->back();
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
        $script = <<<SCRIPT
                    $(document).on("keyup focus", "#messagebody",function(src) {
                        var chars = this.value.length;
                        var s = (chars>1) ? "s" : "";
                        $("#charleft").html( chars +" character" + s + ".");
                    });
SCRIPT;

        Admin::script($script);
        $smslogModel = config('admin.database.smslog_model');
        $clientModel = config('admin.database.client_model');

        $sms_variables = config('admin.sms_variables');
        $sms_variables = (!empty($sms_variables)) ? implode(" ",array_keys($sms_variables)) : "" ;

        $form = new Form(new $smslogModel());

        $form->html('<div class="modal fade" id="smsVariables" data-controls-modal="smsVariables" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="smsVariables">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button id="cancelSmallBtn" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myActionTitle">SMS Variables</h4>
                                </div>
                                <div class="modal-body">
                                    <p class="h4">'.$sms_variables.'</p>
                                </div>
                            </div>
                        </div>
                    </div>');        
        $form->mobile('phones', trans('Mobile Number'))->options(['mask' => '999 999 9999']);
        $form->multipleSelect('clients', trans('Search Client'))->options($clientModel, 'name', 'id')->ajax('/admin/clients/autocomplete');//->options($clientModel::all()->pluck('name', 'id'));
        $form->textarea('message', trans('Message'))->rules('required')->attribute(['id'=>"messagebody"]);
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

        $form->tools(function (Form\Tools $tools) {
            $tools->add('<div class="btn-group pull-right" style="margin-right: 5px">
                            <a data-toggle="modal" data-target="#smsVariables" href="#" class="btn btn-sm btn-info sms-variables" title="SMS Variables"><i class="fa fa-list"></i><span class="hidden-xs">&nbsp;SMS Variables</span></a>
                        </div>'
                    );
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


    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function bulk(Content $content)
    {   
        $sms_variables = config('admin.sms_variables');
        $sms_variables = (!empty($sms_variables)) ? implode(" ",array_keys($sms_variables)) : "" ;
        return $content
            ->header('SMS')
            ->description('Send Bulk SMS')
            ->body(view('Sms.bulk',compact('sms_variables')));
    }
    
    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function bulkform($action = null)
    {
        $clientModel = config('admin.database.client_model');
        $form = new Form(new $clientModel());

        if($action) $form->setAction($action);
        
        $form->row(function ($row) use ( $form) {
            $row->width(2)->html(
                "<div class='box-header'><h3 class='box-title text-upper box-header'>Message Type</h3></div>"
            );
            $row->width(10)->radio('messagetype','')->options(['g' => 'General', 't'=> 'Tax Basis'])->default('g')->attribute(['class'=>'inline']);

        },$form);

        $form->row(function ($row) use ( $form) {
            $row->width(6)->select('user_type', 'Client Type')->options(array(1=>"Individual",2=>"Company"));
            $row->width(6)->select('gender', 'Gender')->options(array('M'=>"Male",'F'=>"Female"));
            //$row->width(6)->select('language', 'Language')->options(array('en'=>"English"));
            $row->width(6)->select('certificate_printed', 'Certificate Printed')->options(array(1=>"Yes",0 => 'No'));
            $row->width(6)->text('zipcode', 'Zipcode')->attribute(['maxlength'=>8]);

        },$form);

        // ADDRESS INFORMATION
        /*$form->row(function ($row) use ( $form) {
            $row->width(12)->html(
                "<div class='box-header with-border'><h3 class='box-title text-upper box-header'>ADDRESS INFORMATION</h3></div>"
            );
            $row->width(6)->text('address', 'Street Address');
            $row->width(6)->text('region', 'Region');
            $row->width(6)->text('district', 'District');
            $row->width(6)->text('ward', 'Ward');
        },$form);*/

        // REGISTRATION INFORMATION
        /*$form->row(function ($row) use ( $form) {
            $row->width(12)->html(
                "<div class='box-header with-border'><h3 class='box-title text-upper box-header'>REGISTRATION INFORMATION</h3></div>"
            );
            $row->width(6)->text('registration_number', 'Registration Number')->attribute(['maxlength'=>20]);
            $row->width(6)->date('registration_date', 'Registration Date');
        },$form);*/
        $form->row(function ($row) use ( $form) {
            $clientModel = config('admin.database.client_model');
            $row->width(6)->listbox('clients', trans('Clients'))->options($clientModel, 'name', 'id')->ajax('/admin/clients/autocomplete')->attribute(['size'=>'10']);
            $row->width(6)->listbox('client1s', trans('Clients'))->options($clientModel::all()->pluck('name', 'id'));
        },$form);
        // TAX INFORMATION
        $form->row(function ($row) use($form) {
            $row->width(12)->html(
                "<div class='box-header with-border'><h3 class='box-title text-upper box-header'>TAX INFORMATION</h3></div>"
            );
            $row->width(6)->select('exempt', 'Exempt')->options(array(1=>"Yes",0 => 'No'));
            $row->width(6)->select('tax_type', 'Tax Type')->options(array('VAT'=>"VAT",'non-VAT' => 'non-VAT'));
            $row->width(6)->select('filling_type', 'Filling Type')->options(array('regular'=>"Regular",'lamp-sum' => 'Lamp sum'));
            $row->width(6)->select('filling_period', 'Filling Period')->options(array('annual'=>"Annual",'quarterly' => 'Quarterly'));
            $row->width(6)->select('filling_currency', 'Filling Currency')->options(array('TSH'=>"TSH",'USD' => 'USD'));
            $row->width(6)->date('due_date', 'Due (Expiration)');
            $row->width(6)->text('total_amount', 'Total amount');
            $row->width(6)->text('penalty_amount', 'Penalty Amount');
        },$form);

        
        $form->footer(function ($footer) {
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

    public function loadclients(Request $request)
    {   $clients=DB::table('clients')->select('name', 'id')->orderBy('name','ASC');
        $user_type= $request->get('user_type');
        if(!empty($user_type)){
            $clients->where('user_type', $user_type);
        }
        $gender= $request->get('gender');
        if(!empty($gender)){
            $clients->where('gender', $gender);
        }
        $certificate_printed= $request->get('certificate_printed');
        if(!empty($certificate_printed)){
            $clients->where('certificate_printed', $certificate_printed);
        }
        $zipcode= $request->get('zipcode');
        if(!empty($zipcode)){
            $clients->where('zipcode','like', "%$zipcode%");
        }

        $exempt= $request->get('exempt');
        if(!empty($exempt)){
            $clients->where('exempt', $exempt);
        }
        $tax_type= $request->get('tax_type');
        if(!empty($tax_type)){
            $clients->where('tax_type', $tax_type);
        }
        $filling_type= $request->get('filling_type');
        if(!empty($filling_type)){
            $clients->where('filling_type', $filling_type);
        }
        $filling_period= $request->get('filling_period');
        if(!empty($filling_period)){
            $clients->where('filling_period', $filling_period);
        }
        $filling_currency= $request->get('filling_currency');
        if(!empty($filling_currency)){
            $clients->where('filling_currency', $filling_currency);
        }
        $due_from= $request->get('due_from');
        if(!empty($due_from)){
            $clients->where('due_date','>=', $due_from);
        }
        $due_to= $request->get('due_to');
        if(!empty($due_to)){
            $clients->where('due_date','<=', $due_to);
        }
        $taxcategory =  $request->get('taxcategory');
        if(!empty($taxcategory)){
            $clients->where('taxcategory', $taxcategory);
        }
        return response()->json(array('status'=>'success','data'=>$clients->get(),'count'=>$clients->get()->count()));
    }

     public function sendbulk(Request $request)
    {   
        
        
        $clients=$request->input('clients');
        if(is_array($clients)){
            $clients=array_filter($clients);
        }
        if (empty($clients)) {
            admin_error('','Please select client');
            return back()->with(['message'=>$request->input('message')]);
        }

        $message=$request->input('message');
        if (empty($message)) {
            admin_error('','Please enter message');
            return back();
        }


        $smslogModel = config('admin.database.smslog_model');
        $smslogModel=new $smslogModel();
        $clientModel = config('admin.database.client_model');
        foreach($clients as $client){
            $ClientData=$clientModel::find($client);
            if(!empty($ClientData) && $ClientData->phone){
                $msgs[] = $smslogModel->sendAndLogSms(array('client_id'=>$ClientData->id,'phone'=>$ClientData->phone,'message'=>$request->input('message')));
            }
        }
        
        admin_success('','Sms sent successfully ');
        return redirect()->back();
    }
}
