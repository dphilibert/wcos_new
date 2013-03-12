<?php
include 'libwiris.php';
$config = parse_ini_file(WRS_CONFIG_FILE);
?>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
		<script type="text/javascript" src="../core/editor.js"></script>
		<title>WIRIS Formula Editor</title>
	</head>
	<body topmargin="0" leftmargin="0" marginwidth="0" marginheight="0">
		<table width="100%" height="100%">
			<tr height="100%">
				<td>
					<applet id="applet" codebase="<?php echo $config['wirisformulaeditorcodebase']; ?>" archive="<?php echo $config['wirisformulaeditorarchive']; ?>" code="<?php echo $config['wirisformulaeditorcode']; ?>" height="100%" width="100%">
						<param name="lang" value="<?php echo $config['wirisformulaeditorlang']; ?>" />
						<param name="menuBar" value="false"/>
					</applet>
				</td>
			</tr>
			<tr>
				<td>
					<input type="button" id="submit" value="Accept" />
					<input type="button" id="cancel" value="Cancel" />
				</td>
			</tr>
		</table>
	</body>
</html>