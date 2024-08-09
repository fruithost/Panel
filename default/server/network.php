<?php
    use fruithost\Localization\I18N;
    use fruithost\Accounting\Auth;
    use fruithost\UI\Icon;
    use fruithost\Hardware\NetworkState;

    $template->header();
	
	if(!Auth::hasPermission('SERVER::VIEW')) {
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
    <header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16'%3E%3Cpath d='M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
                <li class="breadcrumb-item active" aria-current="page">
                    <a href="<?php print $this->url('/server/network'); ?>"><?php I18N::__('Network'); ?></a>
                </li>
            </ol>
        </nav>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="<?php print $this->url('/server/network'); ?>" class="btn btn-sm btn-outline-primary"><?php I18N::__('Refresh'); ?></a>
        </div>
    </header>
    <div class="container-fluid mt-5">
        <div class="row">
            <?php
                foreach($network->getDevices() AS $entry) {
                    if(!$entry->isTunnel() && !$entry->isVirtual() && $entry->getType() == 'ether') {
                        ?>
                            <div class="col card text-break p-0 m-2">
                                <div class="card-header d-inline-flex flex-row p-0">
                                    <?php
                                        $status = 'text-light';

                                        switch($entry->getState()) {
                                            case NetworkState::UP:
                                                $status = 'text-success';
                                            break;
                                            case NetworkState::DOWN:
                                                $status = 'text-danger';
                                            break;
                                            case NetworkState::UNKNOWN:
                                                $status = 'text-warning';
                                            break;
                                        }

                                        Icon::show('network', [
                                            'classes' 		=> [ 'align-self-start', 'flex-shrink-1', 'bg-dark', 'rounded', $status, 'p-2', 'm-2' ],
                                            'attributes'	=> [ 'style' => 'font-size: 30px' ],
                                            'data-current'	=> 'theme-auto'
                                        ]);
                                    ?>
                                    <span class="align-self-start flex-fill p-0 ml-2 mt-1">
                                        <small><?php I18N::__('Network Interface'); ?></small>
                                        <h4 class="p-0 m-o"><?php print $entry->getID(); ?></h4>
                                    </span>
                                </div>
                                <div class="card-body p-1 text-center">
                                    <a class="icon-link icon-link-hover text-decoration-none" style="--bs-link-hover-color-rgb: 25, 135, 84;" href="<?php print $template->url('/server/network/' . $entry->getID() . '/info'); ?>">
                                        <?php
                                            Icon::show('info', [
                                                'classes' => [ 'mb-2' ]
                                            ]);

                                            printf('<span>%s</span>', I18N::get('Details'));
                                        ?>
                                    </a> |
                                    <a class="icon-link icon-link-hover text-decoration-none" style="--bs-link-hover-color-rgb: 25, 135, 84;" href="<?php print $template->url('/server/network/' . $entry->getID() . '/stop'); ?>">
                                        <?php
											switch($entry->getState()) {
												case NetworkState::UP:
													Icon::show('network', [
														'classes' => [ 'mb-2', 'text-danger' ]
													]);

													printf('<span class="text-danger">%s</span>', I18N::get('Stop'));
												break;
												case NetworkState::UNKNOWN:
												case NetworkState::DOWN:
													Icon::show('network', [
														'classes' => [ 'mb-2', 'text-success' ]
													]);

													printf('<span class="text-success">%s</span>', I18N::get('Start'));
												break;
											}
                                        ?>
                                    </a>
                                </div>
                            </div>
                        <?php
                    }
                }
            ?>
        </div>
		<div class="col">
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
        </div>
        <hr />
        <pre>
        <?php
            if($action == 'info') {
               print_r($device->debug);
            }
        ?></pre>
    </div>
    <?php

	#$link = json_decode(`lshw -C network -json`);
	

	#print "<pre>";
	#print_r(`lshw -C network -json`);
	#print_r(json_decode(`ip --json address show`));
	#print_r(json_decode(`ip --json link show`));
	#print_r(json_decode(`ip --json route show`));
	
	$this->footer();
?>