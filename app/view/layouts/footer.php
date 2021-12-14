	</main>

	<footer class="footer">
		<?php
			$year = date('Y');

			if ($year > 2021)
			{
				$year = '2021-' . $year;
			}
		?>

		Copyright &copy; <?= $year; ?>
	</footer>

</body>
</html>