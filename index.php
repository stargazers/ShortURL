<?php
	/* 
	ShortURL url shortener
	Copyright (C) 2009 Aleksi Räsänen <aleksi.rasanen@runosydan.net>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/

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
		echo '<div id="div-container">';
	}

	function create_site_end()
	{
		echo '</div>';

		echo '</body>';
		echo '</html>';
	}

	function get_last_items( $file, $num_last_items, $url )
	{
		if( file_exists( $file ) )
		{
			$data = file( $file );
			$num_items = count( $data );

			$counter = 0;
			for( $i=$num_items; $i>0; $i-- )
			{
				$row = explode( '|', $data[$i] );

				// Show ID as an link
				echo '<a href="' . $url . '?id=' . $row[1] . '">';
				echo $row[1];
				echo '</a> ';

				// Show shorter URL if it is too long to
				// fit in one line.
				if( strlen( $row[2] ) > 40 )
					$text = substr( $row[2], 0, 40 ) . '...';
				else
					$text = $row[2];

				echo '<a class="small" href="' . $row[2] . '">' 
					. $text . '</a>';
				echo '<br>';

				if( $counter == $num_last_items )
					break;

				$counter++;
			}
		}
	}

	$file = 'urls.txt';
	$url = 'http://s.runosydan.net/';
	$num_last_items = 5;


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
		$fh = @fopen( $file, 'a' );

		// Cannot open file for writing, show error
		if(! $fh )
		{
			create_site_start();
			echo '<table width="100%" height="100%">';
			echo '<tr><td>';
			echo '<div id="div-error">';
			echo 'Error! Cannot open file "' . $file . '" for writing.';
			echo '</div>';
			echo '</td></tr>';
			echo '</table>';
			create_site_end();
			die();
		}

		$line = '|' . $last . '|';

		$line .= $_POST['url'] . '|';
		$line .= date( 'Y-m-d H:i:s' );
		$line .= "\n";

		fwrite( $fh, $line );
		fclose( $fh );

		create_site_start();
		echo '<div id="div-response">';
		echo 'Your URL: <a href="' . $url . '?id=' . $last 
			. '">' . $url . '?id=' . $last . '</a>';
		echo '</div>';

		/*
		echo '<table width="100%" height="100%">';
		echo '<tr height="95%"><td>';
		echo '</td></tr>';
		echo '<tr><td>';
		create_bottom_info();
		echo '</td></tr>';
		echo '</table>'; 
		*/
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

		echo '<div id="div-adding_form">';
		echo '<form action="' . $_SERVER['self'] . '" method="post">';
		echo '<h3>Give URL to shorten</h3>';
		echo '<input type="text" name="url" value="http://">';
		echo '<br>';
		echo '<input type="submit" value="Get URL">';
		echo '</form>';
		echo '</div>';

		echo '<div id="div-last">';
		echo '<h3>Last URLs</h3>';
		get_last_items( $file, $num_last_items, $url );
		echo '</div>';
		create_site_end();
	}
?>
