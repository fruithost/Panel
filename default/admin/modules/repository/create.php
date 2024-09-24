<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */
	
	use fruithost\Localization\I18N;
?>
<div class="container">
    <div class="form-group row">
        <label for="new" class="col-4 col-form-label col-form-label-sm"><?php I18N::__('Type'); ?>:</label>
        <div class="col-8 form-check">
            <input class="form-check-input" type="radio" name="repo_type" id="public" value="public" checked/>
            <label class="form-check-label" for="new"><?php I18N::__('Public GitHub'); ?></label>
        </div>
        <div class="col-8 offset-4 form-check">
            <input class="form-check-input" type="radio" name="repo_type" id="private" value="private"/>
            <label class="form-check-label" for="existing"><?php I18N::__('Private GitHub'); ?></label>
        </div>
    </div>

    <div class="form-group row repo_github">
        <label for="repository_url" class="col-12 col-form-label col-form-label-sm"><?php I18N::__('Repository URL'); ?>
            :</label>
        <div class="col-12">
            <input type="text" class="form-control" name="repository_url" id="repository_url"
                   aria-label="<?php I18N::__('Repository URL'); ?>"
                   placeholder="https://github.com/<user>/<repository>/"/>
        </div>
    </div>

    <div class="form-group row d-none repo_git">
        <label for="repository_url"
               class="col-12 col-form-label col-form-label-sm"><?php I18N::__('Repository Data'); ?>
            :</label>
        <div class="col-12">
            <div class="row g-3 align-items-center mb-1">
                <div class="col-sm-2">
                    <label for="user" class="col-form-label"><?php I18N::__('User'); ?>:</label>
                </div>
                <div class="col-auto">
                    <input type="text" class="form-control" name="user" id="user"
                           aria-label="<?php I18N::__('User'); ?>"
                           placeholder="<user>"/>
                </div>
            </div>
            <div class="row g-3 align-items-center mb-1">
                <div class="col-sm-2">
                    <label for="repo" class="col-form-label"><?php I18N::__('Repository'); ?>:</label>
                </div>
                <div class="col-auto">
                    <input type="text" class="form-control" name="repo" id="repo"
                           aria-label="<?php I18N::__('Repository'); ?>"
                           placeholder="<repo>"/>
                </div>
            </div>
            <div class="row g-3 align-items-center mb-1">
                <div class="col-sm-2">
                    <label for="branch" class="col-form-label"><?php I18N::__('Branch'); ?>:</label>
                </div>
                <div class="col-auto">
                    <input type="text" class="form-control" name="branch" id="branch"
                           aria-label="<?php I18N::__('Branch'); ?>"
                           placeholder="<branch>"/>
                </div>
                <div class="col-auto">
                    <span id="passwordHelpInline" class="form-text">
                      Optional, Default <span class="badge text-bg-light">master</span>
                    </span>
                </div>
            </div>
            <div class="row g-3 align-items-center mb-1">
                <div class="col-sm-2">
                    <label for="token" class="col-form-label"><?php I18N::__('Token'); ?>:</label>
                </div>
                <div class="col-auto">
                    <input type="text" class="form-control" name="token" id="token"
                           aria-label="<?php I18N::__('Token'); ?>"
                           placeholder="<token>"/>
                </div>
                <div class="col-auto">
                    <span id="passwordHelpInline" class="form-text">
                        <a href="https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/managing-your-personal-access-tokens"
                           target="_blank">Help</a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (() => {
        let repo_url = document.querySelector('input[name="repository_url"]');
        let repo_github = document.querySelector('.repo_github');
        let repo_git = document.querySelector('.repo_git');
        let changes = [
            'user',
            'repo',
            'branch',
            'token'
        ];

        function update() {
            let result = ['git:'];

            changes.map(function (name) {
                let target = document.querySelector('input[name="' + name + '"]');

                if (target.value.length >= 1) {
                    result.push(target.name + ':' + target.value);
                }
            });

            repo_url.value = result.join(' ');
        }

        changes.map(function (name) {
            document.querySelector('input[name="' + name + '"]').addEventListener('keyup', function (event) {
                update();
            });
        });

        [].map.call(document.querySelectorAll('input[type="radio"][name="repo_type"]'), function (checkbox) {
            checkbox.addEventListener('change', function (event) {
                let select = document.querySelector('.directories');

                if (event.target.value === 'private') {
                    repo_github.classList.add('d-none');
                    repo_git.classList.remove('d-none');
                } else {
                    repo_url.value = '';
                    repo_github.classList.remove('d-none');
                    repo_git.classList.add('d-none');
                }
            });
        });
    })();
</script>