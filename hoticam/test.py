#!/usr/bin/python
import StringIO
import subprocess

command = "This is the {}th tome of {}"
foo = command.format(5, "knowledge")
print foo