<?php
	use fruithost\Accounting\Auth;
	use fruithost\Utils;
	use fruithost\I18N;
	
	$template->header();
	
	if(!Auth::hasPermission('SERVER::MANAGE')) {
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
	<form method="post" action="<?php print $this->url('/server/logs'); ?>">
		<section class="contentbar-content">
			<header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
				<h1 class="h2">
					<a class="active" href="<?php print $this->url('/server/logs'); ?>"><?php I18N::__('Logfiles'); ?></a>
				</h1>
				<div class="btn-toolbar mb-2 mb-md-0">
					<button type="submit" name="action" value="refresh" class="btn btn-sm btn-outline-primary"><?php I18N::__('Refresh'); ?></button>
				</div>
			</header>
			<div class="loading jumbotron text-center bg-transparent text-muted" style="display: none;">
				<h2><?php I18N::__('Loading'); ?></h2>
				<p class="lead">
					<div class="spinner-border text-primary" role="status"></div>
				</p>
			</div>
			<div class="no-file jumbotron text-center bg-transparent text-muted" style="display: block;">
				<i class="material-icons">upload_file</i>
				<h2><?php I18N::__('No File selected!'); ?></h2>
				<p class="lead"><?php I18N::__('Please select an Logfile from the List.'); ?></p>
			</div>
			<article class="editor" style="display: none;"></article>
		</section>
		<aside class="col-12 col-sm-4 col-md-3 col-lg-2 bg-light contentbar d-md-none d-md-block">
			<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-2 mb-2 text-muted" data-toggle="collapse" data-target="#collapse_account" aria-expanded="false" aria-controls="collapse_account">
				<span><?php I18N::__('Logfiles'); ?></span>
			</h6>
			<ul class="tree"></ul>
		</aside>
	</form>
	<script>
		_watcher_modules = setInterval(function() {
			if(typeof(jQuery) !== 'undefined' && typeof(Terminal) !== 'undefined') {
				clearInterval(_watcher_modules);
				
				(function($) {					
					let list	= JSON.parse('<?php print json_encode($list); ?>');
					let parser	= new Terminal();
					let result 	= [];
					let level 	= { result };
					let paths	= {};
					
					list.forEach((path) => {
						let tiles	= path.split('/');
						let end		= tiles[tiles.length - 1];
						paths[end]	= path;
						
						tiles.reduce((target, name, index, array) => {
							if(!target[name]) {
								target[name] = {
									result: 	[],
									path:		paths[name]
								};
							
								target.result.push({
									name:		name,
									children:	target[name].result,
									path:		target[name].path
								});
							}

							return target[name];
						}, level);
					});
					
					function openFile(path) {
						let no_file = document.querySelector('div.no-file');
						let loading = document.querySelector('div.loading');
						let editor	= document.querySelector('article.editor');
						
						no_file.style.display	= 'none';
						editor.style.display	= 'none';
						loading.style.display	= 'block';
						editor.innerHTML		= '';
						
						$.ajax({
							type:	'POST',
							url:	'<?php print $this->url('/server/logs'); ?>',
							data:	{
								action:	'file',
								file:	path
							},
							success: function onSuccess(response) {
								try {
									let json = JSON.parse(response);
									console.log(json);
									
									if(json.content === null) {
										editor.innerText		= 'File not exists';
									} else if(json.content === false) {
										editor.innerText		= 'Permission denied!';
									} else {
										if(json.content.includes('\033')) {
											editor.innerHTML	= parser.parse(json.content)
										} else {										
											editor.innerText	= json.content;
										}
									}
									
									loading.style.display	= 'none';
									editor.style.display	= 'block';
								} catch(e) {
									console.warn(e);
								}
							}
						});
					}

					function build(node, tree) {
						tree.forEach(entry => {
							let element			= document.createElement('li');
							let caret			= document.createElement('span');
							caret.classList.add('caret');
							caret.innerHTML		= '<i>' + entry.name + '</i>';
							element.appendChild(caret);
							
							if(entry.children.length == 1 && entry.children[0].name == '') {
								caret.innerHTML			= '<i>' + entry.name + '/</i>';
								element.style.order 	= 0;
								caret.dataset.content	= '<strong><?php I18N::__('Directory'); ?>:</strong> ' + entry.name + (typeof(entry.path) == 'undefined' ? '' : '<br /><strong><?php I18N::__('Path'); ?>:</strong> ' + entry.path) + '<br /><small class="text-danger"><?php I18N::__('No permissions!'); ?></small>';
								caret.dataset.toggle	= 'popover';
								element.classList.add('empty');
								element.classList.add('directory');
							} else if(entry.children.length > 0) {
								let sub					= document.createElement('ul');
								caret.innerHTML			= '<i>' + entry.name + '/</i>';
								caret.dataset.content	= '<strong><?php I18N::__('Directory'); ?>:</strong> ' + entry.name + (typeof(entry.path) == 'undefined' ? '' : '<br /><strong><?php I18N::__('Path'); ?>:</strong> ' + entry.path);
								caret.dataset.toggle	= 'popover';
								element.style.order		= 0;
								sub.classList.add('nested');
								element.classList.add('directory');
								build(sub, entry.children);
								element.appendChild(sub);
							} else {
								element.style.order		= 1;
								caret.dataset.content	= '<strong><?php I18N::__('File'); ?>:</strong> ' + entry.name + '<br /><strong><?php I18N::__('Path'); ?>:</strong> ' + entry.path;
								caret.dataset.toggle	= 'popover';
								caret.dataset.path		= entry.path;
								element.classList.add('file');	
							}
							
							$(caret).popover({
								container: 'body',
								html:		true,
								placement:	'right',
								trigger:	'hover',
								boundary:	'viewport'
							});
							
							caret.addEventListener('click', function(event) {
								let nested = this.parentElement.querySelector('.nested');
								
								if(nested !== null && typeof(nested) !== 'undefined' && nested.classList !== null) {
									nested.classList.toggle('active');
									this.classList.toggle('caret-down');
								}
								
								if(typeof(this.dataset) !== 'undefined' && typeof(this.dataset.path) !== 'undefined' && this.dataset.path != null) {
									openFile(this.dataset.path);
								}
							});
							
							node.appendChild(element);
						})
					}
					
					build(document.querySelector('ul.tree'), result[0].children);
				}(jQuery));
			}
		}, 500);
	</script>
	<?php
	$template->footer();
?>