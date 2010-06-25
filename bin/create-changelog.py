#!/usr/bin/python
#
# create-changelog.py - Create changelogs from git commits
# Copyright (C) 2010 Icinga Development Team
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
#
# Author: Marius Hein <marius(dot)>hein(at)netways(dot)de>
#
# Idea was borrowed from:
# http://blog.cryos.net/archives/202-Git-and-Automatic-ChangeLog-Generation.html
# Copyright 2008 Marcus D. Hanwell <marcus@cryos.org>
# Distributed under the terms of the GNU General Public License v2 or later
#

import string
import re
import os
from optparse import OptionParser

reKey = re.compile('^(\w+):\s([^$]+)$')

def parse_options():

	oParser = OptionParser(usage='--file=<changelog>')

	oParser.add_option('-f', '--file', dest='filename', help='Target filename')

	oParser.add_option('-O', '--overwrite', dest='overwrite', help='Overwrite file',
		default=False, action="store_true")

	oParser.add_option('-r', '--rev', dest='rev', help='Base revision',
		default=False)

	(options, args) = oParser.parse_args()

	if not options.filename:
		oParser.error("--file is required!")

	return options

def make_text(data):
	out = str()
	data['MAIL'] = data['MAIL'].replace('@', '(AT)')
	data['MAIL'] = '<' + data['MAIL'].replace('.', '(DOT)') + '>'
	out = '%(DATE)-10s %(AUTHOR)s %(MAIL)s\n' % (data)
	out += '%-10s Commit: %s\n' % ('', data['COMMIT'])
	out += chr(10)
	
	text = data['SUBJECT'] + data['BODY']

	for i in text.split(chr(10)):
		i = re.sub('^\*[^\s]', '* ', i)
		if re.search('^[^\*]', i):
			i = '* ' + i
		i = re.sub('\s\*\s', '\n* ', i);
		i = re.sub('^\s+\*', '* ', i);

		# Strip to 70 chars per line
		while len(i):
			l=len(i)
			y=False
			if l>86:
				y = i[0:86].rfind(" ")
				if y:
					l=y+1

			out += i[0:l] + chr(10)

			if y:
				out += '  '

			i=i[l:]
		out += i + chr(10)
	
	return out + chr(10)

def main():
	"""The main method"""
	
	o = parse_options()
	
	gitOptions = "log --no-merges --date=short"
	gitFormat = 'COMMIT: %H%nMAIL: %ae%nAUTHOR: %an%nDATE: %cd%nSUBJECT: %s%nBODY: %b%n---'
	gitBin = '/usr/bin/git'

	gitOptions += ' --pretty=\'format:' + gitFormat + '\''

	if o.rev:
		gitOptions += ' %s..HEAD' % (o.rev)



	# Execute git log with the desired command line options.
	print('Exporting git log: %s' % (gitBin + ' ' + gitOptions))
	fin = os.popen(gitBin + ' ' + gitOptions)

	fMode = 'a'
	if o.overwrite == True:
		fMode = 'w'

	fo = open(o.filename, fMode)

	key = False
	data = dict()
	
	for line in fin:
		
		if line == "---\n":
			fo.write(make_text(data))
			
			data = dict()
		else:
			m=reKey.match(line)

			if m:
				key = m.group(1);
				data[key] = m.group(2).replace("\n", "")
			elif key:
				data[key] = data[key] + line
			
	fin.close()
	exit(0)

if __name__ == '__main__':
	main()