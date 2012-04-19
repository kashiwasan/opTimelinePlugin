<script type="text/javascript">
//<![CDATA[
var gorgon = {
      'mode': 'community',
      'communityId': <?php echo $community->getId(); ?>,
      'limit': '20',
      'orderBy': 'asc',
      'post': {
        'foreign': 'community',
        'foreignId': '<?php echo $community->getId(); ?>',
      },
      'notify': {
        'lib': '<?php echo url_for('@homepage', array('absolute' => true)); ?>opTimelinePlugin/js/jquery.desktopify.js',
        'title': '<?php echo $community->getName();?> の最新投稿',
        <?php if ($community->getImageFileName()): ?>
        'icon': '<?php echo sf_image_path($community->getImageFileName(), array('size' => '48x48',)); ?>',
        <?php endif; ?>
      },
      'timer': '5000',
    };
//]]>
</script>
<?php use_javascript('/opTimelinePlugin/js/jquery.desktopify.js'); ?>
<?php use_javascript('/opTimelinePlugin/js/gorgon-gadget.js'); ?>

<script id="timelineTemplate" type="text/x-jquery-tmpl">
<div class="x-chatItem {{if is_self==false}}incomingItem x-incomingItem{{else}}outgoingItem x-outgoingItem{{/if}}">
  <table width="100%">
    <tbody>
      <tr>
        <td valign="top">
          <img src="${memberImage}" alt="${memberScreenName}" title="${memberScreenName}" class="x-avatar">
            <div class="x-myBubble">
              <div class="x-indicator"></div>
              <table class="x-tableBubble" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td class="x-tl"></td>
                    <td class="x-tr"></td>
                  </tr>
                  <tr>
                    <td class="x-message">
                      {{html body_html}}
                      <div class="x-timeStamp">${memberScreenName} @ ${createdAt}</div>
                    </td>
                    <td class="x-messageRight"></td>
                  </tr>
                  <tr>
                    <td class="x-bl"></td>
                    <td class="x-br"></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
</script>

<div class="partsHeading"><h3><?php echo $community->getName();?> のタイムライン</h3></div>
<a href="<?php echo url_for('@homepage', array('absolute' => true)); ?>/member/config?category=timelineScreenName">■スクリーンネーム設定画面</a><br />

<button id="timeline-desktopify" class="btn success">このコミュニティ内の更新通知を許可する</button><br />

<div id="timeline-list" style="height: 350px; overflow-y: scroll; margin-bottom: 20px;" data-post-csrftoken="<?php echo $token; ?>" data-post-baseurl="<?php echo url_for('@homepage', array('absolute' => true)); ?>" data-last-id="" data-loadmore-id="">
<div id="loadmore-loading" style="text-align: center;"><?php echo op_image_tag('ajax-loader.gif', array()) ?></div>
</div>
<div style="height: 50px;">
<textarea class="gorgon-textarea" id="gorgon-textarea-body" style="width: 590px;"></textarea>
<button class="gorgon-button button" id="gorgon-submit" data-post-csrftoken="<?php echo $token; ?>" data-post-baseurl="<?php echo url_for('@homepage', array('absolute' => true)); ?>">投稿</button>
</div>
