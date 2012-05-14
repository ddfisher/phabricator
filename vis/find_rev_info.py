import subprocess
from collections import namedtuple
import os.path
import json

"""
Format is:  <mode> SP <type> SP <object> SP <object size> TAB <file>
see: man git-ls-tree
"""
BlobInfo = namedtuple('BlobInfo', ['mode', 'type', 'hash', 'size', 'filepath'])
RevisionInfo = namedtuple('RevisionInfo', ['date', 'blobs'])

# parseBlobLine :: str -> BlobInfo
def parseBlobLine(line):
  size_index = BlobInfo._fields.index('size')
  fields = line.split()
  try:
    fields[size_index] = int(fields[size_index])
  except ValueError:
    fields[size_index] = -1

  blob = BlobInfo(*fields)
  return blob

# parseTreeOutput :: str -> [BlobInfo]
def parseTreeOutput(output):
  lines = filter(None, output.split('\n'))
  blobs = map(parseBlobLine, lines)
  blobs = filter(lambda b: b.type == 'blob', blobs)
  return blobs


# getTreeOutput :: IO(str) -> str
def getTreeOutput(commit_hash):
  binary_str = subprocess.check_output(['git', 'ls-tree', '-r', '-l', commit_hash])
  return str(binary_str, encoding='utf8')

# getRevInfo :: IO(str) -> RevisionInfo
def getRevInfo(rev):
  timestamp, commit_hash = rev
  return RevisionInfo(int(timestamp), parseTreeOutput(getTreeOutput(commit_hash)))

# getAllRevs :: IO() -> [(timestamp :: str, hash :: str)]
def getAllRevs():
  revs = subprocess.check_output(['git', 'rev-list', '--all', '--timestamp'])
  revs = str(revs, encoding='utf8').split('\n')[:-1]
  return map(str.split, revs)

# getFullInfo :: IO() -> [RevisionInfo]
def getFullInfo():
  return map(getRevInfo, getAllRevs())

# dumpAllRevInfo :: [[BlobInfo]] -> str
def dumpAllRevInfo(revs):
  def extractInfo(rev):
    return { 'date': rev.date
           , 'files': list(map(lambda blob: {'name': blob.filepath, 'size': blob.size}, rev.blobs))
           }

  return json.dumps(list(reversed(list(map(extractInfo, revs)))))

out = dumpAllRevInfo(getFullInfo())
print(out)
