<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-push').submit() : false;"><i class="fa fa-trash-o"></i></button>
      </div>
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
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-push">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'Message') { ?>
                    <a href="<?php echo $sort_message; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_message; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_message; ?>"><?php echo $column_message; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'Type') { ?>
                    <a href="<?php echo $sort_type; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_type; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_type; ?>"><?php echo $column_type; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'Num_Msgs') { ?>
                    <a href="<?php echo $sort_num_msgs; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_num_msgs; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_num_msgs; ?>"><?php echo $column_num_msgs; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'Module_ID') { ?>
                    <a href="<?php echo $sort_module; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_module; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_module; ?>"><?php echo $column_module; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'Send_Time') { ?>
                    <a href="<?php echo $sort_send_time; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_send_time; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_send_time; ?>"><?php echo $column_send_time; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'Finished') { ?>
                    <a href="<?php echo $sort_finished; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_finished; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_finished; ?>"><?php echo $column_finished; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($push) { ?>
                <?php foreach ($push as $ad) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($ad['ID'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $ad['ID']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $ad['ID']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $ad['Message']; ?></td>
                  <td class="text-left"><?php echo $ad['Type']; ?></td>
                  <td class="text-right"><?php echo $ad['Num_Msgs']; ?></td>
                  <td class="text-right"><?php echo $ad['Module_ID']; ?></td>
                  <td class="text-right"><?php echo date("d F Y h:i a", $ad['Send_Time']); ?></td>
                  <td class="text-right"><?php echo !empty($ad['Finished']) && !is_numeric($ad['Finished'])?date("d F Y h:i a", $ad['Finished']):"لم ينتهى بعد"; ?></td>
                  <td class="text-right"><a href="<?php echo $ad['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="4"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>