<script type="text/javascript">
//<![CDATA[
var gorgon = {
      'limit': '20',
      'post': {

      },
      'timer': '60000',
    };
//]]>
</script>


<?php use_javascript('/opTimelinePlugin/js/timeline-loader.js') ?>
<?php use_stylesheet('/opTimelinePlugin/css/bootstrap.css', 'last') ?>
<?php use_stylesheet('/opTimelinePlugin/css/timeline.css', 'last') ?>

    <script type="text/javascript">
    $(function(){
      $("#timeline-textarea").focus(function(){
        $('.timeline-postform').css('padding-bottom', '30px');
        $('#timeline-textarea').attr('rows', '3');
        $('#timeline-submit-area').css('display', 'inline');
      });
    });
    </script>

<script id="timelineTemplate" type="text/x-jquery-tmpl">
        <div class="timeline-post">
          <a name="timeline-${id}"></a>
          <div class="timeline-post-member-image">
            <img src="${memberImage}" alt="${memberScreenName}" />
          </div>
          <div class="timeline-post-content">
            <div class="timeline-member-name">
              <a class="gorgon-screenname" href="<?php echo url_for('@homepage', array('absolute' => true)); ?>/member/${memberId}">${memberScreenName}</a>
            </div>
            <div class="timeline-post-body">
              {{html body}}
            </div>
          </div>
          <div class="timeline-post-control">
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

<div class="partsHeading"><h3>SNSメンバー全員のタイムライン</h3></div>

    <div class="timeline">
      <div class="timeline-postform well">
        <textarea id="timeline-textarea" class="input-xlarge" rows="1" placeholder="今何してる？"></textarea>
        <div id="timeline-submit-loader"><?php echo op_image_tag('ajax-loader.gif', array()) ?></div>
        <div id="timeline-submit-area">
          <button id="timeline-submit-button" class="btn btn-primary timeline-submit" data-post-csrftoken="<?php echo $token; ?>" data-post-baseurl="<?php echo url_for('@homepage', array('absolute' => true)); ?>">投稿</button>
        </div>
      </div>

      <div id="timeline-loading" style="text-align: center;"><?php echo op_image_tag('ajax-loader.gif', array()) ?></div>
      <div id="timeline-list" data-post-csrftoken="<?php echo $token; ?>" data-post-baseurl="<?php echo url_for('@homepage', array('absolute' => true)); ?>" data-last-id=""data-loadmore-id="">

      </div>
      <button class="btn btn-small" id="timeline-loadmore" style="width: 440px;">もっと読む</button>
    </div>

