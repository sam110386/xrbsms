<?php

namespace App\Admin\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Helpers\CommonMethod;

class ClientController extends Controller
{
    
    public function index(Content $content)
    {
        //$Clients = Client::latest()->paginate(10);
        // return view('Clients.index',compact('Clients'))
            //->with('i', (request()->input('page', 1) - 1) * 5);
        return $content
            ->header('Clients')
            ->description('Manage clients...')
            //->body(view('Clients.index',compact('Clients')));
            ->body($this->grid()->render());

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Content $content)
    { 
        //return view('Clients.create');
        return $content
            ->header('Clients')
            ->description('Create clients...')
            ->body($this->form('/admin/clients'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $valid = request()->validate([
			'name' => 'required',
			'phone' => 'required'
        ]);
        if(Client::create($request->all())){
            admin_success('Success','Client has been successfully added!');
            return redirect()->route('Clients.index');
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
            'name' => 'required',
            'phone' => 'required'
        ]);
        if(Client::findOrFail($id)->update($request->all())){
            admin_success('Success','Client has been successfully updated!');
            return redirect()->route('Clients.index');
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
        return $content
            ->header(trans('Clients'))
            ->description(trans('admin.detail'))
            ->body($this->detail($id));
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
        return $content
            ->header(trans('Clients'))
            ->description(trans('admin.edit'))
            ->body($this->form()->edit($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Client::destroy($id)){
            $res = ['status' => true, 'message' => 'Client has been removed.'];
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
        $clientModel = config('admin.database.client_model');

        $show = new Show($clientModel::findOrFail($id));

        $show->id('ID');
        $show->name(trans('Name'));
        $show->phone(trans('Phone'));
        $show->user_type(trans('Client Type'))->using([1=>"Individual",2=>"Company"]);
        $show->gender(trans('Gender'))->using(['M' => 'Male', 'F' => 'Female']);
        $show->language(trans('Language'))->using(['en' => 'English']);

        // ADDRESS INFORMATION
        $show->address(trans('Street Address'));
        $show->region(trans('Region'));
        $show->district(trans('District'));
        $show->ward(trans('Ward'));
        $show->zipcode(trans('Zipcode'));

        // REGISTRATION INFORMATION
        $show->registration_number(trans('Registration Number'));
        $show->registration_date(trans('Registration Date'))->as(function($date){
            return CommonMethod::formatDateWithTime($date);
        });

        // TAX INFORMATION
        $show->exempt(trans('exempt'))->using([1=>"Yes",0 => 'No']);
        $show->tax_type(trans('Tax Type'))->using(['VAT'=>"VAT",'non-VAT' => 'non-VAT']);
        $show->filling_type(trans('Filling Type'))->using(['regular'=>"Regular",'lamp-sum' => 'Lamp sum']);
        $show->filling_period(trans('Filling Period'))->using(['annual'=>"Annual",'quarterly' => 'Quarterly']);
        $show->filling_currency(trans('Filling Currency'))->using(['TSH'=>"TSH",'USD' => 'USD']);
        $show->due_date(trans('Due (Expiration)'))->as(function($date){
            return CommonMethod::formatDateWithTime($date);
        });
        $show->total_amount(trans('Total Amount'));
        $show->penalty_amount(trans('Penalty Amount'));
        
        $show->certificate_printed(trans('Certificate Printed'))->using(['0' => 'No', '1' => 'Yes']);
        

        $show->status(trans('Status'))->using(['0' => 'Inactive', '1' => 'Active']);
        $show->created_at(trans('admin.created_at'))->as(function($date){
            return CommonMethod::formatDateWithTime($date);
        });
        $show->updated_at(trans('admin.updated_at'))->as(function($date){
            return CommonMethod::formatDateWithTime($date);
        });

        return $show;
    }

    protected function grid()
    {
        $clientModel = config('admin.database.client_model');

        $grid = new Grid(new $clientModel());
        $grid->disableExport();
        $grid->id('ID')->sortable();
        $grid->name(trans('Name'))->sortable();
        $grid->phone(trans('Phone'))->sortable();
        $grid->gender(trans('Gender'))->sortable()->display(function($gender){
            $g = "";
            if($gender == 'F'){
                $g = "Female";
            }elseif($gender == 'M'){
                $g = "Male";
            }
            return $g;
        });
        $grid->status(trans('Status'))->display(function($status){
            return ($status) ? 'Active' : 'Inative';
        });     
        $grid->created_at(trans('Created_at'))->sortable()->display(function($date){
            return CommonMethod::formatDateWithTime($date);
        });
        $grid->updated_at(trans('Updated_at'))->sortable()->display(function($date){
            return CommonMethod::formatDateWithTime($date);
        });

        
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            // $actions->resource = 'client/delete';
            // if ($actions->getKey() == 1) {
            //     $actions->disableDelete();
            // }
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
        $clientModel = config('admin.database.client_model');
        

        $form = new Form(new $clientModel());
        
        if($action) $form->setAction($action);
        
        $form->row(function ($row) use ( $form) {
            $row->width(6)->text('name', 'Name')->rules('required');
            $row->width(6)->text('phone', 'Phone')->rules('required');
            $row->width(6)->select('user_type', 'Client Type')->options(array(1=>"Individual",2=>"Company"));
            $row->width(6)->select('gender', 'Gender')->options(array('M'=>"Male",'F'=>"Female"));
            $row->width(6)->select('language', 'Language')->options(array('en'=>"English"));

        },$form);

        // ADDRESS INFORMATION
        $form->row(function ($row) use ( $form) {
            $row->width(12)->html(
                "<div class='box-header with-border'><h3 class='box-title text-upper box-header'>ADDRESS INFORMATION</h3></div>"
            );
            $row->width(6)->text('address', 'Street Address');
            $row->width(6)->text('region', 'Region');
            $row->width(6)->text('district', 'District');
            $row->width(6)->text('ward', 'Ward');
            $row->width(6)->text('zipcode', 'Zipcode');

        },$form);

        // REGISTRATION INFORMATION
        $form->row(function ($row) use ( $form) {
            $row->width(12)->html(
                "<div class='box-header with-border'><h3 class='box-title text-upper box-header'>REGISTRATION INFORMATION</h3></div>"
            );
            $row->width(6)->text('registration_number', 'Registration Number');
            $row->width(6)->date('registration_date', 'Registration Date');
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

        $form->row(function ($row) use($form) {
            $row->width(12)->html();
            $row->width(6)->select('certificate_printed', 'Certificate Printed')->options(array(1=>"Yes",0 => 'No'));
            $row->width(6)->select('status', 'Status')->options(array(1=>"Active",0 => 'Inactive'));

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


    public function autocomplete(Request $request)
    {
        $q = $request->get('q');
        return Client::where('name', 'like', "%$q%")->orWhere('phone', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
    }
}
