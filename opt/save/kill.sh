#!/bin/bash


list_descendants ()
{
  local children=$(ps -o pid= --ppid "$1")

  for pid in $children
  do
    list_descendants "$pid"
  done

  echo "$children"
  echo "$children" 1>&2
}

kill -9 $(list_descendants $@)
kill -9 $@

