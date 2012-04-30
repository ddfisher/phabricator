<?php


final class PhabricatorVisualizationCommitQuery {

  private $dateAfter;
  
  
  public function setDateAfter($date_after) {
    $this->dateAfter = $date_after;
  }

  public function execute() {
    $diff_table = new DifferentialDiff();
    
    $conn = $diff_table->establishConnection('r');

    $data = queryfx_all(
      $conn,
      "SELECT diff.authorPHID, sum(diff.lineCount) from %T diff
       GROUP BY diff.authorPHID",
      $diff_table->getTableName());
    
    return $data;
  }

}