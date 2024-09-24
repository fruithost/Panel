<?php
    use fruithost\Accounting\Auth;
    use fruithost\Templating\TemplateFiles;

    $this->getFiles()->addStylesheet('global', $this->url('css/global.css'), '1.0.0', [ 'bootstrap' ]);
    $this->getFiles()->addJavascript('global', $this->url('js/global.js'), '1.0.0', [ 'bootstrap' ], TemplateFiles::FOOTER);
    $this->getFiles()->addJavascript('ajax', $this->url('js/ajax.js'), '1.0.0', [ 'bootstrap' ], TemplateFiles::FOOTER);

    if(!Auth::isLoggedIn()) {
        $this->getFiles()->addStylesheet('login', $this->url('css/login.css'), '2.0.0', [ 'bootstrap' ]);
        $this->getFiles()->addJavascript('login', $this->url('js/login.js'), '1.0.0', [ 'bootstrap' ], TemplateFiles::FOOTER);
    } else {
        $this->getFiles()->addStylesheet('style', $this->url('css/style.css'), '2.0.0', [ 'bootstrap' ]);
        $this->getFiles()->addJavascript('ui', $this->url('js/ui.js'), '2.0.0', [ 'bootstrap' ], TemplateFiles::FOOTER);
        $this->getFiles()->addJavascript('codemirror', $this->url('js/codemirror/build/bundle.min.js'), '6.0.0', [ 'ui' ], TemplateFiles::FOOTER);
    }
?>