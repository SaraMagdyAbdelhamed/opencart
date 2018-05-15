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
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-push" accept-charset="utf-8" class="form-horizontal">
                    <div class="form-group" style="display: <?= $id!=''?'none':'block' ?>">
                        <label class="col-sm-2 control-label"><?php echo $entry_send_to; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <label class="radio-inline">
                                    <input type="radio" name="Device_Type" value="all" class="form-control" />كل الأجهزة
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="Device_Type" value="ios" class="form-control" />أيفون
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="Device_Type" value="android" class="form-control" />أندرويد
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_send_time; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" id="Send_Time" name="Send_Time" value="<?php echo isset($push_info['Send_Time'])? date('m/d/Y h:i', $push_info['Send_Time']) : ''; ?>" placeholder="<?php echo $entry_send_time; ?>" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group" style="display: <?= $id!=''?'none':'block' ?>">
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
                                    $selected = isset($push_info['Module_ID'])&&$push_info['Module_ID']==$k?"selected":"";
                                    ?>
                                    <option value="<?= $k ?>" <?= $selected ?>><?= $v ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" style="display: <?= $id!=''?'none':'block' ?>">
                        <label class="col-sm-2 control-label"><?php echo $entry_action; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <select id="Action_ID" name="Action_ID" style="" class="form-control">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group action6" style="display: none;">
                        <label class="col-sm-2 control-label"><?php echo $entry_content; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <select id="Content" name="Content" style="" class="form-control">
                                    <option value=""></option>
                                    <option value="lastone">اخر مادة</option>
                                    <option value="random">اختيار مادة بشكل عشوائي</option>
                                    <option value="viewone">عرض احد المواد</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group actionSelect" style="display: none;">
                        <label class="col-sm-2 control-label"><?php echo $entry_selectvar; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <select id="Selectvarid" name="Selectvarid" style="" class="form-control">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <h4><strong><?= $entry_options ?></strong></h4>
                    <hr/>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_sound; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input id="sound" name="Sound" type="text" style="" value="<?php echo isset($push_info['Sound'])? $push_info['Sound'] : ''; ?>"
                                       size="" class="form-control" placeholder="<?php echo $entry_sound; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_picture; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <select class="form-control" name="Picture" style="" id="Picture">
                                    <option value="icon 1" <?= isset($push_info['Sound']) && $push_info['Sound']=="icon 1"?"selected":"" ?> >icon1</option>
                                    <option value="icon 2" <?= isset($push_info['Sound']) && $push_info['Sound']=="icon 2"?"selected":"" ?>>icon2</option>
                                    <option value="icon 3" <?= isset($push_info['Sound']) && $push_info['Sound']=="icon 3"?"selected":"" ?>>icon3</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_message; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <textarea id="Message" name="Message" style=""
                                          size="50" class="form-control" placeholder="<?php echo $entry_message; ?>"><?= isset($push_info['Message'])?$push_info['Message']:"" ?></textarea>
                            </div>
                            <?php if (isset($error_message) && $error_message != "") { ?>
                            <div class="text-danger"><?php echo $error_message; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="<?= $base_url ?>view/stylesheet/appcreator/jquery-ui-timepicker-addon">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="<?= $base_url ?>view/javascript/appcreator/jquery-ui-timepicker-addon.js"></script>
    <script src="<?= $base_url ?>view/javascript/appcreator/pushmessages.js"></script>
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
        $("#Module_ID").change(function () {
            var v = $(this).val();
            $.post("<?= $base_url ?>index.php?route=appcreator/ads/getActions&token=<?= $token ?>", {data: v}, function (res) {
                $("#Action_ID").html(res);
            });
        });
        $("#Send_Time").datetimepicker();
        //--></script></div>
<?php echo $footer; ?> 