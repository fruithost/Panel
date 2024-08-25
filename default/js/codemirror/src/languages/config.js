var words = {};

function define(style, dict) {
    for(var i = 0; i < dict.length; i++) {
        words[dict[i]] = style;
    }
};

var commonAtoms = [
    "on", "off", "true", "false",

    "from", "allow", "all", "deny", ",deny", "granted", "denied", "user", "nogroup", "users", "groups",
    "set",

    /* Apache2 */
    "All", "None", "Prod",

    /* ProFTPD: SQL */
    "Backend", "Crypt", "Empty", "OpenSSL", "Plaintext",
    "mysql",
    "Argon2", "Bcrypt", "MD5", "PBKDF2", "Scrypt", "SHA1", "SHA256", "SHA512",
    "custom:/", "sql:/"

];

var commonKeywords = [
    /* Apache2 */
    "DocumentRoot", "DirectoryIndex", "AddDefaultCharset", "ErrorDocument", "Order", "Allow", "Require", "AllowOverride", "Options",
    "FollowSymLinks", "ServerAdmin", "ServerSignature", "ServerTokens", "Header", "IncludeOptional", "Server",

    /* ProFTPD */
    "Include", "UseIPv6", "IdentLookups", "ServerName", "ServerType", "DeferWelcome", "DefaultServer", "ShowSymlinks", "TimeoutNoTransfer", "TimeoutStalled",
    "TimeoutIdle", "DisplayLogin", "DisplayChdir", "ListOptions", "DenyFilter", "DefaultRoot", "Port", "MaxInstances", "User", "Group", "Umask", "AllowOverwrite",
    "TransferLog", "SystemLog", "Ratios", "DelayEngine", "ControlsEngine", "ControlsMaxClients", "ControlsLog", "ControlsInterval", "ControlsSocket", "AdminControlsEngine",
    "RequireValidShell", "PassivePorts", "MasqueradeAddress", "DynMasqRefresh", "PersistentPasswd", "AuthOrder", "UseSendFile", "UseLastlog", "SetEnv", "QuotaEngine",
    "UserAlias", "DirFakeUser", "DirFakeGroup", "RequireValidShell", "MaxClients", "DisplayLogin", "DisplayChdir", "DenyAll", "AllowAll", "ModulePath", "ModuleControlsACLs",
    "LoadModule",

    /* ProFTPD: SQL */
    "SQLBackend", "SQLEngine", "SQLAuthenticate", "SQLAuthTypes", "SQLPasswordEngine", "SQLConnectInfo", "SQLNamedQuery", "SQLUserInfo", "SQLMinUserUID", "SQLDefaultUID",
    "SQLMinUserGID", "SQLMinID", "CreateHome", "SQLLogFile", "SQLGroupInfo", "QuotaDirectoryTally", "QuotaDisplayUnits", "QuotaShowQuotas", "QuotaLimitTable", "QuotaTallyTable",
    "RootLogin"
];

var commonCommands = [
    /* Apache2 */
	"standalone"
];

define('atom',		commonAtoms);
define('property',	commonKeywords);
define('builtin',	commonCommands);

