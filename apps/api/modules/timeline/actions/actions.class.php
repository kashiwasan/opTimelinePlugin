<?php

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
