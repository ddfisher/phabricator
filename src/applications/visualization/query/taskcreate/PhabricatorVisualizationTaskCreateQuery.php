<?php

final class PhabricatorVisualizationTaskCreateQuery {

  public function execute() {
    $task_table = new ManiphestTask();
  
    $conn = $task_table->establishConnection('r');
  
    $data = queryfx_all(
      $conn,
      "SELECT authorPHID, count(*) as numCreated, dateCreated
       from %T
     GROUP BY authorPHID",
      $task_table->getTableName());
  
    return $data;
  }
}
