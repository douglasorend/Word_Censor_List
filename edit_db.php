<?php
global $modSettings;

// If we have found SSI.php and we are outside of SMF, then we are running standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF')) // If we are outside SMF and can't find SSI.php, then throw an error
	die('<b>Error:</b> Cannot install - please verify you put this file in the same place as SMF\'s SSI.php.');
db_extend('packages');
	
// Get current settings
$request = $smcFunc['db_query']('', '
	SELECT variable, value
	FROM {db_prefix}settings
	WHERE variable = "censor_vulgar"');
$row = $smcFunc['db_fetch_assoc']($request);
$modSettings['censor_vulgar'] = $row['value'];
$smcFunc['db_free_result']($request);

$request = $smcFunc['db_query']('', '
	SELECT variable, value
	FROM {db_prefix}settings
	WHERE variable = "censor_proper"');
$row = $smcFunc['db_fetch_assoc']($request);
$modSettings['censor_proper'] = $row['value'];
$smcFunc['db_free_result']($request);

// Define all the words to censor:
$censor_vulgar = array(
	'@$$*',
	'a$$*',
	'as$*',
	'a\$s*',
	'@\$s*',
	'@s$*',
	'amcik',
	'andskota',
	'arschloch',
	'arse*',
	'ass',
	'assho*',
	'assram*',
	'ayir',
	'bi+ch',
	'b!+ch',
	'b!tch',
	'b!7ch',
	'bi7ch',
	'b17ch',
	'b1+ch',
	'b1tch',
	'bitch*',
	'bastard',
	'boiolas',
	'bollock*',
	'breasts',
	'buceta',
	'butt-pirate',
	'cock*',
	'c0ck',
	'cabron',
	'cawk',
	'cazzo',
	'chink',
	'chraa',
	'chuj',
	'cipa',
	'clits',
	'cum',
	'cunt*',
	'*damn',
	'*d4mn',
	'dago',
	'daygo',
	'dego',
	'dick*',
	'dike*',
	'dildo',
	'*dyke*',
	'dirsa',
	'dupa',
	'dziwka',
	'ejac*',
	'Ekrem*',
	'Ekto',
	'enculer',
	'faen',
	'fag*',
	'fanculo',
	'fanny',
	'fatass',
	'fat@$$',
	'fata$$',
	'fatas$',
	'fata\$s',
	'fat@\$s',
	'fat@s$',
	'fatarse',
	'fcuk',
	'feces',
	'feg',
	'Felcher',
	'ficken',
	'fitt*',
	'Flikker',
	'foreskin',
	'Fotze',
	'Fu(*',
	'*fuck*',
	'fuk*',
	'futkretzn',
	'fux0r',
	'frig',
	'frigin*',
	'friggin*',
	'gay',
	'gaydar',
	'gook',
	'guiena',
	'h0r',
	'hax0r',
	'h4xor',
	'h4x0r',
	'hell',
	'helvete',
	'hoer*',
	'honkey',
	'hore',
	'Huevon',
	'hui',
	'injun',
	'jackass',
	'jism',
	'jizz',
	'kanker*',
	'kawk',
	'kike',
	'klootzak',
	'knulle',
	'kuk',
	'kuksuger',
	'Kurac',
	'kurwa',
	'kusi*',
	'kyrpa*',
	'l3i+ch',
	'l3itch',
	'l3i7ch',
	'l3!tch',
	'l3!+ch',
	'lesbian',
	'lesbo',
	'mamhoon',
	'masturbat*',
	'merd*',
	'mibun',
	'monkleigh',
	'motherfuck*',
	'mofo',
	'mouliewop',
	'muie',
	'mulkku',
	'muschi',
	'nazi*',
	'nepesaurio',
	'nigga*',
	'nigger*',
	'nutsack',
	'orospu',
	'paska*',
	'penis',
	'perse',
	'phuck',
	'picka',
	'pierdol*',
	'pillu*',
	'pimmel',
	'pimpis',
	'piss*',
	'pizda',
	'poontsee',
	'poop',
	'porn',
	'p0rn',
	'pr0n',
	'preteen',
	'prick',
	'pula',
	'pule',
	'pusse',
	'pussy',
	'puta',
	'puto',
	'qahbeh',
	'queef*',
	'queer*',
	'qweef',
	'rautenberg',
	'schaffer',
	'scheiss*',
	'schlampe',
	'schmuck',
	'screw',
	'scrotum',
	'*shit*',
	'sh!t*',
	'sharmuta',
	'sharmute',
	'shemale',
	'shipal',
	'shiz',
	'skribz',
	'skurwysyn',
	'slut',
	'smut',
	'sphencter',
	'spic',
	'spierdalaj',
	'splooge',
	'suka',
	'teets',
	'b00b*',
	'teez',
	'testicle*',
	'titt*',
	'tits',
	'twat*',
	'vagina',
	'viag*',
	'v1ag*',
	'v14g*',
	'vi4g*',
	'vittu',
	'w00se',
	'wank*',
	'wetback*',
	'whoar',
	'whore',
	'wichser',
	'wop*',
	'wtf',
	'yed',
	'jerk*',
	'dipwad',
	'zabourah',
);
$censor_proper = array();
foreach ($censor_vulgar as $word)
	$censor_proper[] = '*';
$censor_vulgar = implode("\n", $censor_vulgar);
$censor_proper = implode("\n", $censor_proper);
$temp_censor_vulgar = str_replace($censor_vulgar . "\n", '', $modSettings['censor_vulgar']);
$temp_censor_proper = str_replace($censor_proper . "\n", '', $modSettings['censor_proper']);

// Add these words if not uninstalling:
if (empty($context['uninstalling']))
{
	$temp_censor_vulgar = $censor_vulgar . "\n" . $temp_censor_vulgar;
	$temp_censor_proper = $censor_proper . "\n" . $temp_censor_proper;
}

// Update the database with the new settings:
updateSettings(array(
  'censor_vulgar' => $temp_censor_vulgar,
  'censor_proper' => $temp_censor_proper,
));

if (SMF == 'SSI')
   echo 'Congratulations! You have successfully installed this mod!';
?>