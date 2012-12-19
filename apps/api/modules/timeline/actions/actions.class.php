<?php

/**
 * timeline actions.
 *
 * @package    OpenPNE
 * @subpackage timeline
 * @author     suzuki-mar
 */
class timelineActions extends opJsonApiActions
{

  public function executeSearch(sfWebRequest $request)
  {
    //コントローラー部分は変更する必要がないのと変更するためのコストが高すぎるので
    //ソースコードをコピーした

    $builder = opActivityQueryBuilder::create()
                    ->setViewerId($this->getUser()->getMemberId());

    if (isset($request['target']))
    {
      if ('friend' === $request['target'])
      {
        $builder->includeFriends($request['target_id'] ? $request['target_id'] : null);
      }
      elseif ('community' === $request['target'])
      {
        $this->forward400Unless($request['target_id'], 'target_id parameter not specified.');
        $builder
                ->includeSelf()
                ->includeFriends()
                ->includeSns()
                ->setCommunityId($request['target_id']);

      }
      else
      {
        $this->forward400('target parameter is invalid.');
      }
    }
    else
    {
      if (isset($request['member_id']))
      {
        $builder->includeMember($request['member_id']);
      }
      else
      {
        $builder
                ->includeSns()
                ->includeFriends()
                ->includeSelf();
      }
    }

    $query = $builder->buildQuery();

    if (isset($request['keyword']))
    {
      $query->andWhereLike('body', $request['keyword']);
    }

    $globalAPILimit = sfConfig::get('op_json_api_limit', 20);
    if (isset($request['count']) && (int) $request['count'] < $globalAPILimit)
    {
      $query->limit($request['count']);
    }
    else
    {
      $query->limit($globalAPILimit);
    }

    if (isset($request['max_id']))
    {
      $query->addWhere('id <= ?', $request['max_id']);
    }

    if (isset($request['since_id']))
    {
      $query->addWhere('id > ?', $request['since_id']);
    }

    if (isset($request['activity_id']))
    {
      $query->addWhere('id = ?', $request['activity_id']);
    }

    $this->activityData = $query
                    ->andWhere('in_reply_to_activity_id IS NULL')
                    ->execute();

    $this->setTemplate('search');
  }

}
