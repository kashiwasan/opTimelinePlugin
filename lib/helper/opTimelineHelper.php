<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * opTimelineHelper provides timeline helper functions.
 *
 * @package    OpenPNE
 * @subpackage helper
 * @author     Shoua Kashiwagi <kashiwagi@tejimaya.com>
 */

function op_timeline_plugin_body_filter($activity, $body, $is_auto_link = true)
{
  if ($activity->getTemplate())
  {
    $config = $activity->getTable()->getTemplateConfig();
    if (!isset($config[$activity->getTemplate()]))
    {   
      return $body; 
    }   

    $params = array();
    foreach ($activity->getTemplateParam() as $key => $value)
    {   
      $params[$key] = $value;
    }   
    $body = __($config[$activity->getTemplate()], $params);
    $event = sfContext::getInstance()->getEventDispatcher()->filter(new sfEvent(null, 'op_activity.template.filter_body'), $body);
    $body = $event->getReturnValue();
  }

  $event = sfContext::getInstance()->getEventDispatcher()->filter(new sfEvent(null, 'op_activity.filter_body'), $body);
  $body = $event->getReturnValue();

  if (false === strpos($body, '<a') && $activity->getUri())
  {
    return link_to($body, $activity->getUri());
  }

  if ($is_auto_link)
  {
    if ('mobile_frontend' === sfConfig::get('sf_app'))
    {   
      return op_auto_link_text_for_mobile($body);
    }   

    return op_auto_link_text($body);
  }

  return $body;
}

function op_timeline_plugin_screen_name($body, $options = array())
{
  preg_match_all('/(@+)([-_0-9A-Za-z]+)/', $body, $matches);
  if ($matches)
  {
    $i = 0;
    foreach ($matches[2] as $screenName)
    {
      $member = Doctrine::getTable('MemberConfig')->findOneByNameAndValue('op_screen_name', $screenName);
      
      if ($member)
      {
        $memberId = $member->getMemberId();
        $link = link_to('@'.$screenName, app_url_for('pc_frontend', array('sf_route' => 'obj_member_profile', 'id' => $memberId), true), array('target' => '_blank'));
        $mention = '/'.$matches[0][$i].'/';
        $body = preg_replace($mention, $link, $body);
      }

      $i++;
    }
  }

  return $body;
}


function op_timeline_activity($activity)
{
  $viewMemberId = sfContext::getInstance()->getUser()->getMemberId();
  $member = $activity->getMember();
  $images = null;
  if ($activity->getImages()->count())
  {
    $images = $activity->getImages();
  }

  return array(
    'id' => $activity->getId(),
    'member' => op_api_member($member),
    'body' => $activity->getBody(),
    'body_html' => op_activity_linkification(nl2br(op_api_force_escape($activity->getBody()))),
    'public_flag' => (int)$activity->getPublicFlag(),
    'uri' => $activity->getUri(),
    'source' => $activity->getSource(),
    'source_uri' => $activity->getSourceUri(),
    'image_url' => !is_null($images) ? sf_image_path($images[0]->getFile(), array('size' => '48x48'), true) : null,
    'image_large_url' => !is_null($images) ? sf_image_path($images[0]->getFile(), array(), true) : null,
    'created_at' => date('r', strtotime($activity->getCreatedAt())),
  );
}