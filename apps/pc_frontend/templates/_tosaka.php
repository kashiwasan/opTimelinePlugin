<?php use_helper('Javascript'); ?>
<?php use_javascript('jquery.tmpl.js'); ?>
<?php use_javascript('smt_notify.js'); ?>
<?php use_javascript('smt_util.js'); ?>
<?php include_javascripts() ?>
<!-- NCFORM TMPL -->
<div class="ncform hide toggle1">
  <hr class="toumei">
  <div class="row">
    <div class="span10 offset1 center white font14 toggle1_close">
      通知センター
      <hr class="toumei">
    </div>
    <div class="span1">
      <?php echo op_image_tag('UPARROW', array('class' => 'toggle1_close')) ?>
    </div>
  </div>
  <div id="pushList" class="hide">
  </div>
  <div id="pushLoading" class="center"><?php echo op_image_tag('ajax-loader.gif', array()) ?></div>
</div>
<!-- NCFORM TMPL -->

<script id="pushListTemplate" type="text/x-jquery-tmpl">
    <div class="row push" data-location-url="${url}" data-member-id="${member_id_from}">
      <div class="{{if category=="message" || category=="other"}}divlink {{/if}}row" data-location-url="${url}" data-member-id="${member_id_from}">
      <hr class="toumei">
      <div class="span3">
        <img style="margin-left: 5px;" src="${icon_url}" class="rad4" width="48" height="48">
      </div>
      <div class="span9" style="margin-left: -13px;">
      {{if category=="friend"}}
        <div class="row">
        {{html body}}
        </div>
        <div class="row">
            <button class="span2 btn primary small">YES</button>
            <button class="span2 btn small">NO</button>
        </div>
      {{/if}}
      {{if category=="message"}}
        <div class="link_message">
        {{html body}}
        </div>
      {{/if}}
      {{if category=="other"}}
        <div class="link_other">
        {{html body}}
        </div>
      {{/if}}
      </div>
      <hr class="toumei">
    </div>
    </div>
    <hr class="gray">
</script>
<script id="pushCountTemplate" type="text/x-jquery-tmpl">
  {{if message.unread!=='0'}}
  <span class="nc_icon1 label important" id="nc_count1">${message.unread}</span>
  {{/if}}
  {{if link.unread!=='0'}}
  <span class="nc_icon2 label important" id="nc_count2">${link.unread}</span>
  {{/if}}
  {{if other.unread!=='0'}}
  <span class="nc_icon3 label important" id="nc_count3">${other.unread}</span>
  {{/if}}
</script>


<?php include_partial('default/smtMenu') ?>

<!-- POSTFORM TMPL -->
<div class="postform hide toggle1">
  <hr class="toumei">
  <div class="row">
    <div class="span10 offset1 center white font14 toggle1_close">
      投稿フォーム
      <hr class="toumei">
    </div>
    <div class="span1">
      <?php echo op_image_tag('UPARROW', array('class' => 'toggle1_close')) ?>
    </div>
  </div>
  <div class="row">
    <textarea class="posttextarea span12" rows="4" id="gorgon-textarea-body"></textarea>
  </div>
  <hr class="toumei">
  <div class="row">
    <?php $form = new sfForm(); ?>
    <?php $csrfToken = $form->getCSRFToken(); ?>
    <button class="span10 offset1 btn small primary" id="gorgon-submit" data-post-csrftoken="<?php echo $csrfToken; ?>" data-post-baseurl="<?php echo url_for('@homepage', array('absolute' => true)); ?>">POST</button>
  </div>
  <hr class="toumei">
  <hr class="toumei">
  <hr class="toumei">
  <hr class="toumei">
  <hr class="toumei">
  <hr class="toumei">
  <hr class="toumei">
  <hr class="toumei">
  <hr class="toumei">
  <hr class="toumei">
  <hr class="toumei">
  <hr class="toumei">
  <hr class="toumei">
  <?php echo op_image_tag('SEPALATOR.png', array('height' => '6', 'width' => '320')) ?>
</div>
<!-- POSTFORM TMPL -->

<div id="slot_tosaka">
  <div class="row">
    <div class="span12">
      <div class="row">
        <div class="span4"><?php echo op_image_tag('LOGO.png', array('height' => '32', 'class' => 'menubutton')); ?></div>
        <div id="notification_center" class="span4 center"><?php echo op_image_tag('NOTIFY_CENTER.png', array('height' => '32', 'class' => 'ncbutton')) ?>
        </div>
        <div class="span3 offset1 center"><?php echo op_image_tag('POST.png', array('height' => '32', 'class' =>'postbutton')) ?></div>
      </div>
    </div>
  </div>
</div>
