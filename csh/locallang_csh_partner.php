<?php

// Context Sensitive Help (CSH) for the 'Partner'-Module
/* Example syntax
		'label.alttitle' => 'Alternative title (not the field name)',
		'label.description' => 'Short description',
		'label.details' => 'Long description',
		'label.seeAlso' => 'tx_partner_main:type',
		'label.image' => 'EXT:partner/csh/img/filename.png',
		'label.image_descr' => 'Image description',
*/

$LOCAL_LANG = Array (
	'default' => Array (
		'no_sys_folder.alttitle' => 'No Sys-Folder',
		'no_sys_folder.description' => 'You cannot create a new partner on this page, because this is not a sys-folder.',
		'no_sys_folder.details' => 'Partner records can only be created on sys-folders.',
		'exact_search.alttitle' => 'Exact Search',
		'exact_search.description' => 'If you activate this option, the system will look for an exact match with the values you entered. If the option is deactivated however, only the beginning of the search values will be taken into account.',
		'exact_search.details' => 'If this option is not active, it has the same effect as if every value was appended with a wildcard (*) at the beginning and at the end. Example: You have a partner is your system called \'Miller-Smith\'. If you activate the option \'Exact Search\' enter the value \'Miller\', the system will not find the partner. If the option is not active however, the partner will be selected, as part after \'Miller\' is disregarded.',
		'sec_partner_excluded_because_same_as_primary.alttitle' => 'Secondary Partner excluded from search result',
		'sec_partner_excluded_because_same_as_primary.description' => 'The secondary partner was excluded from the search result, because it is the same as the currently selected primary partner.',
		'sec_partner_excluded_because_same_as_primary.details' => 'A partner record cannot have a relationship with itself.',
		'sec_partner_removed_because_not_allowed.alttitle' => 'Secondary Partner removed from search result',
		'sec_partner_removed_because_not_allowed.description' => 'The secondary partner was removed from the search result, because it is not allowed by the selected relationship type.',
		'sec_partner_removed_because_not_allowed.details' => 'Most likely, you have changed the relationship type in the default values. The system now checks if the selected partner is allowed for this new relationship type. If this is not the case, the partner is removed from the search result.',
		'no_partner_found.alttitle' => 'No Partner found',
		'no_partner_found.description' => 'There was no partner record found with the entered search string.',
		'no_partner_found.details' => 'The system looks for partner records with the entered search string in the label field. Please check if the string you entered was correct. Be aware that the system only searches for partner which are allowed for the currently selected relationship type! (e.g. if the relationship type is \'Has Child\', the system only searches for persons, not organisations.)',
		'no_allowed_rel_types_found.alttitle' => 'No allowed relationship types found',
		'no_allowed_rel_types_found.description' => 'There are no relationship types which are allowed for the selected partner. Most likely, your administrator did not yet set up the relationship-type records needed to properly run this extension.',
		'no_allowed_rel_types_found.details' => 'The system looks for relationship-type records in the current sys-folder. When defining relationship-type records, the adminstrator can decide for which partner-types the relationship-type is allowed. For instance, it is not allowed for a relationship type \'Is Owner of\' to have a secondary partner of the type \'Person\'.',
		'no_rel_status_found.alttitle' => 'No status for relationships found',
		'no_rel_status_found.description' => 'There are no status-records which are allowed for relationships. Most likely, your administrator did not yet set up the status records to properly run this extension.',
		'no_rel_status_found.details' => 'The system looks for status records for relationships in the current sys-folder. When defining status records, the administrator can decide for which tables the status is allowed. Currently, no status records are available for the table tx_partner_relationships.',
	),
	'dk' => Array (
	),
	'de' => Array (
		'no_sys_folder.alttitle' => 'Kein Sys-Ordner',
		'no_sys_folder.description' => 'Sie können auf dieser Seite keinen neuen Partner anlegen, da die aktuelle Seite kein Sys-Ordner ist.',
		'no_sys_folder.details' => 'Partner können nur in Sys-Ordnern angelegt werden.',
		'exact_search.alttitle' => 'Genaue Suche',
		'exact_search.description' => 'Wenn Sie diese Option aktivieren, sucht das System nach einer genauen Übereinstimmung mit den von Ihnen eingegebenen Werten. Ist die Option deaktiviert, werden von den eingegebenen Werten nur die Anfangswerte verglichen.',
		'exact_search.details' => 'Wenn diese Option nicht aktiviert ist, hat dies die gleiche Wirkung, wie wenn Sie jedem Eingabewert eine Wildcard (*) vorne und hinten anfügen würden. Beispiel: Sie haben im System einen Partner mit dem Nachnamen \'Meier-Müller\'. Wenn Sie nun bei aktivierter Option \'Genaue Suche\' den Wert \'Meier\' suchen, wird das System nichts finden. Bei deaktivierter Option hingegen wird der Datensatz selektiert, da der Teil hinter \'Meier\' nicht mehr beachtet wird.',
		'sec_partner_excluded_because_same_as_primary.alttitle' => 'Verbundener Partner vom Suchresultat ausgeschlossen',
		'sec_partner_excluded_because_same_as_primary.description' => 'Der verbundene Partner wurde vom Suchresultat ausgeschlossen, da es sich um den gleichen Partner handelt wie den aktuellen Hauptpartner.',
		'sec_partner_excluded_because_same_as_primary.details' => 'Ein Partner kann nicht mit sich selbst über eine Beziehung verbunden werden.',
		'sec_partner_removed_because_not_allowed.alttitle' => 'Verbundener Partner vom Suchresultat ausgeschlossen',
		'sec_partner_removed_because_not_allowed.description' => 'Der verbundene Partner wurde vom Suchresultat ausgeschlossen, weil er für den ausgewählten Beziehungstypen nicht zulässig ist.',
		'sec_partner_removed_because_not_allowed.details' => 'Vermutlich haben Sie den Beziehungstyp in den Vorgabewerten geändert. Das System überprüft nun, ob der gewählte Partner für den neuen Beziehungstypen zulässig ist. Falls dem nicht so ist, wird er aus dem Suchresultat ausgeschlossen.',
		'no_partner_found.alttitle' => 'Kein Partner gefunden',
		'no_partner_found.description' => 'Es wurde kein Partner mit den eingegebenen Suchbegriffen gefunden.',
		'no_partner_found.details' => 'Das System vergleicht die eingegebenen Suchbegriffe mit dem Bezeichnungs-Feld der Partner. Bitte überprüfen Sie, ob die eingegebenen Werte korrekt sind. Bitte beachten Sie, dass das System nur nach Partnern sucht, die für den gewählten Beziehungstyp zugelassen sind! (Wenn z.B. der Beziehungstyp \'Hat Kind\' gewählt ist, sucht das System nur Personen, nicht Organisationen.)',
		'no_allowed_rel_types_found.alttitle' => 'Keine zugelassenen Beziehungstypen gefunden',
		'no_allowed_rel_types_found.description' => 'Für den aktuellen Hauptpartner bestehen keine zugelassenen Beziehungstypen. Vermutlich hat Ihr Administrator die Beziehungstypen noch nicht vollständig eingerichtet. Diese sind für die korrekte Funktionsweise dieser Extension unerlässlich.',
		'no_allowed_rel_types_found.details' => 'Das System sucht im aktuellen Sys-Ordner nach Beziehungstypen. Bei der Definition von Beziehungstypen kann Ihr Administrator festlegen, welche Partner-Typen der Beziehungstyp zulässig ist. So ist es beispielsweise für einen Beziehungstyp \'Ist Besitzer von\' nicht zulässig, einen verbundenen Partner vom Typ \'Person\' zu haben.',
		'no_rel_status_found.alttitle' => 'Keine Status-Informationen für Beziehungen gefunden',
		'no_rel_status_found.description' => 'Es bestehen keine Status-Informationen für Beziehungen. Vermutlich hat Ihr Administrator die Status-Informationen noch nicht vollständig eingerichtet. Diese sind für die korrekte Funktionsweise dieser Extension unerlässlich.',
		'no_rel_status_found.details' => 'Das System sucht im aktuellen Sys-Ordner nach Status-Informationen. Bei der Definition von Status-Informationen kann Ihr Administrator festlegen, für welche Tabellen die Status-Informationen gelten. Aktuell sind keine Status-Informationen für die Tabelle tx_partner_relationships gepflegt.',
		),
	'no' => Array (
	),
	'it' => Array (
	),
	'fr' => Array (
	),
	'es' => Array (
	),
	'nl' => Array (
	),
	'cz' => Array (
	),
	'pl' => Array (
	),
	'si' => Array (
	),
	'fi' => Array (
	),
	'tr' => Array (
	),
	'se' => Array (
	),
	'pt' => Array (
	),
	'ru' => Array (
	),
	'ro' => Array (
	),
	'ch' => Array (
	),
	'sk' => Array (
	),
	'lt' => Array (
	),
	'is' => Array (
	),
	'hr' => Array (
	),
	'hu' => Array (
	),
	'gl' => Array (
	),
	'th' => Array (
	),
	'gr' => Array (
	),
	'hk' => Array (
	),
	'eu' => Array (
	),
	'bg' => Array (
	),
	'br' => Array (
	),
	'et' => Array (
	),
	'ar' => Array (
	),
	'he' => Array (
	),
	'ua' => Array (
	),
	'lv' => Array (
	),
	'jp' => Array (
	),
	'vn' => Array (
	),
);
?>