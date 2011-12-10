<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * timeline actions.
 *
 * @package    OpenPNE
 * @subpackage timeline
 * @author     Shouta Kashiwagi <kashiwagi@tejimaya.com>
 * @author     Yoichi Kimura <yoichi.kimura@tejimaya.com>
 * @version    SVN: $Id: actions.class.php 9301 2008-05-27 01:08:46Z dwhittle $
 */
class timelineActions extends opApiActions
{
  public function executeList(sfWebRequest $request)
  {
    $this->activityData = Doctrine::getTable('ActivityData')->createQuery('ad')
      ->where('ad.in_reply_to_activity_id IS NULL')
      ->andWhere('ad.foreign_table IS NULL')
      ->andWhere('ad.foreign_id IS NULL')
      ->andWhere('ad.public_flag = ?', 1)
      ->orderBy('ad.id DESC')
      ->limit(20)
      ->execute();

    $this->baseUrl = sfConfig::get('op_base_url');
    $this->viewMemberId = $this->getMember()->getId();

    $this->getResponse()->setContentType('application/json');
  }
}
