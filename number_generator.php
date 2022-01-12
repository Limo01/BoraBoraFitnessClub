<?php
	$n = $_GET['n'];

	if (rand(0,1) == 0) {
		$add = rand(1,10);

		if ($n + $add <= 200) {
			echo $n + $add;
		}
		else {
			echo 200;
		}
	}
	else {
		$add = rand(1,10);

		if ($n - $add >= 0) {
			echo $n - $add;
		}
		else {
			echo 0;
		}
	}
?>