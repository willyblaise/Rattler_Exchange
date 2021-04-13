#! c:/strawberry/perl/bin/perl
#
# rszim.cgi
#
# Resizing images, display and make a zip file.
# �C���[�W�t�@�C�������T�C�Y���ĕ\���Azip�쐬
#
# 1.001 : 11/2/05 : �G���[�������C���B�T�[�o�[�Ɏc�����Â��t�@�C���������폜�B
# 1.0 : 10/23/05 : Created
#
# $Id: rszim.cgi,v 1.2 2005/11/23 05:44:59 Hideki Kanayama Exp $

use strict;
use CGI qw(:cgi-lib);
use CGI::Carp qw(fatalsToBrowser);
use GD;
use File::Basename;
use File::Copy;
use Archive::Zip;
use Cwd;

my $lastupdatedyear = 2005;
my $version = "1.001";

my $script = basename($0);

my $charset = "Shift_JIS";
my $lang = 1;

###
my $back = "../rszim.html";

my $body_width = 100;
my $title = 'Resize Image';
my $maxline = 10;
my $bgcolor = 'white';

my $imagedir = '.';

my $prefix = 'rszim';
my $zipfile = "$prefix$$.zip";
my $zipdir = cwd();
my $expire = 60;

my $default_pw = 100;
my $default_ph = 100;
my $default_rw = 50;
my $default_rh = 50;
my $default_ar = 50;
###

my $q = CGI->new;
my $cgierror = $q->cgi_error;
&error($cgierror) if ($cgierror);
my %in = $q->Vars;

if ($in{mode} eq 'resize'){
    &resize;
} elsif ($in{mode} eq 'makeimage'){
    &makeimage;
} elsif ($in{mode} eq 'delete_image'){
    &delete_image;
} elsif ($in{mode} eq 'makezip'){
    &makezip;
} else {
    &idlepage;
}

