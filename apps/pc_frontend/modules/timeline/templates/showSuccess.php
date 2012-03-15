<?php use_helper('opUtil', 'Javascript') ?>
<script type="text/javascript">
//<![CDATA[
var gorgon = {
      'mode': 'one',
      'activityId': <?php echo $activity->getId() ?>,
      'count': 1,
    };
//]]>
</script>
<?php use_javascript('jquery.min.js') ?>
<?php use_javascript('jquery.tmpl.min.js') ?>
<?php use_javascript('/opTimelinePlugin/js/jquery.timeline.js') ?>
<?php use_javascript('/opTimelinePlugin/js/timeline-loader.js') ?>
<?php use_stylesheet('/opTimelinePlugin/css/bootstrap.css', 'last') ?>
<?php use_stylesheet('/opTimelinePlugin/css/timeline.css', 'last') ?>

<script id="timelineTemplate" type="text/x-jquery-tmpl">
        <div class="timeline-post-large">
          <a name="timeline-${id}"></a>
          <div class="timeline-post-member-image">
            <img src="${memberImage}" alt="${memberScreenName}" />
          </div>
          <div class="timeline-post-content-large">
            <div class="timeline-member-name">
              <a class="gorgon-screenname" href="<?php echo url_for('@homepage', array('absolute' => true)); ?>/member/${memberId}">${memberScreenName}</a>
            </div>
            <div class="timeline-post-body-large">
              {{html body}}
            </div>
          </div>
          <div class="timeline-post-control-large">
          <a class="timeline-comment-link">コメントする</a> | {{if deleteLink=="inline"}}<a href="#" rel="timelineDelete" class="timestamp" location-url="<?php echo url_for('@homepage', array('absolute' => true)); ?>" data-activity-id="${id}" data-activity-body="${convertTag(body)}" data-activity-memberScreenName="${memberScreenName}" data-activity-csrftoken="<?php echo $token; ?>">削除する</a> | {{/if}} ${createdAt}
          </div>

          <div class="timeline-post-comments" id="commentlist-${id}">

            <div id="timeline-post-comment-form-${id}" class="timeline-post-comment-form">
            <input class="timeline-post-comment-form-input" data-timeline-id="${id}" data-post-csrftoken="<?php echo $token; ?>" id="comment-textarea-${id}" type="text" />
            <button data-timeline-id="${id}" data-post-csrftoken="<?php echo $token; ?>" data-post-baseurl="<?php echo url_for('@homepage', array('absolute' => true)); ?>" class="btn btn-primary btn-mini timeline-comment-button">投稿</button>
            </div>
            <div id="timeline-post-comment-form-loader-${id}" class="timeline-post-comment-form-loader">
            <?php echo op_image_tag('ajax-loader.gif', array()) ?>
            </div>
          </div>
        </div>
</script>

<script id="timelineCommentTemplate" type="text/x-jquery-tmpl">
            <div class="timeline-post-comment">

              <div class="timeline-post-comment-member-image">
                <img src="${memberImage}" alt="" width="36" />
              </div>
              <div class="timeline-post-comment-content">
                <div class="timeline-post-comment-name-and-body">
                <a href="#">${memberScreenName}</a>
                <span class="timeline-post-comment-body">
                {{html body}}
                </span>
                </div>
              </div>
              <div class="timeline-post-comment-control">
              {{if deleteLink=="inline"}}<a href="#" rel="timelineDelete" class="timestamp" location-url="<?php echo url_for('@homepage', array('absolute' => true)); ?>" data-activity-id="${id}" data-activity-body="${convertTag(body)}" data-activity-memberScreenName="${memberScreenName}" data-activity-csrftoken="<?php echo $token; ?>">削除する</a> {{/if}}
              </div>
            </div>
</script>

<div class="partsHeading"><h3><?php echo $activity->getMember()->getName(); ?>さんのタイムライン</h3></div>

    <div class="timeline-large">
      <button id="timeline-submit-button" class="hide" data-post-csrftoken="<?php echo $token; ?>" data-post-baseurl="<?php echo url_for('@homepage', array('absolute' => true)); ?>"></button>
      <div id="timeline-loading" style="text-align: center;"><?php echo op_image_tag('ajax-loader.gif', array()) ?></div>
      <div id="timeline-list" data-post-csrftoken="<?php echo $token; ?>" data-post-baseurl="<?php echo url_for('@homepage', array('absolute' => true)); ?>" data-last-id=""data-loadmore-id="">

      </div>
    </div>

