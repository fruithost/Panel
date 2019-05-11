<?php
	use fruithost\Auth;

	if(Auth::isLoggedIn()) {
		?>
				</main>
			</div>
		</div>
		<?php
	}
			
	$template->foot();
	?>
	</body>
</html>