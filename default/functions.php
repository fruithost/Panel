<?php
    use fruithost\Accounting\Auth;
    use fruithost\Templating\TemplateFiles;

    if(!Auth::isLoggedIn()) {
        $this->getFiles()->addStylesheet('login', $this->url('css/login.css'), '2.0.0', [ 'bootstrap' ]);
    } else {
        $this->getFiles()->addStylesheet('style', $this->url('css/style.css'), '2.0.0', [ 'bootstrap' ]);
        $this->getFiles()->addJavascript('navigation', $this->url('js/navigation.js'), '2.0.0', [ 'bootstrap' ], TemplateFiles::FOOTER);
    }
?>