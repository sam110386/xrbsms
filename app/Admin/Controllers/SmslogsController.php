<?php

namespace App\Admin\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Smslog;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
class SmslogsController extends Controller
{
    
    public function index(Content $content)
    {
        return $content
            ->header('Sms Logs')
            ->description('Logs..')
            ->body($this->grid()->render());

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
            ->header(trans('Message Details'))
            ->description(trans('Details'))
            ->body($this->detail($id));
    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Smslog::destroy($id)){
            $res = ['status' => true, 'message' => 'Sms log has been removed.'];
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
        
        $show = new Show(Smslog::findOrFail($id));

        $show->id('ID');
        $show->phone(trans('Phone'));
        $show->message(trans('Message Body'));
        $show->created_at(trans('Time Stamp'));
        $show->status()->using(['1' => 'Sent Successfully', '0' => 'Not Sent']);
        $show->type()->using(config('admin.smslogtypes'));
        $show->panel()
        ->tools(function ($tools) {
            $tools->disableEdit();
            $tools->disableDelete();
        });
        return $show;
    }

    protected function grid()
    {
        
        $grid = new Grid(new Smslog());

        $grid->id('ID')->sortable();
        //$grid->username(trans('admin.username'));
        $grid->phone(trans('Phone/Client'))->sortable();
        $grid->type(trans('Message Type'))->sortable()->display(function ($type) {
            return config('admin.smslogtypes')[$type];
        });;
        $grid->status(trans('Status'))->sortable()->display(function ($status) {
            return $status ? 'Sent Successfully' : 'Failed';
        });
        $grid->updated_at(trans('Timestamp'))->sortable();
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
            
        });
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableFilter();
        $grid->tools(function (Grid\Tools $tools) {
            
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });
        });

        return $grid;
    }

    
}
