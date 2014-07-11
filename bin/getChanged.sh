#!/bin/bash
# Yorick Terweijden <yorick.terweijden@sourcefabric.org>
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
if ! git rev-parse 2> /dev/null
then
    echo "Not a git repository"
    exit 1
fi
if [ -z "$1" ]; then
    echo "A git tree-ish is required";
    exit 1
fi
COMMIT=$1
if ! git cat-file -e $COMMIT 2> /dev/null
then
    echo 'The specified tree-ish does not exist' 
    exit 1
fi

if [ -n "$2" ]; then
    PATHPREFIX=$2
else
    PATHPREFIX="packages/"
fi
if [ ! -d "$PATHPREFIX" ]; then
    echo "The packages dir does not exist"
    exit 1
fi
git archive --format zip -o $PATHPREFIX$COMMIT.zip HEAD -- $(git diff-tree --diff-filter=ACMR --no-commit-id --name-only -r $COMMIT^1.. --)
git diff-tree --no-commit-id --name-status -r $COMMIT^1.. -- >> $PATHPREFIX$COMMIT.txt
