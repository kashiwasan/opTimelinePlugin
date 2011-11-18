<?php

class opTimelinePluginUtil
{
  public function screenNameReplace($body, $baseUrl, $options = array())
  {
    preg_match_all('/(@+)([-._0-9A-Za-z]+)/', $body, $matches);
    $i = 0;
    if ($matches)
    {
      foreach ($matches[2] as $screenName)
      {
        $member = Doctrine::getTable('MemberConfig')->findOneByNameAndValue('op_screen_name', $screenName);
        
        if ($member)
        {
          $memberId = $member->getMemberId();
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
