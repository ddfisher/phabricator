<?php

/*
 * Copyright 2011 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

final class DiffusionGitHistoryQuery extends DiffusionHistoryQuery {

  protected function executeQuery() {
    $drequest = $this->getRequest();

    $repository = $drequest->getRepository();
    $path = $drequest->getPath();
    $commit = $drequest->getCommit();

    $local_path = $repository->getDetail('local-path');
    $git = $drequest->getPathToGitBinary();

    list($stdout) = execx(
      '(cd %s && %s log '.
        '--skip=%d '.
        '-n %d '.
        '-M '.
        '-C '.
        '-B '.
        '--find-copies-harder '.
        '--raw '.
        '-t '.
        '--abbrev=40 '.
        '--pretty=format:%%x1c%%H%%x1d '.
        '%s -- %s)',
      $local_path,
      $git,
      $offset = 0,
      $this->getLimit(),
      $commit,
      $path);

    $commits = explode("\x1c", $stdout);
    array_shift($commits); // \x1c character is first, remove empty record

    $history = array();
    foreach ($commits as $commit) {
      list($hash, $raw) = explode("\x1d", $commit);

      $item = new DiffusionPathChange();
      $item->setCommitIdentifier($hash);
      $history[] = $item;
    }

    return $history;
  }

}