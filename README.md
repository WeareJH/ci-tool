# CI Tool

[![CircleCI](https://circleci.com/gh/WeareJH/ci-tool/tree/master.svg?style=svg&circle-token=f1ac70c3eaaec13df09a2c04fc8e0d9c2cea8b61)](https://app.circleci.com/pipelines/github/WeareJH/ci-tool)

## Overview

This tool provides an abstraction that can be used for optimising CI pipelines.

## API

### ci-tool is-tested

Determines whether the currently checked out git tree has already been tested before.
 
It is intended to be used in bash conditional statements 
```bash
if [[ ! php ci-tool is-tested ]] then ;
  # Test commands go here"
  ci-tool register-tested
fi
```
 
### ci-tool register-tested

Marks the currently checked out git tree as tested for future reference. 

### ci-tool register-built

Marks the currently checked out git tree as built for future reference. 

### ci-tool download-artifact

Downloads a built artifact compatible with the current git tree, or else it fails.

It is intended to be used in a bash if statement in the build step. 

The downloaded artifact will be automatically picked up by the deploy step of the M2 recipe.
```bash
if [[ ! php ci-tool download-artifact ]] then ;
  # Build project commands go here 
  ci-tool register-built
fi
```

### ci-tool print-registry

Prints the registry to stdout. It highlights the current hash

## Build

Build a phar file from this project using https://github.com/clue/phar-composer