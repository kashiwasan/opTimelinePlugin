<?php

class opTimelinePluginUtil
{
  public function screenNameReplace($body, $baseUrl, $options = array())
  {
    $profile = Doctrine::getTable('Profile')->findOneByName('op_screen_name');
    if (!$profile)
    {
      return $body;
    }
    $profileId = $profile->getId();

    preg_match_all('/(@+)([-._0-9A-Za-z]+)/', $body, $matches);
    $i = 0;

    if ($matches)
    {
      foreach ($matches[2] as $screenName)
      {
        $member = Doctrine::getTable('MemberProfile')->findOneByProfileIdAndValue($profileId, $screenName);
        
        if ($member)
        {
          $memberId = $member->getId();
          $link = '<a href="'.$baseUrl.'/member/'.$memberId.'" target="_blank">@'.$screenName.'</a>';
          $mention = '/'.$matches[0][$i].'/';
          $body = preg_replace($mention, $link, $body);
        }

        $i++;
      }
    }
    return $body;
  }

}
