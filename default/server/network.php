<?php
    use fruithost\Localization\I18N;
    use fruithost\Accounting\Auth;
    use fruithost\UI\Icon;
    use fruithost\Hardware\NetworkState;
    use fruithost\Hardware\NetworkFamily;
    use fruithost\Hardware\NetworkFlag;
    use fruithost\Hardware\NetworkAddress;

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
                if(!$network->hasDevices()) {
					?>
                        <div class="jumbotron text-center bg-transparent text-muted">
                            <?php Icon::show('networking'); ?>
                            <h2><?php I18N::__('No Device available!'); ?></h2>
                            <p class="lead"><?php I18N::__('We can\'t find some network devices.'); ?></p>
                        </div>
					<?php
				} else {
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
    <?php
        if($network->hasDevices()) {
	    ?>
         <hr />
            <?php
                if($action == 'info') {
                   ?>
                    <div class="container-fluid mt-5">
                        <div class="row">
                            <div cla<div class="col-6">
                                <div class="card">
                                    <div class="card-header d-flex flex-row">
                                        <?php I18N::__('Configuration'); ?>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table m-0 table-borderless table-striped">
                                            <tr>
                                                <td>
                                                    <strong>ID</strong>
                                                </td>
                                                <td>
                                                    <?php print $device->getID(); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>Type</strong>
                                                </td>
                                                <td>
                                                    <?php print $device->getType(); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>Address</strong>
                                                </td>
                                                <td>
                                                    <?php print $device->getAddress(); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>Broadcast</strong>
                                                </td>
                                                <td>
                                                    <?php print $device->getBroadcast(); ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header d-flex flex-row">
                                        <?php I18N::__('Properties'); ?>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table m-0 table-borderless table-striped">
                                            <tr>
                                                <td>
                                                    <strong><?php I18N::__('Flags'); ?></strong>
                                                </td>
                                                <td>
                                                    <?php
                                                        foreach($device->getFlags() AS $flag) {
                                                            $color = 'light';

                                                            switch($flag) {
                                                                case NetworkFlag::UP:
                                                                    $color = 'success';
                                                                break;
                                                                case NetworkFlag::DEBUG:
                                                                    $color = 'danger';
                                                                break;
                                                                case NetworkFlag::DORMANT:
                                                                    $color = 'warning';
                                                                break;
                                                            }
                                                            printf('<span class="badge m-1 text-bg-%s">%s</span>', $color, NetworkFlag::for($flag));
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>Virtual</strong>
                                                </td>
                                                <td>
                                                    <?php print ($device->isVirtual() ? I18N::get('Yes') : I18N::get('No')); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>Tunnel</strong>
                                                </td>
                                                <td>
                                                    <?php print ($device->isTunnel() ? I18N::get('Yes') : I18N::get('No')); ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="container-fluid mt-5">
                            <div class="row p-0">
                                <?php
                                    if($device->hasAddresses()) {
                                        ?>
                                            <div class="border rounded overflow-hidden mb-5 p-0">
                                                <table class="table table-borderless table-striped table-hover m-0 p-0">
                                                    <thead>
                                                    <tr>
                                                        <th class="bg-secondary-subtle" scope="col" style="width: 20px;"><?php I18N::__('Type'); ?></th>
                                                        <th class="bg-secondary-subtle" scope="col"><?php I18N::__('Name'); ?></th>
                                                        <th class="bg-secondary-subtle" scope="col"><?php I18N::__('Address'); ?></th>
                                                        <th class="bg-secondary-subtle" scope="col"><?php I18N::__('Broadcast'); ?></th>
                                                        <th class="bg-secondary-subtle" scope="col" style="width: 20px;"><?php I18N::__('TTL'); ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                        foreach($device->getAddresses() AS $entry) {
                                                            $color = 'light';

                                                            switch($entry->getFamily()) {
                                                                case NetworkFamily::IPV4:
                                                                    $color = 'danger';
                                                                break;
                                                                case NetworkFamily::IPV6:
                                                                    $color = 'warning';
                                                                break;
                                                            }
                                                            ?>
                                                            <tr>
                                                                <th scope="row">
                                                                    <span class="badge text-bg-<?php print $color; ?>"><?php print NetworkFamily::for($entry->getFamily()); ?></span>
                                                                </th>
                                                                <td>
                                                                    <?php print $entry->getName(); ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                        $link = $entry->getAddress();

                                                                        if($entry->getFamily() == NetworkFamily::IPV6) {
                                                                            $link = sprintf('[%s]', $link);
                                                                        }

                                                                        printf('<a href="http://%1$s" target="_blank">%2$s</a>', $link, $entry->getAddress());
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php print $entry->getBroadcast(); ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                        $ttl = $entry->getTTL();
                                                                        // @see https://github.com/iproute2/iproute2/blob/main/ip/ip_common.h#L219
                                                                        // INFINITY_LIFE_TIME = 0xFFFFFFFFU, unsigned
                                                                        if($ttl == 0) {
                                                                            print '<span class="badge text-bg-warning">zero</span>';
                                                                        } else if($ttl == 0xFFFFFFFF) {
                                                                            print '<span class="badge text-bg-primary">forever</span>';
                                                                        } else {
                                                                            printf('<span class="badge text-bg-warning">%s %s</span>', $ttl, I18N::get('Seconds'));
                                                                        }
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                    ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php
                                    }
                                    ?>
                            </div>
                        </div>
                    <?php
                } else {
                    ?>
                        <div class="jumbotron text-center bg-transparent text-muted">
                            <?php Icon::show('networking'); ?>
                            <h2><?php I18N::__('No Device selected!'); ?></h2>
                            <p class="lead"><?php I18N::__('Please select an network device for more informations.'); ?></p>
                        </div>
                    <?php
                }
            }
        ?>
    </div>
    <?php
	$this->footer();
?>