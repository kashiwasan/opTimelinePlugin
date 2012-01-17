<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.templates/beta1/jquery.tmpl.js"></script>
<script type="text/javascript">
$(document).ready(function(){
  $("#postbutton").click(function(){
    $(".postform").toggle();
  });
});
$(document).ready(function(){
  $("#menubutton").click(function(){
    $(".menuform.template").toggle();
  }); 
});
</script>
<div id="tosaka" class="row">
  <div class="span4"><?php echo link_to(op_image_tag('LOGO.png', array('height' => '32')), '@homepage'); ?></div>
  <?php if (opToolkit::isSecurePage()): ?>
  <div class="span4 center"><?php echo op_image_tag('NOTIFY_CENTER.png', array('height' => '32')) ?></div>
  <div class="span3 offset1 center"><a href="#" class="postbutton" id="postbutton"><?php echo op_image_tag('POST.png', array('height' => '32')) ?></a></div>
  <?php endif; ?>
</div>
<div class="postform hide" style="padding-bottom: 100px;">
  <div class="post_margin row" style="height: 12px;">
    <div class="span12">
      <img src="<?php echo url_for('@homepage', array('absolute' => true)); ?>images/POST_MARGIN.png" width="320" alt="" />
    </div>
  </div>
  <div id="face" class="row">
    <div class="span2"><?php echo op_image_tag_sf_image($sf_user->getMember()->getImageFileName(), array('size' => '48x48')) ?></div>
    <div class="span3">
      <img src="<?php echo url_for('@homepage', array('absolute' => true)); ?>images/post_icon.png" alt="" />
    </div>
    <?php if ($this->getModuleName()=='community'): ?>
    <div class="span5">
      <div class="row"><span class="face-name"><?php echo $community->getName() ?></span></div>
      <hr class="toumei">
      <div class="row"><a href="#"></a></div>
    </div>
    <div class="span2"> <?php echo op_image_tag_sf_image($community->getImageFileName(), array('size' => '48x48', 'class' => 'rad4')) ?></div>
    <?php else: ?>
    <div class="span7">
      <div class="row"><span class="face-name"><?php echo $op_config['sns_name'] ?></span></div>
      <hr class="toumei">
    </div>
    <?php endif; ?>
    
  </div>
  <hr class="toumei">
  <div class="row">
    <textarea class="span12" rows="3" id="gorgon-textarea-body"></textarea>
  </div>
<?php $form = new sfForm(); ?>
<?php $csrfToken = $form->getCSRFToken(); ?>
  <div class="row">
    <button class="span12 btn small primary" id="gorgon-submit" data-post-csrftoken="<?php echo $csrfToken; ?>" data-post-baseurl="<?php echo url_for('@homepage', array('absolute' => true)); ?>">POST</button>
  </div>
</div>
