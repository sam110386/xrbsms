<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\Models\Smsapisetting;

class ExampleController extends Controller
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
            ->header('Index')
            ->description('description')
            ->body($this->grid());
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
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
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
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');

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
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new YourModel);

        $form->display('id', 'ID');
        $form->display('created_at', 'Created At');
        $form->display('updated_at', 'Updated At');

        return $form;
    }



    public function testSms(){
      $phone='7837837076';
      $phone="+255".substr(preg_replace("/[^0-9]/", "",$phone),-9);
      $msg='Please ignore this sms';        
        // SEND SMS START
        $smsApiConfig = Smsapisetting::find(1);
        $authorization = "{$smsApiConfig->username}:{$smsApiConfig->passowrd}";
        $authorizationEncoded = base64_encode($authorization);
        $baseUrl = $smsApiConfig->apiurl;
        $from = (isset($smsApiConfig->from)) ? $smsApiConfig->from : "INFOSMS";       
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "{$baseUrl}/sms/2/text/single",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "{ \"from\":\"$from\", \"to\":\"$phone\", \"text\":\"$msg\" }",
          CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "authorization: Basic {$authorizationEncoded}",
            "content-type: application/json"
          ),
        ));

        $response = curl_exec($curl);
        //print_r($response);die('heh');
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
          $dataToSave['status'] = 0;
          $dataToSave['error'] = (is_array($err)) ? json_encode($err) : $err;
        } else {
            echo $response;
          $responseArr = json_decode($response,true);
          echo "<pre>"; print_r($responseArr); echo "</pre>"; 
        }        
    }


    public function testSmsStatus(){
        $msgId="1550495314899179574";        
        // SEND SMS START
        $smsApiConfig = Smsapisetting::find(1);
        $authorization = "{$smsApiConfig->username}:{$smsApiConfig->passowrd}";
        $authorizationEncoded = base64_encode($authorization);
        $baseUrl = $smsApiConfig->apiurl;
        $from = (isset($smsApiConfig->from)) ? $smsApiConfig->from : "INFOSMS";       
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "{$baseUrl}/sms/1/reports",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_POSTFIELDS => "{ \"messageId\":\"$msgId\"}",
          CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "authorization: Basic {$authorizationEncoded}",
            "content-type: application/json"
          ),
        ));

        $response = curl_exec($curl);
        //print_r($response);die('heh');
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          $responseArr = json_decode($response,true);
          echo "<pre>"; print_r($responseArr); echo "</pre>"; 
        }
    }
}
