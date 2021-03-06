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
use App\Helpers\CommonMethod;

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
        if(!empty($id)){
            if(strpos($id,',') !== false)
            {   $ids=explode(',', $id);
                foreach($ids as $id):
                    Smslog::where('id', $id)->delete();
                endforeach;
                $res = ['status' => true, 'message' => 'Sms log has been removed.'];
            }else{
                if(Smslog::destroy($id)){
                    $res = ['status' => true, 'message' => 'Sms log has been removed.'];
                }else{
                    $res = ['status' => false, 'message' => 'Something went wrong!'];
                }
            }
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
        
        $sms = Smslog::findOrFail($id);
        $show = new Show($sms);
        $show->id('ID');
        $show->phone(trans('Phone'));
        $show->message(trans('Message Body'));

        $show->display(trans('Message Characters'))->as(function() use($sms){
            return strlen($sms->message);
        });
        $show->created_at(trans('Time Stamp'))->as(function($date){
            return CommonMethod::formatDateWithTime($date);
        });
        $show->status()->using(['1' => 'Pending', '0' => 'Not Sent','2'=>'Undeliverable','3'=>"Delivered",'4'=>'Expired','5'=>'Rejected']);
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
        $grid->model()->orderBy('id', 'desc');
        $grid->id('ID')->sortable();
        //$grid->username(trans('admin.username'));
        $grid->phone(trans('Phone/Client'))->sortable();
        $grid->type(trans('Message Type'))->sortable()->display(function ($type) {
            return config('admin.smslogtypes')[$type];
        });;
        $grid->status(trans('Status'))->sortable()->display(function ($status) {
            $sts = 'Failed';
            if($status==1){
                $sts = 'Pending';
            }elseif($status==2){
                $sts = 'Undeliverable';
            }elseif($status==3){
                $sts = 'Delivered';
            }elseif($status==4){
                $sts = 'Expired';
            }elseif($status==5){
                $sts = 'Rejected';
            }else{
                $sts = 'Unknown';
            }
            return $sts;
        });
        $grid->sender(trans('Sender'))->sortable();
        $grid->updated_at(trans('Timestamp'))->sortable()->display(function($date){
            return CommonMethod::formatDateWithTime($date);
        });

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
            
        });
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableFilter();
        $grid->tools(function (Grid\Tools $tools) {
            
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                //$actions->disableDelete();
            });
        });

        return $grid;
    }

    
}
