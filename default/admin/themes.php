<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

    use fruithost\Accounting\Auth;
    use fruithost\Localization\I18N;

    $template->header();
	
	if(!Auth::hasPermission('THEMES::VIEW')) {
		?>
			<div class="alert alert-danger mt-4" role="alert">
				<strong><?php I18N::__('Access denied!'); ?></strong>
				<p class="pb-0 mb-0"><?php I18N::__('You have no permissions for this page.'); ?></p>
			</div>
		<?php
		$template->footer();
		exit();
	}
	?>
    <form method="post" action="<?php print $this->url('/admin/themes' . (!empty($tab) ? '/' . $tab : '')); ?>">
        <header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16'%3E%3Cpath d='M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
                    <li class="breadcrumb-item active" aria-current="page">
                        <a class="active" href="<?php print $this->url('/admin/themes'); ?>"><?php I18N::__('Themes'); ?></a>
                    </li>
                </ol>
            </nav>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group mr-2">
                    <button type="submit" name="action" value="upgrade" class="btn btn-sm btn-outline-success"><?php I18N::__('Upgrade'); ?></button>
                    <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger"><?php I18N::__('Delete'); ?></button>
                </div>
            </div>
        </header>
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link<?php print (empty($tab) ? ' active' : ''); ?>" id="globals-tab" href="<?php print $this->url('/admin/themes'); ?>" role="tab">
					<?php
						I18N::__('Installed Themes');
					?>
                </a>
            </li>
        </ul>
		<?php
			if(isset($error)) {
				?>
                    <div class="alert alert-danger mt-4" role="alert"><?php print $error; ?></div>
				<?php
			} else if(isset($success)) {
				?>
                    <div class="alert alert-success mt-4" role="alert"><?php print $success; ?></div>
				<?php
			}
		?>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane show active" id="globals" role="tabpanel" aria-labelledby="globals-tab">
				<?php
					switch($tab) {
						default:
							$template->display('admin/themes/empty');
						break;
					}
				?>
            </div>
        </div>
    </form>
    <?php
	$template->footer();
?>