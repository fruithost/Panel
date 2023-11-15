<?php
    use fruithost\Localization\I18N;
    use fruithost\Accounting\Auth;
    use fruithost\UI\Icon;

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
			<header class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16'%3E%3Cpath d='M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
						<li class="breadcrumb-item active" aria-current="page">
							<a href="<?php print $this->url('/server/logs'); ?>"><?php I18N::__('Logfiles'); ?></a>
						</li>
					</ol>
				</nav>
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
				<?php Icon::show('file'); ?>
				<h2><?php I18N::__('No File selected!'); ?></h2>
				<p class="lead"><?php I18N::__('Please select an Logfile from the List.'); ?></p>
			</div>
			<article class="editor" style="display: none;"></article>
		</section>
		<aside class="bg-body-tertiary border-start contentbar d-md-block">
			<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-2 mb-2 text-muted" data-toggle="collapse" data-target="#collapse_account" aria-expanded="false" aria-controls="collapse_account">
				<span><?php I18N::__('Logfiles'); ?></span>
			</h6>
			<ul class="tree"></ul>
		</aside>
	</form>
	<script>
		(() => {
			'use strict'
			window.addEventListener('DOMContentLoaded', () => {
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
				
				function highlight(content) {
					let lines	= [];
					
					content.split('\n').forEach(function(line, index ) {
						lines.push('<div class="line user-select-all"><div class="number">' + (index + 1) + '</div><div class="data">' + line + '</div></div>');
					});
					
					return '<div class="content">' + lines.join('') + '</div>';
				}
				
				function openFile(path) {
					let no_file = document.querySelector('div.no-file');
					let loading = document.querySelector('div.loading');
					let editor	= document.querySelector('article.editor');
					
					no_file.style.display	= 'none';
					editor.style.display	= 'none';
					loading.style.display	= 'block';
					editor.innerHTML		= '';
					
					new Ajax('<?php print $this->url('/server/logs'); ?>').onError(function(event, error, response) {
						console.warn(event, error, response);
					}).onSuccess(function(response) {
						try {
							let json = response;
							
							if(json.content === null) {
								editor.innerText		= 'File not exists';
							} else if(json.content === false) {
								editor.innerText		= 'Permission denied!';
							} else {
								if(json.content.includes('\\033')) {
									editor.innerHTML	= parser.parse(json.content)
								} else {
									editor.innerHTML	= highlight(json.content);
								}
							}
							
							loading.style.display	= 'none';
							editor.style.display	= 'flex';
						} catch(e) {
							console.warn(e);
						}
					}).post({	
						action:	'file',
						file:	path
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
						
						/*$(caret).popover({
							container: 'body',
							html:		true,
							placement:	'right',
							trigger:	'hover',
							boundary:	'viewport'
						});*/
						
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
			});
		})();
	</script>
	<?php
	$template->footer();
?>