sub idlepage {
    &delete_zip;
    &delete_image_auto;
    &htmlhead($title);

    print "<center>\n";
    print "<p>";
    my $backname = ("�߂�","back")[$lang];
    print qq(<a href="$back">$backname</a>\n);
    print "<p>\n";
    print (("�摜�t�@�C������͂��Ă��������B<br>�T�|�[�g����Ă���t�@�C����jpeg, gif, png, gd, gd2�ł��B","Please enter your image files. <br>The supported files are jpeg, gif, png, gd and gd2.")[$lang]);

    print "<p>\n";
    print qq(<form name="inform" action="$script" method=post enctype="multipart/form-data">\n);
    my $i;
    for ($i=0;$i<$maxline;$i++){
	print qq(<input type=file size=40 name="$i"><br>\n);
    }
    print qq(<br>);

    print qq(<table cols=1 width="10%" border=0 align=center>\n);
    print qq(<tr><td nowrap>\n);
    print qq(<input type=radio name=target_type value=xy_pixel checked> \n);
    print (('��','Width pixel')[$lang]);
    print qq(<input type=text size=5 name=target_pw value=$default_pw>pixel \n);
    print (('����','Height pixel')[$lang]);
    print qq(<input type=text size=5 name=target_ph value=$default_ph>pixel<br>\n);

    print qq(<input type=radio name=target_type value=xy_ratio> \n);
    print (('���ϊ���','Width ratio')[$lang]);
    print qq(<input type=text size=5 name=target_rw value=$default_rw>% \n);
    print (('�����ϊ���','Height ratio')[$lang]);
    print qq(<input type=text size=5 name=target_rh value=$default_rh>%<br>\n);

    print qq(<input type=radio name=target_type value=area_ratio> \n);
    print (('�ʐϕϊ���','Area ratio')[$lang]);
    print qq(<input type=text size=5 name=target_rarea value=$default_ar>%<br>\n);

    print qq(<br><input type="submit" name="go" value=\");
    print (('�ϊ�����','Convert')[$lang]);
    print qq(\" >\n);
    print qq(</td></tr>\n);
    print qq(</table>\n);
    print "</center>\n";
    print qq(<input type=hidden name=mode value=resize>);
    print qq(</form>\n);
    &htmltail;
}

sub resize {
    my $i;
    my $target_type = $in{target_type};
    if ($target_type ne 'xy_pixel' and 
	$target_type ne 'xy_ratio' and
	$target_type ne 'area_ratio'){
	&error((('�ϊ����@��I�����Ă��������B','Please select conversion type')[$lang]));
    }

    &htmlhead($title);
    print "<center>";
    print (("�ۑ��͉摜���N���b�N���ĕʃE�B���h�E�ŕۑ����邩�A�E�N���b�N�Ń��j���[����ۑ����Ă��������B","Please save photo with right click on a image, or simply click the image and do it on a separate window.")[$lang]);
    print "<p>\n";
    print (("�T�[�o�[��ɕϊ���̉摜�t�@�C�����쐬����Ă���̂Ōʂɏ����ꍇ�́u���̃t�@�C�����T�[�o�[����폜����v���N���b�N���Ă��������B�܂��܂Ƃ߂đS�������ꍇ�͂��̃y�[�W��ԉ��ɂ���u�ϊ���̑S�Ẳ摜���T�[�o�ォ��폜����v���N���b�N���Ă��������B�ϊ���̃t�@�C�������o���Ă���Όォ��$script?mode=delete_image&images=filename,filename,...�ō폜�ł��܂��B","The converted files are generated in the server. If you want to delete them from the server individually, please click \"Delete this file on the server\". If you want to delete all of them at once, please click \"Delete all converted files from the server\" link. If you remember file names, you can delete the files by $script?mode=delete_image&images=filename,filename,...")[$lang]);
    print "<p>\n";
    print (("�܂Ƃ߂ăA�[�J�C�u���ꂽ.zip�Ń_�E�����[�h����ꍇ�́A�摜���폜����O�ɂ��̃y�[�W��ԉ��́u�܂Ƃ߂�zip file�Ń_�E�����[�h����v���N���b�N���Ă��������B","If you want to download with a .zip file, please click \"Download as a zip file\" in the bottom of this page before deleting the images.")[$lang]);
    print "<br>\n";
    &backbutton;
    print "</center>";

    my @new_imagefiles;
    for ($i=0;$i<$maxline;$i++){
	next unless ($in{$i});
	my $imagefile = $q->upload($i);
	my $cgierror = $q->cgi_error;
	&error($cgierror) if (!$imagefile && $cgierror);

	my $image = basename($in{$i});
	$image =~ s/^.+[\/\\]([^\/\\]+)$/$1/; #just in case
	my ($body,$path,$suffix) = fileparse("$image",'\.\w+');

	my $im;
	my $image_type;
	my $target_type = $q->param('target_type');
	if ($suffix =~ /\.jpe?g$/i){
	    $im = GD::Image->newFromJpeg($imagefile);
	    $image_type = 'jpg';
	} elsif ($suffix =~ /\.gif$/i) {
	    $im = GD::Image->newFromGif($imagefile);
	    $image_type = 'gif';
	} elsif ($suffix =~ /\.png$/i) {
	    $im = GD::Image->newFromPng($imagefile);
	    $image_type = 'png';
	} elsif ($suffix =~ /\.gd$/i) {
	    $im = GD::Image->newFromgd($imagefile);
	    $image_type = 'gd';
	} elsif ($suffix =~ /\.gd2$/i) {
	    $im = GD::Image->newFromgd2($imagefile);
	    $image_type = 'gd2';
	} else {
	    close($imagefile) if ($imagefile);
	    print (("<center>$suffix�̓T�|�[�g����Ă��܂���B</center>","<center>$suffix is not supported</center>")[$lang]);
	    exit;
	}
	unless ($im) {
	    close($imagefile) if ($imagefile);
	    print "<center>$imagefile������ɊJ���܂���</center>";
	    exit;
	}
	close($imagefile);
	my ($width, $height) = $im->getBounds();
	my ($new_width, $new_height);
	if ($target_type eq 'xy_pixel'){
	    ($new_width, $new_height) = (int($in{target_pw}), int($in{target_ph}));
	} elsif ($target_type eq 'xy_ratio'){
	    ($new_width, $new_height) = (int($width*$in{target_rw}/100), int($height*$in{target_rh}/100));
	} elsif ($target_type eq 'area_ratio'){
	    ($new_width, $new_height) = (int($width*sqrt($in{target_rarea}/100)), int($height*sqrt($in{target_rarea}/100)));
	} else {
	    print (("�����ȃT�C�Y�I���ł��B","Invalid size selectoin")[$lang]);
	    exit;
	}

	my $new_image_body =  lc($body) . "_${new_width}x${new_height}" . lc($suffix);
	my $new_image = "$imagedir/$new_image_body";

	push(@new_imagefiles,$new_image_body);

	my $target_im = new GD::Image($new_width,$new_height);
	$target_im->copyResized($im,0,0,0,0,$new_width,$new_height,
				$width,$height);
	
	unless (open(IMAGE, "> $new_image")){
	    print (("�e���|�����t�@�C���쐬�Ɏ��s���܂����B",'Failed to create a temporary file')[$lang]);
	    exit;
	}
	binmode(IMAGE);
	if ($suffix =~ /\.jpe?g$/i){
	    print IMAGE $target_im->jpeg();
	} elsif ($suffix =~ /\.gif$/i) {
	    print IMAGE $target_im->gif();
	} elsif ($suffix =~ /\.png$/i) {
	    print IMAGE $target_im->png();
	} elsif ($suffix =~ /\.gd$/i) {
	    print IMAGE $target_im->gd();
	} elsif ($suffix =~ /\.gd2$/i) {
	    print IMAGE $target_im->gd2();
	}
	close(IMAGE);
	chmod (0666,$new_image);

	print "<center>\n";
	print "$new_image_body<br>\n";
	print "width=$new_width, height=$new_height<br>\n";
	my $dellink_mes = ("���̃t�@�C�����T�[�o�[����폜����","Delete this image file from the server")[$lang];
	print qq(<a href="$script?mode=delete_image&images=$new_image_body">$dellink_mes</a><br>\n);
	print qq(<a href="$new_image" target="_blank"><img src="$script?mode=makeimage&image=$new_image_body" border=0></a><p>\n);
	print "</center>\n";
    }

    my $new_imagefiles = join ",", @new_imagefiles;

    my $delmes = ("�ϊ���̑S�Ẳ摜���T�[�o�ォ��폜����","Delete all converted images from server")[$lang];
    print "<center>";
    print qq(<a href="$script?mode=delete_image&images=$new_imagefiles">$delmes</a><p>\n);
    my $archive_mes = ("�܂Ƃ߂�zip file�Ń_�E�����[�h����","Download as a zip file")[$lang];
    print qq(<a href="$script?mode=makezip&images=$new_imagefiles">$archive_mes</a><br>\n);
    &backbutton;
    print "</center>";
    &htmltail;
}

sub makezip {

    my $zip = Archive::Zip->new();
    my $member;
    
    chdir($imagedir);
    my $i=0;
    my $eachfile;
    my @images = split /,/, $in{images};
    foreach (@images){
	$member = $zip->addFile("$_");
    }
    
    my $status = $zip->writeToFileNamed("$zipfile");
    if ($status != 'AZ_OK') {
	unlink("$zipfile") if (-e "$zipfile");
	&error(("$zipfile���쐬����܂���","Cannot make $zipfile")[$lang]) 
	}
    rename "$zipfile", "$zipdir/$zipfile";
    chdir($zipdir);
    print "Location: $zipfile\n\n";
    
}

sub delete_zip {
    opendir(ZIPDIR, "$zipdir") or &error(("�f�B���N�g��$zipdir���J���܂���","Cannot open $zipdir")[$lang]);
    my @ziplist = grep /^$prefix.*\.zip$/, readdir ZIPDIR;
    closedir(ZIPDIR);
    
    my $zipfile;
    my $now = time;
    foreach $zipfile (@ziplist){
	my ($d_dev,$d_ino,$d_mode,$d_nlink,$d_uid,$d_gid,$d_rdev,$d_size,$d_atime,$d_mtime,$d_ctime,$d_blksize,$d_blocks)=stat("$zipdir/$zipfile");
	if ($now > $d_mtime + $expire * 60){
	    unlink("$zipdir/$zipfile");
	}
    }
}

sub delete_image_auto {
    opendir(IMGDIR, "$imagedir") or &error(("�f�B���N�g��$imagedir���J���܂���","Cannot open $imagedir")[$lang]);
    my @all = grep !/^\./, readdir IMGDIR;
    closedir(IMGDIR);
    my @imglist =  grep /\.jpe?g$/i, @all;
    @imglist = (@imglist, grep /\.gif$/i, @all);
    @imglist = (@imglist, grep /\.png/i, @all);
    @imglist = (@imglist, grep /\.gd$/i,  @all);
    @imglist = (@imglist, grep /\.gd2$/i, @all);
    
    my $imgfile;
    my $now = time;
    foreach $imgfile (@imglist){
	my ($d_dev,$d_ino,$d_mode,$d_nlink,$d_uid,$d_gid,$d_rdev,$d_size,$d_atime,$d_mtime,$d_ctime,$d_blksize,$d_blocks)=stat("$imagedir/$imgfile");
	if ($now > $d_mtime + $expire * 60){
	    unlink("$imagedir/$imgfile");
	}
    }
}

sub delete_image {
    my @images = split /,/, $in{images};
    my @deleted;
    foreach (@images){
	if (-e "$imagedir/$_") {
	    unlink "$imagedir/$_";
	    push (@deleted,$_);
	}
    }
    print $q->header(-charset=>$charset);
    print "<center>";
    &backbutton;
    print "<p>";
    my $deleted = join ',', @deleted;
    if ($#deleted > -1) {
	print (("$deleted���폜���܂����B","Deleted $deleted.")[$lang]);
    } else {
	print (("$in{images}�͑��݂��܂���ł����B","There are no $in{images}.")[$lang]);
    }
    print "<p>";
    &backbutton;
    print "</center>";
    exit;
}

sub makeimage {
    my $imagefile = "$imagedir/$in{image}";
    my $im;
	if ($imagefile =~ /\.jpe?g$/i){
	    $im = GD::Image->newFromJpeg($imagefile);
	    print $q->header(-type=>'image/jpeg');
	    print $im->jpeg();
	} elsif ($imagefile =~ /\.gif$/i) {
	    $im = GD::Image->newFromGif($imagefile);
	    print $q->header(-type=>'image/gif');
	    print $im->gif();
	} elsif ($imagefile =~ /\.png$/i) {
	    $im = GD::Image->newFromPng($imagefile);
	    print $q->header(-type=>'image/png');
	    print $im->png();
	} elsif ($imagefile =~ /\.gd$/i) {
	    $im = GD::Image->newFromgd($imagefile);
	    print $q->header(-type=>'image/gd');
	    print $im->gd();
	} elsif ($imagefile =~ /\.gd2$/i) {
	    $im = GD::Image->newFromgd2($imagefile);
	    print $q->header(-type=>'image/gd2');
	    print $im->gd2();
	} else {
	    $imagefile =~ s/(\.+)$/$1/;
	    &error(("$imagefile�̓T�|�[�g����Ă��܂���B","$imagefile is not supported")[$lang]);
	}

}

sub backbutton {
    my $back = ('�߂�','Back')[$lang];
    print "<form><input type=button value=\"$back\" onClick=\"history.back()\"></form>\n";
}

sub htmlhead {
  
  my $title = shift;
  
  my $bgimage = "bgcolor=\"$bgcolor\"";

  print $q->header(-charset=>$charset);
  print "<html>\n";
  print "<HEAD>\n";
  print "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=$charset\">\n";
  print "<TITLE>$title</TITLE>\n";
  print "</HEAD>\n";
  print "<BODY TEXT=\"#000000\" $bgimage LINK=\"#0000EE\" VLINK=\"#551A8B\" ALINK=\"#FF0000\">\n";
  print "<table cols=1 border=0 width=\"${body_width}%\" style=\"border:0px\" align=center><tr><td style=\"border:0px;background-color:transparent\" >\n";
  
}

sub htmltail {
    my $mysite = ('http://www.hidekik.com/','http://www.hidekik.com/en/')[$lang];
  print "<div align=\"right\"><i>$script Ver. $version</i></div>\n";
  print "<div align=\"right\"><i>Copyright(C) $lastupdatedyear, <a href=\"$mysite\" target=\"_blank\">hidekik.com</a></i></div>\n";
  print "</td></tr></table></body></html>\n";
}

sub error {
    my ($msg) = shift;
    &htmlhead($msg);
    print "<br><center>$msg</center>\n";
    &htmltail;
    exit;
}
