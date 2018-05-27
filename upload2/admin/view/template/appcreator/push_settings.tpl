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
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
            </div>
            <div class="panel-body">
                <form action="" method="post" enctype="multipart/form-data" id="form-push" accept-charset="utf-8" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_google_push_api_key; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" id="" name="google_push_api_key" value="<?= $robo_google_push_api_key ?>" placeholder="<?php echo $entry_google_push_api_key; ?>" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_apple_cert_file; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" id="" name="apple_cert_file" value="<?= $robo_apple_cert_file ?>" placeholder="<?php echo $entry_apple_cert_file; ?>" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_apple_pass_phrase; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" id="" name="apple_pass_phrase" value="<?= $robo_apple_pass_phrase ?>" placeholder="<?php echo $entry_apple_pass_phrase; ?>" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_apple_feedback_server; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" id="" name="apple_feedback_server" value="<?= $robo_apple_feedback_server ?>" placeholder="<?php echo $entry_apple_feedback_server; ?>" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_apple_server; ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" id="" name="apple_server" value="<?= $robo_apple_server ?>" placeholder="<?php echo $entry_apple_server; ?>" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Your Cron Url</label>
                        <p>Copy and paste this url and add to cron job</p>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <span style="font-size: 20px;"><?= $base_url ?>?route=appcreator/pushcron/processPushAction</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?> 