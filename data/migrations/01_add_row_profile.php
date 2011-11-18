<?php

class opTimelinePlugin01AddRowProfile extends Doctrine_Migration_base
{
  public function up()
  {
    $profile = Doctrine::getTable('Profile')->findByName('op_screen_name');
    if (!$profile)
    {
      $profile = new Profile();
      $profile->setName('op_screen_name');
      $profile->setIsRequired(1);
      $profile->setIsEditPublicFlag(0);
      $profile->setDefaultPublicFlag(1);
      $profile->setFormType('input');
      $profile->setValueType('regexp');
      $profile->setIsDispRegist(1);
      $profile->setIsDispConfig(1);
      $profile->setIsDispSearch(1);
      $profile->setIsPublicWeb(0);
      $profile->setValueRegexp('/(@+)([-._0-9A-Za-z]+)/');
      $profile->setSortOrder(15);
      $profile->save();
    }
  }
}
