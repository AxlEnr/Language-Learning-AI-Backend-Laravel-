#!/bin/bash

SAIL_BIN_PATH="${SAIL_BIN_PATH:-./vendor/bin/sail}"

exec "${SAIL_BIN_PATH}" "$@"
