<?php

namespace App\Admin\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class GeneralSettingsController extends Controller
{
    
    public function index(Content $content)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request){
        $validate = request()->validate([
            'date_format' => 'required',
        ]);

        if(GeneralSetting::findOrFail(1)->update($request->all())){
            admin_success('Success','Settings has been successfully updated!');
            return back();
        }else{
            admin_error('Error','Something went wrong! Please Try Again.');
            return back();
        }
    }

    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit(Content $content)
    {
        return $content
            ->header(trans('General Settings'))
            ->description(trans('Edit General Settings'))
            ->body($this->form()->edit(1));
    }   

    public function form($action = null)
    {

        $settings = config('admin.database.generalsettings_model');
        $form = new Form(new $settings());
        $form->setAction('/admin/settings');
        $form->row(function ($row) use ( $form) 
            { 
                $row->width(4)->select('date_format', 'Date Format')
                ->options(
                    [
                        'd/m/Y'=>"d/m/Y",
                        'm/d/Y'=>"m/d/Y",
                        'Y/m/d'=>"Y/m/d",
                        'Y/d/m'=>"Y/d/m",
                        'd-m-Y'=>"d-m-Y",
                        'm-d-Y'=>"m-d-Y",
                        'Y-m-d'=>"Y-m-d",
                        'Y-d-m'=>"Y-d-m"
                    ]
                );
                $row->width(4)
                ->select('time_from', 'Time From')
                ->options(
                    [
                        '01'=>"01 AM",                
                        '02'=>"02 AM",                
                        '03'=>"03 AM",                
                        '04'=>"04 AM",                
                        '05'=>"05 AM",                
                        '06'=>"06 AM",                
                        '07'=>"07 AM",                
                        '08'=>"08 AM",                
                        '09'=>"09 AM",                
                        '10'=>"10 AM",
                        '11'=>"11 AM",                
                        '12'=>"12 AM",                
                        '13'=>"01 PM",                
                        '14'=>"02 PM",                
                        '15'=>"03 PM",                
                        '16'=>"04 PM",                
                        '17'=>"05 PM",                
                        '18'=>"06 PM",                
                        '19'=>"07 PM",                
                        '20'=>"08 PM",                
                        '21'=>"09 PM",                
                        '22'=>"10 PM",                
                        '23'=>"11 PM",
                        '24'=>"12 PM",

                    ]
                );

                $row->width(4)
                ->select('time_to', 'Time To')
                ->options(
                    [
                        '01'=>"01 AM",                
                        '02'=>"02 AM",                
                        '03'=>"03 AM",                
                        '04'=>"04 AM",                
                        '05'=>"05 AM",                
                        '06'=>"06 AM",                
                        '07'=>"07 AM",                
                        '08'=>"08 AM",                
                        '09'=>"09 AM",                
                        '10'=>"10 AM",
                        '11'=>"11 AM",                
                        '12'=>"12 AM",                
                        '13'=>"01 PM",                
                        '14'=>"02 PM",                
                        '15'=>"03 PM",                
                        '16'=>"04 PM",                
                        '17'=>"05 PM",                
                        '18'=>"06 PM",                
                        '19'=>"07 PM",                
                        '20'=>"08 PM",                
                        '21'=>"09 PM",                
                        '22'=>"10 PM",                
                        '23'=>"11 PM",
                        '24'=>"12 PM",

                    ]
                );
            },  $form);
        $form->footer(function ($footer) {
            // disable `View` checkbox
            $footer->disableViewCheck();
            // disable `Continue editing` checkbox
            $footer->disableEditingCheck();
            // disable `Continue Creating` checkbox
            $footer->disableCreatingCheck();

        });
        $form->tools(function (Form\Tools $tools) {
            $tools->disableList();
            $tools->disableView();
            $tools->disableDelete();
        });
        $form->saving(function (Form $form) {
            
        });

        return $form;
    }
}
