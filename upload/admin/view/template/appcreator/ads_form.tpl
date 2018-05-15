<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-ads" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
            </div>
            <div class="panel-body">
                <h4><strong><?= $entry_basic ?></strong></h4>
                <hr/>
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-ads" accept-charset="utf-8" class="form-horizontal">
                    <div class="form-group required">
                        <label class="col-sm-2 control-label"><?php echo $entry_title; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" name="Title" value="<?php echo isset($ads_info['Title']) ? $ads_info['Title'] : ''; ?>" placeholder="<?php echo $entry_title; ?>" class="form-control" />
                            </div>
                            <?php if (isset($error_title) && !empty($error_title)) { ?>
                            <div class="text-danger"><?php echo $error_title; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_module; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                
                                <select id="Module_ID" name="Module_ID" style="" class="form-control">
                                    <option value=""></option>
                                    <?php
                                    $modules = array(
                                    1 => "الاخبار",
                                    3 => "الفيديو",
                                    4 => "الصور",
                                    6 => "الصفحات",
                                    5 => "المنتجات",
                                    47 => "الاستفتاء"
                                    );
                                    foreach ($modules as $k => $v) {
                                    $selected = isset($ads_info['Module_ID'])&&$ads_info['Module_ID']==$k?"selected":"";
                                    ?>
                                    <option value="<?= $k ?>" <?= $selected ?>><?= $v ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_action; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <select id="Action_ID" name="Action_ID[<?php echo $language['language_id']; ?>][name]" style="" class="form-control">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_img; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="" /></a><input type="hidden" name="Img" value="<?= isset($ads_info['Img'])?$ads_info['Img']:'' ?>" id="input-image" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_link; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" name="Link[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($ads_info['Link'])? $ads_info['Link'] : ''; ?>" placeholder="<?php echo $entry_link; ?>" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_startdate; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                
                                <input type="text" id="StartDate" name="StartDate" value="<?php echo isset($ads_info['StartDate'])? $ads_info['StartDate'] : ''; ?>" placeholder="<?php echo $entry_startdate; ?>" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_enddate; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                
                                <input type="text" id="EndDate" name="EndDate" value="<?php echo isset($ads_info['EndDate'])? $ads_info['EndDate'] : ''; ?>" placeholder="<?php echo $entry_enddate; ?>" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_active; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <label class="radio-inline">
                                    <input type="checkbox" value="1" <?= isset($ads_info['Active'])&&$ads_info['Active']==1?'checked':'' ?> name="Active">
                                </label>
                            </div>
                        </div>
                    </div>
                    <h4><strong><?= $entry_options ?></strong></h4>
                    <hr/>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_ad_type; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <select class="form-control" name="Ads_Type" style="" id="type_1">
                                    <option value="4" <?= isset($other_data->Type) && $other_data->Type==4?'selected':'' ?>>اعلان ثابت</option>
                                    <option value="1" <?= isset($other_data->Type) && $other_data->Type==1?'selected':'' ?>>اعلان جوجل ادسنس</option>
                                    <option value="2" <?= isset($other_data->Type) && $other_data->Type==2?'selected':'' ?>>اعلان متقلب</option>
                                    <option value="3" <?= isset($other_data->Type) && $other_data->Type==3?'selected':'' ?>>اعلان متحرك</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="Ads_Provider_Wrapper">
                        <label class="col-sm-2 control-label"><?php echo $entry_ad_provider; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <select class="form-control" name="Ads_Provider" style="" id="">
                                    <option value=""></option>
                                    <option value="MoPub" <?= isset($other_data->Ads_Provider) && $other_data->Ads_Provider=='MoPub'?'selected':'' ?>>MoPub</option>
                                    <option value="Flurry" <?= isset($other_data->Ads_Provider) && $other_data->Ads_Provider=='Flurry'?'selected':'' ?>>Flurry</option>
                                    <option value="DoubleClickForPublishers" <?= isset($other_data->Ads_Provider) && $other_data->Ads_Provider=='DoubleClickForPublishers'?'selected':'' ?>>DoubleClickForPublishers</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="IOS_Publisher_ID_Wrapper">
                        <label class="col-sm-2 control-label"><?php echo $entry_ios_publisher; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" id="" name="IOS_Publisher_ID" value="<?= isset($other_data->IOS_Publisher_ID)?$other_data->IOS_Publisher_ID:'' ?>" placeholder="" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="Android_Publisher_ID_Wrapper">
                        <label class="col-sm-2 control-label"><?php echo $entry_android_publisher; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" id="" name="Android_Publisher_ID" value="<?= isset($other_data->Android_Publisher_ID)?$other_data->Android_Publisher_ID:'' ?>" placeholder="" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_ad_pos; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <select class="form-control" name="Ads_Position" style="" id="">
                                    <option value="top" <?= isset($other_data->Position)&&$other_data->Position=='top'?'selected':'' ?>>اعلى التطبيق</option>
                                    <option value="bottom" <?= isset($other_data->Position)&&$other_data->Position=='bottom'?'selected':'' ?>>اسفل التطبيق</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script type="text/javascript"><!--
        var v = $("#Module_ID").val();
            $.get("<?= $base_url ?>index.php?route=appcreator/ads/getActions&token=<?= $token ?>&data="+$("#Module_ID").val(), function (res) {
                $("#Action_ID").html(res);
            });
$("#Module_ID").change(function () {
            var v = $(this).val();
            $.get("<?= $base_url ?>index.php?route=appcreator/ads/getActions&token=<?= $token ?>&data="+$(this).val(), function (res) {
                $("#Action_ID").html(res);
            });
        });
        $("#StartDate, #EndDate").datepicker();
        if($("#type_1").val()==1)
       {
           $("#Ads_Provider_Wrapper, #IOS_Publisher_ID_Wrapper, #Android_Publisher_ID_Wrapper").show();
       }else{
           $("#Ads_Provider_Wrapper, #IOS_Publisher_ID_Wrapper, #Android_Publisher_ID_Wrapper").hide();
       }
       if($("#type_1").val()==1)
       {
           $("#Ads_Provider_Wrapper, #IOS_Publisher_ID_Wrapper, #Android_Publisher_ID_Wrapper").show();
       }else{
           $("#Ads_Provider_Wrapper, #IOS_Publisher_ID_Wrapper, #Android_Publisher_ID_Wrapper").hide();
       }
      $("#type_1").change(function(){
       if($(this).val()==1)
       {
           $("#Ads_Provider_Wrapper, #IOS_Publisher_ID_Wrapper, #Android_Publisher_ID_Wrapper").show();
       }else{
           $("#Ads_Provider_Wrapper, #IOS_Publisher_ID_Wrapper, #Android_Publisher_ID_Wrapper").hide();
       }
    });
        //--></script></div>
<?php echo $footer; ?> 