function tokenBase(stream, state) {
    if(stream.eatSpace()) {
		return null;
	}
	
    var sol = stream.sol();
    var ch = stream.next();

	/* Escaping */
    if(ch === '\\') {
        stream.next();
        return null;
    }
	
	/* Qutotes */
    if(ch === '\'' || ch === '"' || ch === '`') {
        state.tokens.unshift(tokenString(ch, ch === "`" ? "quote" : "string"));
		
        return tokenize(stream, state);
    } else if(ch === '#') {
        if(sol && stream.eat('!')) {
            stream.skipToEnd();
            return 'meta'; // 'comment'?
        }
		
        stream.skipToEnd();
        return 'comment';
    }

	/* IP-Address: ipv6 */
    if(stream.match(/(?:[0-9a-fA-F]+)?(?:\:|$){1,8}?/)) {
        return "string";
    }

	/* IP-Address: ipv4 */
    if(stream.match(/(?:[0-9]+)?\.(?:[0-9]+)?\.(?:[0-9]+)?\.(?:[0-9]+)?/)) {
        return "string";
    }

	/* Variables */
    if(ch === '$') {
        state.tokens.unshift(tokenDollar);
        return tokenize(stream, state);
    }
	
	/* Options (Apache) */
    if(ch === '-' || ch === '+') {
        stream.eat(ch);
        stream.eatWhile(/\w/);
		
        return 'attribute';
    }

	/* Groups */
    if(ch === '<') {
        state.tokens.unshift(tokenGroup(ch, "qualifier"));
		
        return tokenize(stream, state);
    }

	/* Numbers*/
    if(/\d/.test(ch)) {
        stream.eatWhile(/\d/);
		
        if(stream.eol() || !/\w/.test(stream.peek())) {
            return 'number';
        }
    }
	
    stream.eatWhile(/[\w-]/);
	
    var cur = stream.current();
	
    if(stream.peek() === '.' && /\w+/.test(cur)) {
		return 'def';
    }
	
	return words.hasOwnProperty(cur) ? words[cur] : null;
}

function tokenGroup(quote, style) {
    var close = quote === "<" ? ">" : quote;
	
    return function (stream, state) {
        var next, escaped = false;
		
        while((next = stream.next()) != null) {
            if(next === close && !escaped) {
                state.tokens.shift();
                break;
				
            } else if(!escaped && quote !== close && next === quote) {
                state.tokens.unshift(tokenGroup(quote, style));
                return tokenize(stream, state);
				
            } else if(!escaped && /['"]/.test(next) && !/['"]/.test(quote)) {
                state.tokens.unshift(tokenStringStart(next, "string"));
                stream.backUp(1);
                break;
            }
			
            escaped = !escaped && next === '\\';
        }
		
        return style;
    };
}

function tokenString(quote, style) {
    var close = quote === "(" ? ")" : quote === "{" ? "}" : quote;
	
    return function (stream, state) {
        var next, escaped = false;
		
        while((next = stream.next()) != null) {
            if(next === close && !escaped) {
                state.tokens.shift();
                break;
				
            } else if(next === '$' && !escaped && quote !== "'" && stream.peek() !== close) {
                escaped = true;
                stream.backUp(1);
                state.tokens.unshift(tokenDollar);
                break;
				
            } else if(!escaped && quote !== close && next === quote) {
                state.tokens.unshift(tokenString(quote, style));
                return tokenize(stream, state);
				
            } else if(!escaped && /['"]/.test(next) && !/['"]/.test(quote)) {
                state.tokens.unshift(tokenStringStart(next, "string"));
                stream.backUp(1);
                break;
            }
			
            escaped = !escaped && next === '\\';
        }
		
        return style;
    };
};

function tokenStringStart(quote, style) {
    return function(stream, state) {
        state.tokens[0] = tokenString(quote, style);
        stream.next();
		
        return tokenize(stream, state);
    }
}

var tokenDollar = function (stream, state) {
    if(state.tokens.length > 1) {
		stream.eat('$');
    }
	
	var ch = stream.next();
	
    if(/['"({]/.test(ch)) {
        state.tokens[0] = tokenString(ch, ch === "(" ? "quote" : ch === "{" ? "def" : "string");
        return tokenize(stream, state);
    }
	
    if(!/\d/.test(ch)) {
		stream.eatWhile(/\w/);
	}
	
    state.tokens.shift();
    
	return 'def';
};

function tokenize(stream, state) {
    return (state.tokens[0] || tokenBase)(stream, state);
}

export const config = {
    name:		"config",
    startState:	function startState() {
        return {
			tokens: []
		};
    },
    token: function token(stream, state) {
        return tokenize(stream, state);
    },
    languageData: {
        autocomplete:	commonAtoms.concat(commonKeywords, commonCommands),
        closeBrackets:	{
			brackets: [ "<", "(", "[", "{", "'", '"', "`" ]
		},
        commentTokens:	{
			line: "#"
		}
    }
};