#!/usr/bin/perl -w

package Icinga::Web::Locale::Updater::Bin;

use File::Basename;
use Getopt::Long;
use Data::Dumper;
use Locale::PO;

use vars qw (
	$VERSION
	$PROGNAME
	$BASE
	$CAT
	$PATTERN_MATCH
	$PATTERN_IGNORE
	$PATTERT_GT
	$POFILE
);

use subs qw (
	cp
);

$VERSION = '1.0';
$PROGNAME = basename($0);

Getopt::Long::Configure('bundling');
GetOptions(
	'base|b=s'	=> \$BASE,
	'out|O=s'	=> \$POFILE
);

cp('%s starting up ...', $PROGNAME);
cp('Basedir is %s', $BASE);

$CAT = {};
$PATTERN_MATCH = qr{\.(php|js|xml)$};
$PATTERN_IGNORE = qr{(lib\/(agavi|ext|doctrine|jsgettext|jit|phing|.+api)|app\/(cache)|git|plugins\/|pub\/)};

parseFiles($BASE, $CAT);

cp("%d translations found", scalar(keys(%{$CAT})));

buildPo($CAT);

cp ("%s created", $POFILE);

exit (0);

sub cp {
	my $f = shift;
	my @l = @_;
	
	# Mask undef values
	for (my $k=0; $k < scalar(@l) ; $k++) {
		if (!defined $l[$k]) {
			$l[$k] = '<undef>';
		}
	}
	
	printf($PROGNAME. ': '. $f. chr(10), @l);
}

sub buildPo {
	my %cat = %{$_[0]};
	my (@poa);
	push (@poa, new Locale::PO(-msgid=>'', -msgstr=>
                'Project-Id-Version: icinga-web-translation 0.0\n' .
                'PO-Revision-Date: YEAR-MO-DA HO:MI +ZONE\n' .
                'Last-Translator: icinga developer team <translation@icinga.org>\n' .
                'Language-Team: icinga developer team <translation@icinga.org>\n' .
                'MIME-Version: 1.0\n' .
                'Content-Type: text/plain; charset=UTF-8\n' .
                'Content-Transfer-Encoding: 8-bit\n'));
	
	foreach my $id (sort(keys(%cat))) {
		my $npo = new Locale::PO(-msgid=>$id, -msgstr=>'');
		my $oca;
		while (my ($file, $lh) = each(%{ $cat{$id} })) {
			$oca .= $file;
			while (my ($line, $na) = each(%{ $lh })) {
				$oca .= ', line '. $line
				. ' ('. join(', ', @{$na}). ')';
			}
			$oca .= chr(10);
		} 
		chomp($oca);
		$npo->comment($oca);
		push (@poa, $npo);
	}
	
	Locale::PO->save_file_fromarray($POFILE,\@poa);
}

sub parseFiles {
	my ($dir, $cat) = @_;
	$dir .= '/' unless ($dir =~ m/\/$/);
	return unless (-d $dir);
	foreach my $i (glob($dir. '*')) {
		if ($i =~ m/$PATTERN_IGNORE/) {
			next;
		}
		elsif (-d $i) {
			parseFiles($i, $cat);
		}
		elsif ($i =~ m/$PATTERN_MATCH/) {
			parseFile($i, $cat);
		}
	}
}

sub parseFile {
	my ($file, $cat) = @_;
	open F, '<', $file;
	my $i = 0;
	foreach my $c (<F>) { 
		# |(loc:\s+)([\d\w\-\_]+)
		# ([\"\'])[^(\g{1})]+\g{1}
		$i++;
		pos($c) = 1;
		while ($c =~ /_\(([\"\'])(.*?)(?<!\\)\1|#loc:\s(.+)/gc) {
			my $val = $2 || $3;
			$val =~ s/\\([\"\'])/$1/g;
			# cp ("%s (file: %s, line %d, pos %d)", $val, basename($file), $i, pos($c));
			push(@{ $cat->{$val}->{basename($file)}->{$i} }, pos($c));
		}
	}
	
	close F;
}

1;
