web

/**
* The server mode can be defined at the server level:
*
* - Apache: SetEnv ServerMode development
* - Nginx:  fastcgi_param ServerMode development
* - Shell:  export ServerMode=development
*/
if (empty($_SERVER['ServerMode1'])) {
exit(<<<STR
<h1>Rax Installation Error!</h1>
<p>The server mode was not defined. Please define it at the server level:</p>
<ul>
    <li>Apache: SetEnv ServerMode development</li>
    <li>Nginx:  fastcgi_param ServerMode development</li>
    <li>Shell:  export ServerMode=development</li>
</ul>
STR
);
}

