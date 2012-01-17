<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.templates/beta1/jquery.tmpl.js"></script>
<script type="text/javascript">
//-----------------------------------
//COMMENT BUTTON NAVIGATION
$(document).ready(function(){
  $(".commentbutton").click(function(){
    $(".commentform").toggle();
    $(".commentbutton").toggle();
  });
});

//-----------------------------------
//TOSAKA BUTTON NAVIGATION
$(document).ready(function(){
  $(".postbutton").click(function(){
    $(".toggle1:not(.postform)").hide();
    $(".postform").toggle();
    if($(".postform").is(":visible")){
      $(".posttextarea").focus();
    }
  });

  $(".ncbutton").click(function(){
    $(".toggle1:not(.ncform)").hide();
    $(".ncform").toggle();
  });

  $(".menubutton").click(function(){
    $(".toggle1:not(.menuform)").hide();
    $(".menuform").toggle();
  });

  $(".toggle1_close").click(function(){
    $(".toggle1").hide();
  });
});
</script>


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
  <div class="row">
  <hr class="toumei">
  <hr class="toumei">
    <div class="span12 center">
      <img src="http://p.pne.jp/d/201201162250.png" alt="" />
    </div>
  </div>
  <hr class="toumei">
  <hr class="toumei">
  <?php echo op_image_tag('SEPALATOR.png', array('height' => '6', 'width' => '320')) ?>
</div>
<!-- NCFORM TMPL -->


<!-- MENUFORM TMPL -->
<div class="menuform hide toggle1">
  <hr class="toumei">
  <div class="row">
    <div class="span10 offset1 center white font14 toggle1_close">
      MENU
      <hr class="toumei">
    </div>
    <div class="span1">
      <img class="toggle1_close" src="./UPARROW.png">
      <?php echo op_image_tag('UPARROW', array('class' => 'toggle1_close')) ?>
    </div>
  </div>


  <hr class="toumei">

  <div class="menu-middle row">
    <div class="span11 offset1">
      <a class="btn">Home</a>
      <a class="btn">Diary</a>
      <a class="btn">Community</a>
      <a class="btn">Community</a>
      <a class="btn">Logout</a>
      <a class="btn">Home</a>
      <a class="btn">Diary</a>
      <a class="btn">Logout</a>
      <a class="btn">Home</a>
      <a class="btn">Diary</a>
      <a class="btn">Community</a>
      <a class="btn">Logout</a>
    </div>
  </div>
  <hr class="toumei">
  <hr class="toumei">
  <?php echo op_image_tag('SEPALATOR.png', array('height' => '6', 'width' => '320')) ?>
</div>
<!-- MENUFORM TMPL -->


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
        <div class="span4"><?php echo link_to(op_image_tag('LOGO.png', array('height' => '32', )), '@homepage'); ?></div>
        <?php if (opToolkit::isSecurePage()): ?>
        <div class="span4 center"><?php echo op_image_tag('NOTIFY_CENTER.png', array('height' => '32', 'class' => 'ncbutton')) ?></div>
        <div class="span3 offset1 center"><?php echo op_image_tag('POST.png', array('height' => '32', 'class' => 'postbutton')) ?></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
