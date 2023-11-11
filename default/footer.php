<?php
    use fruithost\Accounting\Auth;
	use fruithost\Localization\I18N;

	if(Auth::isLoggedIn()) {
		?>
				</main>
			</div>
		</div>
		<?php
	}
	?>
	<!-- Theme Switch -->
	<div class="dropdown position-fixed bottom-0 end-0 mb-3 me-3 bd-mode-toggle">
		<button class="btn btn-bd-primary py-2 dropdown-toggle d-flex align-items-center" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" aria-label="<?php I18N::__('Toggle theme'); ?> (<?php I18N::__('Auto'); ?>)">
			<i class="bi-circle-half theme-icon-active"></i>
			<span class="visually-hidden" id="bd-theme-text"><?php I18N::__('Toggle theme'); ?></span>
		</button>
		<ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text">
			<li>
				<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
					<i class="bi-sun-fill me-2 opacity-50 theme-icon"></i>
						<?php I18N::__('Light'); ?>
					<i class="bi-check2 ms-auto d-none"></i>
				</button>
			</li>
			<li>
				<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
					<i class="bi-moon-stars-fill me-2 opacity-50 theme-icon"></i>
						<?php I18N::__('Dark'); ?>
					<i class="bi-check2 ms-auto d-none"></i>
				</button>
			</li>
			<li>
				<button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto" aria-pressed="true">
					<i class="bi-circle-half me-2 opacity-50 theme-icon"></i>
						<?php I18N::__('Auto'); ?>
					<i class="bi-check2 ms-auto d-none"></i>
				</button>
			</li>
		</ul>
	</div>
	<?php
		$template->foot();
	?>
	</body>
</html>