<?php

/**
 * opTimelinePluginConfiguration
 *
 * @package    opTimelinePlugin
 * @subpackage config
 * @author     Shouta Kashiwagi <kashiwagi@openpne.jp>
 */
class opTimelinePluginConfiguration extends sfPluginConfiguration
{
 /** 
  * initialize plugin
  *
  */
  public function initialize()
  {
    $this->dispatcher->connect('op_doctrine.post_insert_ActivityData', array($this, 'listenToPostInsertActivityData'));
  }

  public function listenToPostInsertActivityData(opDoctrineEvent $event)
  {
    $inReplyTo = $event->getSubject()->getInReplyToActivityId();
    if ($inReplyTo)
    {
      $memberFrom = sfContext::getInstance()->getUser()->getMember();
      $inReplyToRecord= Doctrine::getTable('ActivityData')->find($inReplyTo);
      $memberTo = $inReplyToRecord->getMember();
      if ($memberFrom->getId() !== $memberTo->getId())
      {
        $commentBody = $event->getSubject()->getBody();
        if (mb_strlen($commentBody) > 20)
        {
          $body = $memberFrom->getName().'さんからコメントが来ました: '.mb_substr($commentBody, 0, 19).'...';
        }
        else
        {
          $body = $memberFrom->getName().'さんからコメントが来ました: '.$commentBody;  
        }
        $options = array(
          'category' => 'other',
          'url' => sfContext::getInstance()->getConfiguration()->generateAppUrl('pc_frontend', 'timeline/show?id='.$inReplyTo, true),
        );
        opNotificationCenter::notify($memberFrom, $memberTo, $body, $options);
      }
    }
  }
}
