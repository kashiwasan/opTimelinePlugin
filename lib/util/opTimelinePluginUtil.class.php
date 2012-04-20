<?php

class opTimelinePluginUtil
{
  public static function hasScreenName($body)
  {
    preg_match_all('/(@+)([-._0-9A-Za-z]+)/', $body, $matches);
    if($matches[2])
    {
      $memberIds = array();
      foreach ($matches[2] as $screenName)
      {
        $member = Doctrine::getTable('MemberConfig')->findOneByNameAndValue('op_screen_name', $screenName);
        if ($member)
        {
          $memberIds[] = $member->getMemberId();
          $memberObject = Doctrine::getTable('Member')->find($member->getMemberId());
          opNotificationCenter::notify(sfContext::getInstance()->getUser()->getMember(), $memberObject, $body, array('category' => 'other', 'url' => url_for('@member_timeline?id='.sfContext::getInstance()->getUser()->getMemberId())));
        }
      }
      $memberId = implode("|", $memberIds);
      return '|' . $memberId . '|';
    }
    else
    {
      return null;
    }
  }
}
