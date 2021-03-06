--TEST--
mkdir-torture.tar
--SKIPIF--
<?php
    if (!extension_loaded("archive")) print "skip";
    if (!defined('ARCH_COMPRESSION_BZIP2')) print "skip bzip2 uncompression in not supported";
?>
--FILE--
<?php

$ar = new ArchiveReader(dirname(__FILE__)."/../_files/mkdir-torture.tar.bz2");

while ($e = $ar->getNextEntry(true)) {
	var_dump($e);
	var_dump($e->isDir());
	var_dump($e->isFile());
	var_dump($e->isLink());
	var_dump($e->getPathname());
	var_dump($e->getResolvedPathname());
	var_dump($e->getUser());
	var_dump($e->getGroup());
	var_dump($e->getMtime());
	var_dump($e->getSize());
	var_dump($e->getPerms());
	var_dump($e->getData());
}

echo "Done\n";
?>
--EXPECTF--
object(ArchiveEntry)#%d (1) {
  ["entry"]=>
  resource(%d) of type (archive entry descriptor)
}
bool(false)
bool(true)
bool(false)
string(97) "usr/./local/lib/../share/./ImageMagick/.././doc/ghostscript/7.06/./../7.06/pcl3/how-to-report.txt"
bool(false)
string(4) "root"
string(5) "wheel"
int(1053769444)
int(12890)
int(33060)
string(12890) "*******************************************************************************
  File:     @(#)%s
  Contents: How to report bugs and hardware compatibility for pcl3
  Author:   Martin Lottermoser, Greifswaldstrasse 28, 38124 Braunschweig,
            Germany. E-mail: Martin.Lottermoser@t-online.de.

*******************************************************************************
*									      *
*	Copyright (C) 2000, 2001 by Martin Lottermoser			      *
*	All rights reserved						      *
*									      *
*******************************************************************************


Bugs and Failures
*****************
This document distinguishes between "bugs" and "failures". A bug is incorrect
behaviour on the part of pcl3, a failure is described by a set of
hardware-relevant parameter values which are not accepted by a particular
printer. Most often a failure is due to hardware limitations in the printer,
not to a problem in pcl3, although it might be difficult to find out which is
the case. For the user, this distinction is fortunately irrelevant.


Bugs
****
The basic rule is that it's a bug if there is disagreement between the
behaviour and the documentation (the bug might be in the documentation, though).
For example, if the documentation states that pcl3 produces a core dump under
certain circumstances, this is by definition not a bug, which means that you
should not report it (I'll have to fix it nevertheless, of course).

If your printer has a specific subdevice in pcl3 (e.g., if you have a
Hewlett-Packard DeskJet 540, this is the "hpdj540" subdevice) and the result
when using this subdevice is not accepted by the printer or there is printer
functionality you cannot access through this subdevice, this is a bug.

If you find a bug in pcl3, please report it. The format of your message is
irrelevant, but the content should be sufficiently detailed that I can
reproduce the behaviour. You should always specify the versions of gs and pcl3
you used. Please make sure that the behaviour is due to pcl3 and not to other
causes. In particular, if nothing seems to work and you are on a UNIX system,
do the following:

  - Generate the PCL file by calling gs yourself, and print through a
    "raw"/"transparent" queue which does not modify the file.
  - If spooled printing fails, log in as root and try to send the file
    directly to the device file. If it works, the problem is in the spooler.
  - If even that fails, send a line of text (still as superuser) directly
    to the device, followed by a form feed:

      printf 'Hello!\r\n\f' > /dev/...

    If this also does not work, you very likely have a kernel or hardware
    problem.

I am writing this because there have been cases where people complained that my
driver wasn't working at all and where the cause turned out to be, e.g., a
misconfiguration of the spooler. Please don't bother me with problems of this
kind; I have enough to do correcting my own mistakes. However, I only demand
that you take reasonable steps to ensure that it isn't you who has created the
problem. If you can't decide which component is at fault, by all means contact
me -- just don't expect a quick answer and don't start by telling me that my
driver doesn't work :-).



Hardware compatibility reports
******************************
The pcl3 driver is intended to support a large number of printers. Because the
PCL-3 documentation supplied by Hewlett-Packard is incomplete and occasionally
wrong (sometimes obviously so because of inconsistencies), tests must be made
on various printers in order to discover whether the driver can really be used
for a particular printer model and which parameter values one should specify.
As I do not have access to a complete range of PCL-3 printers, I must rely on
others to do this for me.

If you have a PCL-3 printer and are using pcl3, please check the file
reports.txt whether there is already an entry for your printer or not. If there
is none or if the hardware-relevant parameters listed are different from the
ones you used, please compose a report in the format described below and send
it to me. You need not cover all possible combinations; in fact, this might be
too confusing for others anyway. Even a single success report, however, can
help someone else with less knowledge or experience to get started. This is a
service *you* can provide to the community of pcl3 users.



Guidelines for testing
======================
- Read the documentation. Please. :-)

- As test files, use publicly available PostScript files, preferably those
  included in the ghostscript or pcl3 distributions. The file levels-test.ps in
  the latter is a good starting point.

- Keep in mind that some environment variables influence ghostscript's
  behaviour and make sure their values are as intended.

- If you have access to a manufacturer-endorsed driver for your printer, use
  it to generate some test files with various settings, analyse them with
  pcl3opts, and try the suggested option combinations first.

- The basic hardware parameters to test are colour model, resolution, and the
  number of intensity levels.

    - Colour model: The interesting models are Gray, CMY, and CMY+K/CMYK,
      depending on your printer. You need not bother with CMY for a CMY+K or
      CMYK printer unless you discover that the CMY cartridge supports a
      higher resolution than the black cartridge.

    - Resolution: Check your printer's documentation first. It should at least
      list the highest supported resolution, possibly separately for black and
      colour printing. If you don't have access to the documentation, a good
      set to test with is {300, 600x300, 600, 1200}. Smaller resolutions
      ({75, 100, 150}) are sometimes also supported but they are uninteresting
      for serious printing.

      Resolution tests are best made with a file where only the top left corner
      of the sheet is used. It should contain some structure of known extension.
      If a resolution is not accepted, the printer usually prints the picture
      anyway, but at a supported resolution, leading to magnified or reduced
      output.

    - Intensity levels: Test this only if your printer's documentation claims
      that some kind of Resolution Enhancement (e.g., C-REt) is supported or
      pcl3opts tells you that an official driver generates files with this
      property. Unless pcl3opts tells you differently, start with 300 ppi and
      4 levels for all colorants used.

  Whether a particular value for a parameter is supported often depends on
  the values for other parameters. In some cases, even the print quality is
  important. If in doubt, test with "presentation".

- If your printer has special hardware functionality, tests for that are
  also interesting (duplex printing, banner printing, different input or
  output trays). If you are using "unspec" or "unspecold", you should also
  always try compression method 9 unless the pcl3 documentation states that it
  is not supported by your printer.



General rules for reports
=========================
- You can use all characters in ISO 8859-1, plus HT and NL/LF.
- Please keep to the following syntactic rules which simplify converting the
  information into other formats:
  - The report consists of a sequence of fields, each occupying an integral
    number of lines.
  - Each field begins with a field name, starting in column 1 and ending with
    the first following colon (':') in the same line.
  - The content of a field may be continued over several lines provided all
    continuation lines are either empty or indented with blanks (SP or HT).
    Trailing empty lines are ignored.
  - With the exception of "Success" and "Failure", every field may appear at
    most once in a report. If it appears a second time in a file, a new report
    is assumed to start at this point.
- Make your report sufficiently detailed to be reproducible but don't tell us
  your life story.



Special remarks for individual fields
=====================================

- "Name": Supply your full name here. I don't accept anonymous or obviously
  nickname-signed reports.

- "E-mail address" (optional): choose a stable one, your information might be
  used for several years. Omit this field if you do not wish your e-mail
  address to be disclosed to other users of pcl3.

- "Date": I prefer the internationally standardized notation, YYYY-MM-DD.

- "Printer": Be as precise as possible in identifying the printer. Ideally,
  I should like to have the following kind of information (as an example
  I'm giving some values for a DJ 850C):
  - printer name ("HP DeskJet 850C")
  - manufacturer's model identification ("C2145A")
  - firmware revision ("9.20 02/14/95")
  At least some of Hewlett-Packard's printers issue this information when you
  print a diagnostic or self test. If your printer's manual doesn't tell you
  how to generate such a test, try sending the command "ESC z" to the printer
  (on a UNIX system, you can use "printf '\x1bz'" to generate it).

- pcl3opts (optional): Use this field to describe pcl3opts's output if you ran
  pcl3opts on files generated by manufacturer-endorsed drivers and you have
  obtained interesting information. You should always describe the driver you
  used.

  Interesting information is for example that the official driver generated
  files with certain settings for which pcl3opts did not give a warning but
  nevertheless you were not able to print such a file with pcl3. Expect me to
  ask you for a copy of such a file.

- "Media configuration file" (optional): If you used a media configuration file
  and its content was relevant for the test or you wish to make the file
  available to other users, include it or a reference to it here. You must
  state where the information came from, so it can be verified in case of
  discrepancies.

- "Remarks" (optional): Use this, if needed, for global information applying
  to a number of tests (perhaps a summary) or any other relevant information.

- "Success:" and "Failure": These are intended for describing the result of
  using a single option combination and may be repeated any number of times
  (including zero). Each report should be independent of the others so they can
  be sorted differently if needed.

  Keep in mind that the key purpose of a report is to find out which hardware
  parameters work and which don't. It is therefore a success if the driver is
  at least able to correctly tell the printer how to paint shapes at the level
  of the printer's hardware capabilities (resolution, colorants and
  intensities). A good test file is levels-test.ps which has been designed for
  this purpose. I don't count it a failure when the result of printing a
  photograph or a similarly demanding document is not as aesthetically pleasing
  as it should be or when a problem is due to a bug in ghostscript. Use
  comments for such statements if desired. However, the decision whether an
  entry is a success or a failure remains yours to make, not mine.

  These fields should start with the list of options given to ghostscript via
  the command line, including the "-sDEVICE" option. You should omit options
  which are obviously irrelevant for the outcome of the test (like "-dNOPAUSE"
  or "-sOutputFile"), but otherwise the option list must be complete. If you
  wish to add a comment (and for a failure you must always do that), terminate
  this list with a period followed by a newline and add the comment after that.
  Here's an example:

    Failure: -sDEVICE=pcl3 -r1200 -sColourModel=CMYK
      -sPrintQuality=best -sMedium=transparency.
      Printed at twice the size intended.

  The option list terminates immediately after "transparency" in this case.
  Note that you don't need backslashes here if you break the command line
  into several text lines.

  If you did something interesting in the PostScript file (like setting page
  device parameters to unusual values), append the PostScript code in the
  comment part or in the "Remarks" field.

*****************<Start of form; cut here>*************************************

Name: [your full name]
E-mail address: [optional]
Date: [YYYY-MM-DD]
Printer: [at least manufacturer and model]
Ghostscript version: [GNU or AFPL, version number]
pcl3 version: [version number]
pcl3opts: [optional; information on printer capabilities obtained by running
  pcl3opts on other drivers' output files]
Media configuration file: [optional]
Remarks: [optional; use, e.g., for global comments or a summary]
Success: [options used, repeat any number of times]
Failure: [options used, repeat any number of times. Don't forget to give your
  reason for classifying this as a failure (after terminating the options with
  a period followed by a newline).]
"
Done
