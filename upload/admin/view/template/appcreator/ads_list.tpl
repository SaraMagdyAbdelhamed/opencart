<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-ads').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-ads">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'Title') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_title; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $column_title; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'Active') { ?>
                    <a href="<?php echo $sort_active; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_active; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_active; ?>"><?php echo $column_active; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'Count') { ?>
                    <a href="<?php echo $sort_count; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_count; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_count; ?>"><?php echo $column_count; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'Link') { ?>
                    <a href="<?php echo $sort_link; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_link; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_link; ?>"><?php echo $column_link; ?></a>
                    <?php } ?></td>
                  <td class="text-left">
                    <?php echo $column_img; ?>
                  </td>
                  <td class="text-left"><?php if ($sort == 'Module_ID') { ?>
                    <a href="<?php echo $sort_module; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_module; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_module; ?>"><?php echo $column_module; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'StartDate') { ?>
                    <a href="<?php echo $sort_startdate; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_startdate; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_startdate; ?>"><?php echo $column_startdate; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'EndDate') { ?>
                    <a href="<?php echo $sort_enddate; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_startdate; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_enddate; ?>"><?php echo $column_startdate; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($ads) { ?>
                <?php foreach ($ads as $ad) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($ad['ID'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $ad['ID']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $ad['ID']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $ad['Title']; ?></td>
                  <td class="text-left"><?php echo $ad['Active']=="1"?"&#10004;":""; ?></td>
                  <td class="text-right"><?php echo $ad['Count']; ?></td>
                  <td class="text-right"><a href="<?php echo $ad['Link']; ?>"><?php echo $ad['Link']; ?></a></td>
                  <td class="text-right"><img src="<?php echo $base_url . 'image/'. $ad['Img']; ?>" width="50" height="50"/></td>
                  <td class="text-right"><?php echo $ad['Module_ID']; ?></td>
                  <td class="text-right"><?php echo $ad['StartDate']; ?></td>
                  <td class="text-right"><?php echo $ad['EndDate']; ?></td>
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