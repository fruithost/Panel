<?php
    use fruithost\Accounting\Auth;
	use fruithost\Localization\I18N;
    use fruithost\UI\Icon;

	if(Auth::isLoggedIn()) {
		?>
						</main>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	?>
	
	<!-- Theme Switch -->
	<div class="theme-switch dropdown position-fixed bottom-0 end-0 mb-3 me-3 bd-mode-toggle">
		<button class="btn border-0 py-2 dropdown-toggle d-flex align-items-center" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" aria-label="<?php I18N::__('Toggle theme'); ?> (<?php I18N::__('Auto'); ?>)">
			<?php
				Icon::show('theme-auto', [
					'classes' 		=> [ 'me-2', 'opacity-50', 'theme-icon' ],
					'attributes'	=> [
						'data-current'	=> 'theme-auto'
					]					
				]);
			?>
			<span class="visually-hidden" id="bd-theme-text"><?php I18N::__('Toggle theme'); ?></span>
		</button>
		<ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text">
			<li>
				<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
					<?php
						Icon::show('theme-light', [
							'classes' 		=> [ 'me-2', 'opacity-50', 'theme-icon' ],
							'attributes'	=> [ 'data-name' => Icon::get('theme-light') ]
						]);
						
						I18N::__('Light');
						
						Icon::show('check', [
							'classes' 		=> [ 'ms-auto', 'd-none' ]
						]);
					?>
				</button>
			</li>
			<li>
				<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
					<?php
						Icon::show('theme-dark', [
							'classes' 		=> [ 'me-2', 'opacity-50', 'theme-icon' ],
							'attributes'	=> [ 'data-name' => Icon::get('theme-dark') ]
						]);
						
						I18N::__('Dark');
						
						Icon::show('check', [
							'classes' 		=> [ 'ms-auto', 'd-none' ]
						]);
					?>
				</button>
			</li>
			<li>
				<button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto" aria-pressed="true">
					<?php
						Icon::show('theme-auto', [
							'classes' 		=> [ 'me-2', 'opacity-50', 'theme-icon' ],
							'attributes'	=> [ 'data-name' => Icon::get('theme-auto') ]
						]);
						
						I18N::__('Auto');
						
						Icon::show('check', [
							'classes' 		=> [ 'ms-auto', 'd-none' ]
						]);
					?>
				</button>
			</li>
		</ul>
	</div>
	<?php
		$template->foot();
	?>
	</body>
</html>