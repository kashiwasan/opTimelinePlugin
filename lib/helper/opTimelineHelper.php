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
/*******************
function op_timeline_replace_screenname_link($body, $baseUrl, $options = array())
{
  $profileId = Doctrine::getTable('Profile')->findByName('op_screen_name')->getId();
  preg_match_all('/(@+)([-._0-9A-Za-z]+)/', $body, $matches);
  $i = 0;
  foreach ($matches[2] as $screenName)
  {
    $memberId = Doctrine::getTable('MemberProfile')->findByProfileIdAndValue($profileId, $screenName)->getMemberId();
    //$matches[3][$i] = $memberId;
    $link = '<a href="'.$baseUrl.'/member/'.$memberId.'" target="_blank">@'.$screenName.'</a>';
    preg_replace('/(@+)'.$screenName.'/', $link, $body);
    $i++;
  }
  return $body;
}
******************/
?>
