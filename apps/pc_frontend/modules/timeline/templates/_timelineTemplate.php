<script id="timelineTemplate" type="text/x-jquery-tmpl">
        <div class="timeline-post">
          <a name="timeline-${id}"></a>
          <div class="timeline-post-member-image">
            <a href="${member.profile_url}" title="${member.name}"><img src="${member.profile_image}" alt="${member.name}" /></a>
          </div>
          <div class="timeline-post-content">
            <div class="timeline-member-name">
              <a class="screen-name" href="${member.profile_url}">${member.screen_name}</a>
            </div>
            <div class="timeline-post-body">
              {{if image_url !== null}}
                <a href="#timeline-post-image-detail-${id}" class="timeline-post-image-detail-link">
                  <img src="${image_url}" alt="timeline-images-${id}" class="timeline-post-image" width="48" height="48" />
                </a>
              {{/if}}
              {{html body_html}}
            </div>
          </div>
          <div class="timeline-post-control">
            <a class="timeline-comment-link">コメントする</a>{{if member.self==true}} | <a href="#timeline-post-delete-confirm-${id}" class="timeline-post-delete-confirm-link">削除する</a>
            {{/if}} | <a href="<?php echo url_for('@homepage', array('absolute' => true)) ?>timeline/show/id/${id}"><span class="timestamp">${created_at}</span></a>

            <!--Nice Plugin -->
            <div class="nice" style="padding-bottom: 5px;">
              <a><span class="nice-list" data-nice-id="${id}">いいね！</span></a>
              <a><span class="nice-cancel" data-nice-id="${id}" style="display: none;">いいね！を取り消す&nbsp;</span></a>
              {{if member.self==false}}<a><span class="nice-post" data-nice-id="${id}" member-id="${member.id}"><i class="icon-thumbs-up"></i>&nbsp;&nbsp;</span></a>{{/if}}
              <div class="nice-list-member" data-nice-id="${id}"></div>
            </div>
          </div>

          <div class="timeline-post-comments" id="commentlist-${id}">

            <div id="timeline-post-comment-form-${id}" class="timeline-post-comment-form">
            <input class="timeline-post-comment-form-input" data-timeline-id="${id}" id="comment-textarea-${id}" type="text" />
            <button data-timeline-id="${id}" class="btn btn-primary btn-mini timeline-comment-button">投稿</button>
            </div>
            <div id="timeline-post-comment-form-loader-${id}" class="timeline-post-comment-form-loader">
            <?php echo op_image_tag('ajax-loader.gif', array()) ?>
            </div>
            <div id="timeline-post-comment-form-error-${id}" class="timeline-post-comment-form-loader">
            </div>
          </div>
          {{if null!==image_url}}
          <div class="timeline-post-image-detail" id="timeline-post-image-detail-${id}">
            <div class="partsHeading"><h3>${member.name} さんの投稿</h3></div>
            <div class="timeline-post-image-detail-expand">
              <img src="${image_url_large}" alt="timeline-images-${id}-expaned" class="timeline-images-expand" />
            </div>
            <div class="timeline-post-image-detail-content">
              <div class="timeline-post-member-image">
                <a href="${member.profile_url}" title="${member.name}"><img src="${member.profile_image}" alt="${member.name}" /></a>
              </div>
              <div class="timeline-post-content">
                <div class="timeline-member-name">
                  <a class="screen-name" href="${member.profile_url}">${member.screen_name}</a>
                </div>
                <div class="timeline-post-body">
                  {{html body_html}}
                </div>
              </div>
            </div>
          </div>
          {{/if}}
          {{if member.self==true}}
          <div class="timeline-post-delete-confirm" id="timeline-post-delete-confirm-${id}">
            <div class="partsHeading"><h3>投稿の削除</h3></div>
            <div class="timeline-post-delete-confirm-context">削除してよろしいですか？</div>
            <div class="timeline-post-delete-confirm-content">
              <div class="timeline-post-member-image">
                <a href="${member.profile_url}" title="${member.name}"><img src="${member.profile_image}" alt="${member.name}" /></a>
              </div>
              <div class="timeline-post-content">
                <div class="timeline-member-name">
                  <a class="screen-name" href="${member.profile_url}">${member.screen_name}</a>
                </div>
                <div class="timeline-post-body">
                  {{if image_url !== null}}
                      <img src="${image_url}" alt="timeline-images-${id}" class="timeline-post-image" width="48" height="48" />
                  {{/if}}
                  {{html body_html}}
                </div>
              </div>
              <div class="timeline-post-delete" style="text-align: center;">
              <button class="timeline-post-delete-button btn btn-danger"data-activity-id="${id}">削除</button>
              </div>
              <div class="timeline-post-delete-loading" style="text-align: center; display: none;">
                <?php echo op_image_tag('ajax-loader.gif') ?>
              </div>
            </div>
          </div>
          {{/if}}
        </div>
