#!/usr/bin/env bash
cp automatisation/pre-commit.sh .git/hooks/pre-commit
chmod a+rwx,g-w,o-w .git/hooks/pre-commit