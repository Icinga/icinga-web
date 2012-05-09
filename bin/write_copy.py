#!/usr/bin/env python

import re
import argparse
import fnmatch
import os

_print_margin = 80

_fill_letter = '-'

_marker = re.compile(".+{{{ICINGA_LICENSE_CODE}}}")
_marker_plain = "{{{ICINGA_LICENSE_CODE}}}"

_cfg = {'php': {'prefix': '// ',
                'fill_first': True,
                'fill_last': True,
                'lines_after': 1},
        
        'js': {'prefix': '// ',
               'fill_first': True,
               'fill_last': True,
               'lines_after': 1}}

_parser = argparse.ArgumentParser(description="icinga web license writer")
    
_parser.add_argument("-b", "--base", dest="base",
                        help="Base directory to start")

_parser.add_argument("-l", "--license", dest="license",
                        help="Inline license file")

_args = _parser.parse_args()

def _get_files(base):
    pattern = re.compile(".*\.(" + "|".join(_cfg.keys()) + ")$")
    result = []
    for root, dirnames, filenames in os.walk(base):
        for filename in filenames:
            if (re.match(pattern, filename)):
                result.append(os.path.join(root, filename))
    return result

def _add_copy(file, data, cfg, type, license):
    start = 0
    
    if type == "php" and re.match('<\?php', data[start]):
        start = 1
    
    match = []
    for index, item in enumerate(data):
        if (re.match(_marker, item)):
            match.append(index)
    
    if (len(match) == 2):
        print("Removing copy from %s" % file)
        del data[ match[0] :match[1]+1]
    
    new_license = _prefix_array(license, cfg['prefix'])
    
    data.insert(start, cfg['prefix'] + _marker_plain + "\n");
    start = start + 1
    
    try:
        if cfg['fill_first'] == True:
            data.insert(start, _fill_bar(cfg["prefix"]))
            start = start + 1
    except(KeyError):
        pass
    
    start = _insert_list(data, new_license, start)
    
    try:
        if cfg['fill_last'] == True:
            data.insert(start, _fill_bar(cfg["prefix"]))
            start = start + 1
    except(KeyError):
        pass
    
    data.insert(start, cfg['prefix'] + _marker_plain + "\n");
    start = start + 1
    
    try:
        data.insert(start, "\n" * cfg['lines_after'])
    except (KeyError):
        pass
    
    print("License added to %s" % file)
    
    return data

def _fill_bar(prefix):
    return prefix + _fill_letter * (80-len(prefix)) + "\n"

def _get_file_content(file):
    fp = open(file, 'r')
    data = fp.readlines()
    fp.close()
    return data

def _write_file(file, data):
    fp = open(file, 'w')
    fp.writelines(data)
    fp.close()

def _prefix_array(array, prefix):
    out = []
    for item in array:
        out.append(prefix + item)
    return out

def _insert_list(target, new, start=0):
    for item in new:
        target.insert(start, item)
        start = start + 1
    return start

def main():
    
    license_data = _get_file_content(_args.license)
    
    for file in _get_files(_args.base):
        ext = os.path.splitext(file)[1].lstrip(".")
        cfg = _cfg[ext]
        data = _get_file_content(file)
        new = _add_copy(file, data, cfg, ext, license_data)
        _write_file(file, data)
    

if __name__ == "__main__":
     main()

