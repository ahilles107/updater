#!/bin/bash
# Yorick Terweijden <yorick.terweijden@sourcefabric.org>
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
AUTHOR="Yorick Terweijden"
PATHPREFIX="packages/"
VERSION="1"

USAGE="\n$BASH_SOURCE v$VERSION HELP\n \
\n\
This script allows you to create an update package in the target (-t) directory\n\
based on the differences in a git repository between the current state and a\n\
specific git tree-ish.\n\
The script requires to be called with the current working directory set to be the\n\
appropriate git repostitory.\n\
\n \
REQUIRED\n \
\t-c\ta git-treeish (e.g. a COMMIT or TAG)\n\n \
OPTIONAL\n \
\t-t\tthe target directory, defaults to '$PATHPREFIX'\n \
\t-h\tthis usage description\
\n \
\n\
Copyright 2014 Sourcefabric z.ú. written by $AUTHOR"

while getopts ":hc:t:" opt; do
  case $opt in
    c)
      COMMIT=$OPTARG
      if ! git rev-parse 2> /dev/null
      then
          echo "Current directory is a not a git repository: $PWD"
          exit 1
      fi
      if ! git cat-file -e $COMMIT 2> /dev/null
      then
          echo "The specified git tree-ish does not exist: $COMMIT"
          exit 1
      fi
      ;;
    t)
      PATHPREFIX=$OPTARG
      if [ ! -d "$PATHPREFIX" ]; then
          echo "The specified target does not exist: $PATHPREFIX"
          exit 1
      fi
      ;;
    h)
      echo -e "$USAGE"
      ;;
    \?)
      echo "Invalid option: -$OPTARG" >&2
      echo -e "$USAGE"
      ;;
    :)
      echo "Option -$OPTARG requires an argument." >&2
      echo -e "$USAGE"
      exit 1
      ;;
  esac
done

if [ -n "$COMMIT" ]; then
git archive --format zip -o $PATHPREFIX$COMMIT.zip HEAD -- $(git diff-tree --diff-filter=ACMR --no-commit-id --name-only -r $COMMIT^1.. --)
git diff-tree --no-commit-id --name-status -r $COMMIT^1.. -- >> $PATHPREFIX$COMMIT.txt
else
    echo -e "$USAGE"
fi