</script>

<script id="timelineCommentTemplate" type="text/x-jquery-tmpl">
            <div class="timeline-post-comment">
              <div class="timeline-post-comment-member-image">
                <a href="${member.profile_url}"><img src="${member.profile_image}" alt="" width="36" /></a>
              </div>
              <div class="timeline-post-comment-content">
                <div class="timeline-post-comment-name-and-body">
                <a class="screen-name" href="${member.profile_url}">${member.screen_name}</a>
                <span class="timeline-post-comment-body">
                {{html body_html}}
                </span>
                </div>
              </div>
              <!-- nice Plugin -->
              <div class="nice-comment">
                <a><span class="nice-list" data-nice-id="${id}">いいね！</span></a>
                <a><span class="nice-cancel" data-nice-id="${id}" style="display: none;">いいね！を取り消す&nbsp;</span></a>
                {{if member.self==false}}<a><span class="nice-post" data-nice-id="${id}" member-id="${member.id}"><i class="icon-thumbs-up"></i>&nbsp;&nbsp;</span></a>{{/if}}
                <div class="nice-list-member" data-nice-id="${id}"></div>
              </div>


                <div class="timeline-post-comment-control">
                {{if member.self==true }}<a href="#timeline-post-delete-confirm-${id}" class="timeline-post-delete-confirm-link">削除する</a> | {{/if}} <a href="<?php echo url_for('@homepage', array('absolute' => true)) ?>timeline/show/id/${id}"><span class="timestamp">${created_at}</span></a>
                </div>
              
              {{if member.self==true }}
              <div class="timeline-post-delete-confirm" id="timeline-post-delete-confirm-${id}">
                <div class="partsHeading"><h3>投稿の削除</h3></div>
                <div class="timeline-post-delete-confirm-context">削除してよろしいですか？</div>
                <div class="timeline-post-delete-confirm-content">
                  <div class="timeline-post-member-image">
                    <a href="${member.profile_url}" title="${member.name}"><img src="${member.profile_image}" alt="${member.name}" /></a>
                  </div>
                  <div class="timeline-post-content">
                    <div class="timeline-member-name">
                      <a class="screen-name" href="${member.profile_url}">${member.screen_name}</a>
                    </div>
                    <div class="timeline-post-body">
                      {{html body_html}}
                    </div>
                  </div>
                  <div class="timeline-post-delete" style="text-align: center;">
                  <button class="timeline-post-delete-button btn btn-danger"data-activity-id="${id}">削除</button>
                  </div>
                </div>
              </div>
              {{/if}}
            </div>
</script>

<script id="NicelistTemplate" type="text/x-jquery-tmpl">
  <table style="border: 1px #000 solid;">
    <tr style="padding: 2px;">
      <td style="width: 48px; padding: 2px;"><a href="${profile_url}"><img src="${profile_image}" width="48"></a></td>
      <td style="padding: 2px;"><a href="${profile_url}">${name}</a></td>
    </tr>
  </table>
</script>
