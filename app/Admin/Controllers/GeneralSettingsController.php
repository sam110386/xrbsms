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
        $settings = GeneralSetting::findOrFail(1);
        $settings->date_format = $request->date_format;
        if($settings->update()){
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
        $form->select('date_format', 'Date Format')->options(            [
                'DD-MM-YYYY'=>"DD-MM-YYYY",
                'MM-DD-YYYY'=>"MM-DD-YYYY",
                'YYYY-MM-DD'=>"YYYY-MM-DD",
                'YYYY-DD-MM'=>"YYYY-DD-MM"
            ]);
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
