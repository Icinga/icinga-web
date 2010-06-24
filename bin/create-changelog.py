#!/usr/bin/python
# Copyright 2010 Icinga Development Team
# GPLv2
# Author Marius

import string
import re
import os
from pprint import PrettyPrinter
from optparse import OptionParser

reKey = re.compile('^(\w+):\s([^$]+)$')
reLength = re.compile('\s\w*$')

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

#	# Create a ChangeLog file in the current directory.
#	fout = open(o.filename, 'w')
#
#	# Set up the loop variables in order to locate the blocks we want
#	authorFound = False
#	dateFound = False
#	messageFound = False
#	filesFound = False
#	message = ""
#	messageNL = False
#	files = ""
#	prevAuthorLine = ""
#
#	# The main part of the loop
#	for line in fin:
#		# The commit line marks the start of a new commit object.
#		if string.find(line, 'commit') >= 0:
#			# Start all over again...
#			authorFound = False
#			dateFound = False
#			messageFound = False
#			messageNL = False
#			message = ""
#			filesFound = False
#			files = ""
#			continue
#		# Match the author line and extract the part we want
#		elif re.match('Author:', line) >=0:
#			authorList = re.split(': ', line, 1)
#			author = authorList[1]
#			author = author[0:len(author)-1]
#			authorFound = True
#		# Match the date line
#		elif re.match('Date:', line) >= 0:
#			dateList = re.split(':   ', line, 1)
#			date = dateList[1]
#			date = date[0:len(date)-1]
#			dateFound = True
#		# The svn-id lines are ignored
#		elif re.match('    git-svn-id:', line) >= 0:
#			continue
#		# The sign off line is ignored too
#		elif re.search('Signed-off-by', line) >= 0:
#			continue
#		# Extract the actual commit message for this commit
#		elif authorFound & dateFound & messageFound == False:
#			# Find the commit message if we can
#			if len(line) == 1:
#				if messageNL:
#					messageFound = True
#				else:
#					messageNL = True
#			elif len(line) == 4:
#				messageFound = True
#			else:
#				if len(message) == 0:
#					message = message + line.strip()
#				else:
#					message = message + " " + line.strip()
#		# If this line is hit all of the files have been stored for this commit
#		elif re.search('files changed', line) >= 0:
#			filesFound = True
#			continue
#		# Collect the files for this commit. FIXME: Still need to add +/- to files
#		elif authorFound & dateFound & messageFound:
#			fileList = re.split(' \| ', line, 2)
#			if len(fileList) > 1:
#				if len(files) > 0:
#					files = files + ", " + fileList[0].strip()
#				else:
#					files = fileList[0].strip()
#		# All of the parts of the commit have been found - write out the entry
#		if authorFound & dateFound & messageFound & filesFound:
#			# First the author line, only outputted if it is the first for that
#			# author on this day
#			authorLine = date + "  " + author
#			if len(prevAuthorLine) == 0:
#				fout.write(authorLine + "\n")
#			elif authorLine == prevAuthorLine:
#				pass
#			else:
#				fout.write("\n\n" + authorLine + "\n")
#
#			# Assemble the actual commit message line(s) and limit the line length
#			# to 80 characters.
#			# commitLine = "* " + files + ": " + message
#			commitLine = message
#			i = 0
#			commit = ""
#			while i < len(commitLine):
#				if len(commitLine) < i + 78:
#					commit = commit + "\n " + commitLine[i:len(commitLine)]
#					break
#				index = commitLine.rfind(' ', i, i+78)
#				if index > i:
#					commit = commit + "\n  " + commitLine[i:index]
#					i = index+1
#				else:
#					commit = commit + "\n  " + commitLine[i:78]
#					i = i+79
#
#			# Write out the commit line
#			fout.write(commit)
#
#			#Now reset all the variables ready for a new commit block.
#			authorFound = False
#			dateFound = False
#			messageFound = False
#			messageNL = False
#			message = ""
#			filesFound = False
#			files = ""
#			prevAuthorLine = authorLine
#
#	# Close the input and output lines now that we are finished.
#	fin.close()
#	fout.close()

if __name__ == '__main__':
	main()