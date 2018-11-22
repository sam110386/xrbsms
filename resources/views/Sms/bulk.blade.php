<style type="text/css">
.duedate{margin: 27px 0;}
</style>
<div class="row">
	<div class="col-md-12">
		<div class="box box-info">
			<form action="/admin/sms/sendbulk" method="post" accept-charset="UTF-8" id="sendbulkform" class="form-horizontal" pjax-container >
				<input type="hidden" name="_method" value="POST">
				<div class="box-body">

					<div class="fields-group">

						<div class="row">
							<div class='box-header col-md-2'><h4 class='box-title'>Message Type</h4></div>
							<div class="col-md-10">
								<label class="radio-inline">
									<input type="radio" name="messagetype" value="general" class="minimal messagetype" checked class="inline" />&nbsp;General&nbsp;&nbsp;
								</label>
								<label class="radio-inline">
									<input type="radio" name="messagetype" value="taxtype" class="minimal messagetype"  class="inline" />&nbsp;Tax Basis&nbsp;&nbsp;
								</label>
								<div class="btn-group pull-right" style="margin-right: 5px">
									<a data-toggle="modal" data-target="#bulkSmsVariables" href="#" class="btn btn-sm btn-info sms-variables" title="SMS Variables"><i class="fa fa-list"></i><span class="hidden-xs">&nbsp;SMS Variables</span></a>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class=" ">

									<label for="user_type" class=" control-label">Client Type</label>

									<div class="">

										<select class="form-control user_type reloadclient" id="user_type" style="width: 100%;" name="user_type" data-value="" >
											<option value="1" >Individual</option>
											<option value="2" >Company</option>
										</select>


									</div>
								</div>

							</div>
							<div class="col-md-6" id="genderwrapper">
								<div class=" ">

									<label for="gender" class=" control-label">Gender</label>

									<div class="">
										<select class="form-control gender reloadclient" style="width: 100%;" name="gender" data-value="" >
											<option value="M" >Male</option>
											<option value="F" >Female</option>
										</select>
									</div>
								</div>

							</div>
							<div class="col-md-6">
								<div class=" ">

									<label for="certificate_printed" class=" control-label">Certificate Printed</label>

									<div class="">
										<select class="form-control certificate_printed reloadclient" style="width: 100%;" name="certificate_printed" data-value="" >
											<option value=""></option>
											<option value="1" >Yes</option>
											<option value="0" selected>No</option>
										</select>

									</div>
								</div>

							</div>
							<div class="col-md-6">
								<div class=" ">

									<label for="zipcode" class=" control-label">Zip Code</label>

									<div class="">


										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
											<input maxlength="8" type="text" id="zipcode" name="zipcode" value="" class="form-control zipcode reloadclient" placeholder="Input Zipcode" />
										</div>


									</div>
								</div>
							</div>
						</div>
						<div class="row" id="taxinfowrapper" style="display: none;">
							<div class="col-md-12">
								<div class="form-group">
									<label  class=" control-label"></label>
									<div class="">
										<div class='box-header with-border'><h3 class='box-title text-upper box-header'>TAX INFORMATION</h3></div>
									</div>
								</div>
							</div>
							
							<div class="col-md-12">
								<div class="">

									<label for="taxcategory" class=" pull-left control-label">TAX Category</label>

									<div class="col-md-4">
										<select class="form-control taxcategory reloadclient" style="width: 100%;" name="taxcategory" data-value="" >
											<option value=""></option>
											<option value="Returns" >Returns</option>
											<option value="Motor Vehicle">Motor vehicle</option>
											<option value="Driving Licence">Driving Licence</option>
										</select>


									</div>
								</div>
							</div>


							<div class="col-md-6">
								<div class=" ">

									<label for="exempt" class=" control-label">Exempt</label>

									<div class="">
										<select class="form-control exempt reloadclient" style="width: 100%;" name="exempt" data-value="" >
											<option value=""></option>
											<option value="1" >Yes</option>
											<option value="0" selected>No</option>
										</select>


									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class=" ">

									<label for="tax_type" class=" control-label">Tax Type</label>

									<div class="">
										<select class="form-control tax_type reloadclient" style="width: 100%;" name="tax_type" data-value="" >
											<option value=""></option>
											<option value="VAT" >VAT</option>
											<option value="non-VAT" >non-VAT</option>
										</select>
									</div>
								</div>

							</div>
							<div class="col-md-6">
								<div class=" ">

									<label for="filling_type" class=" control-label">Filling Type</label>

									<div class="">
										<select class="form-control filling_type reloadclient" style="width: 100%;" name="filling_type" data-value="" >
											<option value=""></option>
											<option value="regular" >Regular</option>
											<option value="lamp-sum" >Lamp sum</option>
										</select>


									</div>
								</div>

							</div>
							<div class="col-md-6">
								<div class=" ">

									<label for="filling_period" class=" control-label">Filling Period</label>

									<div class="">
										<select class="form-control filling_period reloadclient" style="width: 100%;" name="filling_period" data-value="" >
											<option value=""></option>
											<option value="annual" >Annual</option>
											<option value="quarterly" >Quarterly</option>
										</select>


									</div>
								</div>

							</div>
							<div class="col-md-6">
								<div class=" ">

									<label for="filling_currency" class=" control-label">Filling Currency</label>

									<div class="">
										<select class="form-control filling_currency reloadclient" style="width: 100%;" name="filling_currency" data-value="" >
											<option value=""></option>
											<option value="TSH" >TSH</option>
											<option value="USD" >USD</option>
										</select>
									</div>
								</div>

							</div>
							<div class="col-md-6">
								<div class="duedate">
									<label for="due_date" class=" col-md-3 control-label">Due (Expiration)</label>
									<div class="col-md-3">
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
											<input style="width: 110px" type="text" id="due_from" name="due_from" value="" class="form-control due_from reloadclient" placeholder=" Due From" />
										</div>
									</div>
									<div class="col-md-3">
										<div class="input-group ">
											<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
											<input style="width: 110px" type="text" id="due_to" name="due_to" value="" class="form-control due_to reloadclient" placeholder=" Due To" />
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class=" ">

									<label for="clients" class=" control-label">Clients</label>

									<div class="">
										<select class="form-control clients" id="clients" style="width: 100%;" name="clients[]" multiple="multiple" data-placeholder="Input Clients" size="5" data-value="" >
										</select>
									</div>
									<span id="clientcount"></span>
								</div>

							</div>
							<div class="col-md-6">
								<div class=" ">
									<label for="clients" class=" control-label">Message Body</label>
									<div class="">
										<textarea class="form-control message" id="message" style="width: 100%;" name="message" data-placeholder="Message Body" rows="4">{{Session::get('message')}}</textarea>
										<span id="charleft"></span>
									</div>
								</div>
							</div>
							
						</div>
					</div>

				</div>
				<!-- /.box-body -->

				<div class="box-footer">

					{!! csrf_field() !!}

					<div class="col-md-2">
					</div>

					<div class="col-md-8">

						<div class="btn-group pull-right">
							<button type="submit" class="btn btn-primary">Submit</button>
						</div>

						<div class="btn-group pull-left">
							<button type="reset" class="btn btn-warning">Reset</button>
						</div>
					</div>
				</div>


				<!-- /.box-footer -->
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="bulkSmsVariables" data-controls-modal="bulkSmsVariables" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="bulkSmsVariables">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button id="cancelSmallBtn" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myActionTitle">SMS Variables</h4>
			</div>
			<div class="modal-body">
				<p class="h4">{{$sms_variables}}</p>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	function loadclients(){

		$.ajax({
			url: '/admin/sms/loadclients',
			dataType: 'json',
			type: 'GET',
			data: $("#sendbulkform").serialize(),
			success: function(response) {
				var clients = response.data;
				$("#clientcount").html("This message will be send to "+response.count+" users");
				if(response.status=='success'){
					$("#clients").find('option').remove().end();
				}
				if (clients != '')
              {	//reset clients
              	$.each(clients,function(i,v){
              		$("#clients").append("<option value="+v['id']+">"+v['name']+"</option>");
              	});                       

              }
          }


      });
	}
	
	$(document).on("keyup focus", "#message",function(src) {
		var chars = this.value.length;
		var s = (chars>1) ? "s" : "";
		$("#charleft").html( chars +" character" + s + ".");
	});
	$(document).ready(function(){

		$("#user_type").change(function(){
			if($(this).val()==2){
				$("#genderwrapper").hide();
			}else{
				$("#genderwrapper").show();
			}
		});
		$(".messagetype").change(function(){
			if($(this).val()=='taxtype'){
				$("#taxinfowrapper").show();
			}else{
				$("#taxinfowrapper").hide();
			}
		});
		$(".reloadclient").change(function(){
			loadclients();
		});
		/**load clients**/
		loadclients();
	});
</script>
<script type="text/javascript">
	$(function () {
		$('#due_from').datetimepicker({format: 'L'});
		$('#due_to').datetimepicker({
			format: 'L',
            useCurrent: false //Important! See issue #1075
        });
		$("#due_from").on("dp.change", function (e) {
			$('#due_to').data("DateTimePicker").minDate(e.date);
			loadclients();
		});
		$("#due_to").on("dp.change", function (e) {
			$('#due_from').data("DateTimePicker").maxDate(e.date);
			loadclients();
		});
	});
</script>