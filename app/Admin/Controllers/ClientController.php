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
            ->body($this->form());
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        print_r($request->all());
        $valid = request()->validate([
			'name' => 'required',
			'phone' => 'required'
        ]);

        Client::create($request->all());
        return redirect()->route('Clients.index')->with('success','Client created successfully');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
            ->header(trans('admin.administrator'))
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
            ->header(trans('admin.administrator'))
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
        Client::destroy($id);
        return redirect()->route('Clients.index')
                        ->with('success','Client deleted successfully');
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
        $show->name(trans('admin.name'));
        $show->phone(trans('admin.phone'));
        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));

        return $show;
    }

    protected function grid()
    {
        $clientModel = config('admin.database.client_model');

        $grid = new Grid(new $clientModel());

        $grid->id('ID')->sortable();
        //$grid->username(trans('admin.username'));
        $grid->name(trans('admin.name'));
        //$grid->roles(trans('admin.roles'))->pluck('name')->label();
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            if ($actions->getKey() == 1) {
                $actions->disableDelete();
            }
        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });
        });

        return $grid;
    }

    public function form()
    {
        $clientModel = config('admin.database.client_model');
        

        $form = new Form(new $clientModel());
        
        $form->setAction('/admin/clients');
        
        $form->display('id', 'ID');

        $form->text('name', 'Name')->rules('required');
        $form->text('phone', 'Phone')->rules('required');
        
        $form->select('user_type', 'Client Type')->options(array(1=>"Individual",2=>"Company"));
        $form->select('gender', 'Gender')->options(array('M'=>"Male",'F'=>"Female"));
        $form->select('status', 'Status')->options(array('0'=>"Inactive",'1'=>"Active"));
        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        $form->saving(function (Form $form) {
            
        });

        return $form;
    }
}
