<?php
include('Net/SFTP.php');

$sftp = new Net_SFTP('sftp.kronostm.com');
if (!$sftp->login('sftp_ulta_post', 'W56@x%22')) {
    exit('Login Failed');
}

if($sftp->get("/home/outgoing/postingextract/active/postings_ULTA_ULTAKTMDReqExt.xml", "xml.php")) {
    print "File downloaded";
} else {
    print "<br />Download failed: " . $ftp->error;
}

?>
