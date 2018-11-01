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
use App\Models\Smsapisetting;
class SettingController extends Controller
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
            ->header('Settings')
            ->description('Settings ..')
            ->body('nothing');
    }

    public function smspisetting(Content $content)
    {
        return $content
            ->header('Settings')
            ->description('SMS API ..')
            ->body($this->smsapiform()->edit(1));
    }

    public function smsapiformsave(Request $request){
        request()->validate([
            'username' => 'required',
            'passowrd' => 'required',
            'apiurl'=>'required'
        ]);

        if(Smsapisetting::findOrFail(1)->update($request->all())){
            admin_success('Success','Setting has been successfully updated!');
            return redirect(admin_base_path('/setting/smspisetting'));
        }else{
            admin_error('Error','Something went wrong! Please Try Again.');
            return back();
        }
    }

    /**SMS API SETTING FORM**/
    protected function smsapiform()
    {
       
        $form = new Form(new Smsapisetting());
        $form->hidden('id','');
        $form->text('username', trans('API Username'))->rules('required');
        $form->text('passowrd', trans('API Password'))->rules('required');
        $form->url('apiurl', trans('API HTTTP URL'))->rules('required');
        $form->setAction('/admin/setting/smsapiformsave');
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
        
            $form->saved(function () {
                admin_toastr(trans('admin.update_succeeded'));
                return redirect(admin_base_path('/setting/smsapiform'));
            });
        return $form;
    }


    
}
