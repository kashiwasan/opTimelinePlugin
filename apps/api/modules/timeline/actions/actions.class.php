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
    $parameters = $request->getGetParameters();

    if (isset($parameters['target']))
    {
      $this->forward400IfInvalidTarget($parameters);
    }

    //実行の仕方自体はアクティビティの検索と同じなので、アクティビティ検索APIを使用する
    //@todo 本体のアクティビティ検索の部分をmodel化して同じクラスを使用するようにする
    $apiDatas = (array) json_decode($this->fetchApiData('activity/search'));

    $timeline = new opTimeline();
    $returnDatas = $timeline->addPublicFlagForActivityDatas($apiDatas);

    return $this->renderJson($returnDatas);
  }

  private function fetchApiData($apiName)
  {
    $moduleName = sfContext::getInstance()->getModuleName();
    $actionName = sfContext::getInstance()->getActionName();
    $currentApiName = $moduleName.'/'.$actionName;

    $currentUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $callApiUrl = str_replace($currentApiName, $apiName, $currentUrl);

    return file_get_contents($callApiUrl);
  }

  private function forward400IfInvalidTarget(array $params)
  {
    $validTargets = array('friend', 'community');

    if (!in_array($params['target'], $validTargets)) {
      return $this->forward400('target parameter is invalid.');
    }

    if ($params['target'] === 'community')
    {
      $this->forward400Unless($params['target_id'], 'target_id parameter not specified.');
    }

  }
  
}
