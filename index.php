<?php

	function create_site_start()
	{
		echo '<html>';
		echo '<head>';
		echo '<title>Shorturl</title>';
		echo '<meta http-equiv="Content-Type" '
			. 'content="text/html; charset=utf8">';
		echo '<link rel="stylesheet" type="text/css" href="index.css">';
		echo '</head>';
		echo '<body>';
	}

	function create_site_end()
	{
		echo '</body>';
		echo '</html>';
	}

	function create_bottom_info()
	{
		echo '<div id="div-bottom">';
		echo 'Author: Aleksi Räsänen <aleksi.rasanen@runosydan.net>';
		echo '</div>';
	}

	$file = 'urls.txt';

	// User added new link
	if( isset( $_POST['url'] ) )
	{
		$last = '0';

		// Read last ID
		if( file_exists( $file ) )
		{
			$data = file( $file );
			$num = count( $data );
			$last = $data[$num-1];

			// Get last ID
			$arr = explode( '|', $last );

			if( isset( $arr[1] ) )
				$last = $arr[1];
		}

		$last++;

		// Write new url to file
		$fh = fopen( $file, 'a' );
		$line = '|' . $last . '|';
		$line .= $_POST['url'] . '|';
		$line .= date( 'Y-m-d H:i:s' );
		$line .= "\n";

		fwrite( $fh, $line );
		fclose( $fh );

		create_site_start();
		echo '<table width="100%" height="100%">';
		echo '<tr height="95%"><td>';
		echo '<div id="div-response">';
		echo 'Your URL: <a href="' . $_SERVER['PHP_SELF'] . '?id=' . $last 
			. '">' . $_SERVER['PHP_SELF'] . '?id=' . $last . '</a>';
		echo '</div>';
		echo '</td></tr>';
		echo '<tr><td>';
		create_bottom_info();
		echo '</td></tr>';
		echo '</table>'; 
		create_site_end();
		
	}
	else
	{
		// If we have got ID, then we forward user
		if( isset( $_GET['id'] ) )
		{
			$data = file( $file );
			$search = '|' . $_GET['id'] . '|';

			foreach( $data as $cur )
			{
				if( strstr( $cur, $search ) != false )
				{
					$tmp = explode( '|', $cur );
					$url = $tmp[2];
					header( 'Location: ' . $url );
				}
			}
		}

		// Create adding form
		create_site_start();
		echo '<table width="100%" height="100%">';
		echo '<tr height="95%"><td>';
		echo '<div id="div-add_form">';
		echo '<form action="' . $_SERVER['self'] . '" method="post">';
		echo '<h3>Give URL to shorten</h3>';
		echo '<input type="text" name="url" value="http://">';
		echo '<input type="submit" value="Get URL">';
		echo '</div>';
		echo '</form>';
		echo '</td></tr>';
		echo '<tr><td>';
		create_bottom_info();
		echo '</td></tr>';
		echo '</table>';
		create_site_end();
	}
?>
