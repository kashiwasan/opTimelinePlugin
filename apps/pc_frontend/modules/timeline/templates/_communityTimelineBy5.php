<div class="partsHeading"><h3><?php echo $community->getName(); ?>のタイムライン</h3></div>
<div class="main-content" style="min-height: 300px;">
  <div class="page-header">
    <div id="timeline-list">
<?php foreach ($activityData as $activity): ?>
<?php if ($activity->getMemberId() === $sf_context->getUser()->getMemberId()): ?>
<div class="x-chatItem outgoingItem x-outgoingItem">
<?php else: ?>
<div class="x-chatItem incomingItem x-incomingItem">
<?php endif; ?>
  <table width="100%">
    <tbody>
      <tr>
        <td valign="top">
            <?php echo link_to(op_image_tag_sf_image($activity->getMember()->getImageFileName(), array('alt' => sprintf('[%s]', $activity->getMember()), 'size' => '48x48', 'class' => 'x-avatar')), '@obj_member_profile?id='.$activity->getMemberId()); ?>
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
                      <?php echo $activity->getBody(); ?>
                      <div class="x-timeStamp"><?php echo $activity->getMember()->getConfig('op_screen_name', $activity->getMember()->getName()); ?> @ <?php echo $activity->getCreatedAt(); ?></div>
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
<?php endforeach; ?>
    </div>
  </div>
  <div style="float: right; margin-top: 15px;">
  <li><?php echo link_to('もっと読む', '@community_timeline?id='.$community->getId()); ?></li>
  </div>
</div>
