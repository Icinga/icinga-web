#!/usr/bin/env python

from optparse import OptionParser
import sys
import re
import fileinput

script = sys.argv[0]

parser = OptionParser()

parser.add_option("-f", "--file", dest="filename",
                  help="Filename to work on")

(options, args) = parser.parse_args()

if not options.filename:
	print("Filename is required: " + script + " -f <filename>")

re_msgid = re.compile(r"^msgid\s+\"(.+)\"$");
mo = False

for line in fileinput.input(options.filename, inplace=1):
	if not mo:
		mo = re_msgid.match(line)
	elif mo:
		if line == "msgstr \"\"\n":
			line = "msgstr \"" + mo.group(1) + "\"\n"
		mo = False
	sys.stdout.write(line)
