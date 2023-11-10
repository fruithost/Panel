<?php
	use fruithost\Accounting\Auth;

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