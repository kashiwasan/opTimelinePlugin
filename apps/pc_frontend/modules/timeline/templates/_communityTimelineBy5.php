<div class="partsHeading"><h3><?php echo $community->getName(); ?>のタイムライン</h3></div>
<div class="main-content" style="min-height: 300px;">
  <div class="page-header">
    <div id="timeline-list">
<?php $count = count($activityData); ?>
<?php for ($j = 0; $j < $count; $j++): ?>
<?php $i = 2 - $j; ?>
<div class="x-chatItem outgoingItem x-outgoingItem">
  <table width="100%">
    <tbody>
      <tr>
        <td valign="top">
            <?php echo link_to(op_image_tag_sf_image($activityData[$i]->getMember()->getImageFileName(), array('alt' => sprintf('[%s]', $activityData[$i]->getMember()), 'size' => '48x48', 'class' => 'x-avatar')), '@obj_member_profile?id='.$activityData[$i]->getMemberId()); ?>
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
                      <?php echo $activityData[$i]->getBody(); ?>
                      <div class="x-timeStamp"><?php echo $activityData[$i]->getMember()->getConfig('op_screen_name', $activityData[$i]->getMember()->getName()); ?> @ <?php echo $activityData[$i]->getCreatedAt(); ?></div>
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
<?php endfor; ?>
    </div>
  </div>
  <div style="float: right; margin-top: 15px;">
  <li><?php echo link_to('もっと読む', '@community_timeline?id='.$community->getId()); ?></li>
  </div>
</div>
