var words = {};

function define(style, dict) {
    for(var i = 0; i < dict.length; i++) {
        words[dict[i]] = style;
    }

	/* Resorting */
	var temporary	= Object.keys(words);
	var sorted		= {};
	temporary.sort();
	temporary.reverse();

	temporary.forEach(function(index) {
		sorted[index] = words[index];
	});
	
	words = sorted;
};

var commonAtoms = [
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
    "DocumentRoot", "DirectoryIndex", "AddDefaultCharset", "ErrorDocument", "Order", "Require", "AllowOverride", "Allow", "Options",
    "FollowSymLinks", "Indexes", "ServerAdmin", "ServerSignature", "ServerTokens", "Header", "IncludeOptional", "Server", "ErrorLog",
	"CustomLog", "Alias",

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
	"on", "off", "true", "false",

    "from", "allow", "all", "deny", ",deny", "granted", "denied", "user", "nogroup", "users", "groups",
    "set", "standalone", "anonymous", "common", "combined"
];

define('atom',		commonAtoms);
define('property',	commonKeywords);
define('builtin',	commonCommands);

function parse(stream, state) {
	var ch = stream.peek();
	
	/* Comment */
	if(ch == "#") {
		stream.skipToEnd();
		return "comment";
	}
	
	/* Variables */
	if(stream.match(/^\$\w+/)) {
		return "def";
	}
	
	/* Words */
	for(let word in words) {
		if(stream.match(new RegExp('^(\\+|\\-)' + word + '', 'i')) || stream.match(new RegExp('^(' + word + ')')) || stream.match(new RegExp('^(' + word + ')$'))) {
			return words[word];
		}
	}
	
	/* Numbers */
	if(stream.match(/^(\d+)/)) {
		return "number";
	}
	
	/* Strings */
	if(stream.match(/^(".+")/)) {
		return "string";
	}
	
	/* Groups */
	if(stream.match(/^(<.+>)/)) {
		return "atom";
	}
	
	/* Filesystem */
	if(stream.match(/([\.\/a-zA-Z0-9_\-\*]+)\.([a-zA-Z0-9\*\-]+){1,6}/)) {
		return "operator";
	}
	
	
	stream.next();
	return null;
}

export const config = {
    name:		"config",
    startState:	function startState() {
        return {
			controlFlow:		false,
			macroParameters:	false,
			section:			false
		};
    },
    token: parse,
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