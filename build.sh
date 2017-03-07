#!/usr/bin/env bash

commit=$1
if [ -z ${commit} ]; then
    commit=$(git tag | tail -n 1)
    if [ -z ${commit} ]; then
        commit="master";
    fi
fi

# Remove old release
rm -rf WbmViewportResizer WbmTagManager-*.zip

# Build new release
mkdir -p WbmTagManager
git archive ${commit} | tar -x -C WbmTagManager
composer install --no-dev -n -o -d WbmTagManager
zip -r WbmTagManager-${commit}.zip WbmTagManager