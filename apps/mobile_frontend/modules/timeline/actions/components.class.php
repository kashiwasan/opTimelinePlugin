<?php

/**
 * opGyoenKintaiPlugin components.
 *
 * @package    OpenPNE
 * @subpackage opTimelinePlugin
 * @author     tatsuya ichikawa <ichikawa@tejimaya.com>
 */

class timelineComponents extends sfComponents
{
  public function executeTimelineAll(sfWebRequest $request)
  {
    if ($this->gadget->getConfig('is_viewable_activity_form') && opConfig::get('is_allow_post_activity'))
    {   
      $this->form = new ActivityDataForm();
    }   

    $builder = opActivityQueryBuilder::create()
            ->setViewerId($this->getUser()->getMemberId());
    $builder->includeSns()->includeFriends()->includeSelf();
    $query = $builder->buildQuery();

    $globalAPILimit = $this->gadget->getConfig('row');
    $query->limit($globalAPILimit);

    $this->activities = $query
         ->andWhere('in_reply_to_activity_id IS NULL')
         ->execute();
  }

  public function executeTimelineProfile(sfWebRequest $request)
  {
    $builder = opActivityQueryBuilder::create()
            ->setViewerId($this->getUser()->getMemberId());
    $builder->includeMember($this->getRequestParameter('id'));
    $query = $builder->buildQuery();
    $query->limit(20);

    $this->activities = $query
         ->andWhere('in_reply_to_activity_id IS NULL')
         ->execute();
    $this->member = Doctrine::getTable('Member')->find($this->getRequestParameter('id'));
  }

  public function executeTimelineCommunity(sfWebRequest $request)
  {
    $this->member_id = $this->getUser()->getMemberId();
    $community_id = $this->community->getId();

    $builder = opActivityQueryBuilder::create()
            ->setViewerId($this->member_id)
            ->setCommunityId($community_id);
    $builder->includeSns()->includeFriends()->includeSelf();
    $query = $builder->buildQuery();
    $query->limit(20);

    $this->activities = $query
         ->andWhere('in_reply_to_activity_id IS NULL')
         ->execute();
    if ($this->community->isPrivilegeBelong($this->member_id))
    {
      $this->form = new TimelineDataForm();
      $this->form->setDefault('foreign_table', 'community');
      $this->form->setDefault('foreign_id', $community_id);
    }     
  }
}
