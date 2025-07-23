#!/usr/bin/env bash

# Read the current version from composer.json
CURRENT_VERSION=$(git describe --tags --abbrev=0)

# Read the increment argument (if provided)
INCREMENT=$1

# If $INCREMENT is not empty, run semver to increment the version, then update composer.json and create a git tag
if [ -n "$INCREMENT" ]; then
    VERSION=$(semver --increment "$INCREMENT" "$CURRENT_VERSION")
    git tag -a -m "Tagging version $VERSION" "$VERSION"
else
    echo "No increment provided, skipping version bump"
    exit 1
fi
