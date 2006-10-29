<?php

// Field description for the table 'tx_partner_main'
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
		'label.description' => 'Label of the partner',
		'label.details' => 'The label is used in many places in the backend, usually when the partner has to be described with just one field. The value of the label is formed by TYPO3 in the following way:
<b>For Persons</b>
[Last name], [First Name] - [Locality]

<b>For Organisations</b>
[Name of Organisation] - [Locality]',
		'status.description' => 'Status of the partner',
		'status.details' => 'Describes the current status of the partner. The list of possible status can be defined by your administrator. Possible values could be \'Active\', \'Passive\' or \'To be archived\'.',
		'data_source.description' => 'Data source of the partner',
		'data_source.details' => 'Describes where the partner has originated. Can be for instance the name of a third-party application. Can be used together with the field \'External ID\' to fully identify a partner from an external application.',
		'data_source.seeAlso' => 'tx_partner_main:external_id',
		'external_id.description' => 'External Identification of the partner',
		'external_id.details' => 'Contains the identification of this partner in an external application, which is usually defined in the field \'Data Source\'.',
		'external_id.seeAlso' => 'tx_partner_main:data_source',
		'contact_permission.description' => 'Contact permission status for the partner',
		'contact_permission.details' => 'Defines if and how a partner may be contacted. This list can be defined by your administrator. Possible values could be \'Unrestricted\', \'Do not contact\' or \'Call X before contacting\'.',
		'fe_user.description' => 'Frontend User ID of the partner',
		'fe_user.details' => 'If this partner is also a frontend user, then the user record can be linked to the partner by entering the frontend user ID in this field.',
		'image.description' => 'Image of the partner',
		'image.details' => 'You can upload one picture of the partner here. This picture may for instance be used when displaying partner records on your website.',
		'relationships_overview.description' => 'List of all relationships for this partner',
		'relationships_overview.details' => 'This list gives you an overview about all the relationships that were entered for this partner. You can also change existing relationships, change related partners or create new relationships.

<strong>Primary or Secondary Partner?</strong>
A relationship always consists of exactly two partners. One is the primary partner, the other is the secondary partner. The type of a relationship defines what kind of relationships the two partners have.

Let\'s say, for instance, that one partner is the member of the other, like a member of a club. Well in this case, the relationship type you want to have is \'has member\'. You can see already from the formulation of the type that there is a \'direction\' to the relationship. Club Y \'has member\' Mr. Miller and not the other way round. So Club Y is the primary partner and Mr. Miller is the secondary partner.

Let\'s take another example. You would like to know who belongs to a certain family. Your administrator has configured the relationship types \'has child\' and \'has wife\' for this purpose. Again you see from the formulation that each of these types have a \'direction\'. So Mr. and Mrs. Miller have a child, David. You would therefore create three new partners: Mr. Miller, Mrs. Miller and David Miller. In order to enter the family relationship, you would open the record of Mr. Miller and create a new relationship as \'primary partner\'. Then you would chose the type \'has child\' and enter David Miller as secondary partner. Secondly, you would create a new relationship, again with Mr. Miller as the primary partner, chose \'has wife\' as type and enter Mrs. Miller as secondary partner. And here you are, you just created your family!
		',
		'preceding_title.description' => 'Preceding Title of the partner',
		'preceding_title.details' => 'Describes any titles that must be used before the actual title of the partner. Examples: \'His Excellency\', \'Honorable\', etc.',
		'title.description' => 'Title of the partner',
		'title.details' => 'Defines the title of the partner, for instance \'Mister\', \'Professor\', \'Dr.\', etc. This list can be defined by your administrator.',
		'letter_title.description' => 'Letter Title of the partner',
		'letter_title.details' => 'Defines a special letter title just for this partner, if applicable. If this field is blank, then letters to this partner will feature the standard letter title (like \'Dear Mr. xyz\')',
		'first_name.description' => 'First Name of the partner',
		'first_name.details' => 'Enter <b>only</b> the <b>first</b> name here. There are additional fields for the middle and the last name of the partner.',
		'middle_name.description' => 'Middle Name of the partner',
		'middle_name.details' => 'Enter <b>only</b> the <b>middle</b> name here. There are additional fields for the first and the last name of the partner.',
		'last_name_prefix.description' => 'Last Name Prefix of the partner',
		'last_name_prefix.details' => 'If the last name of the partner should be prefixed, you can enter this prefix here. For instance \'de la\' or \'van den\', etc.',
		'last_name.description' => 'Last Name of the partner',
		'last_name.details' => 'Enter <b>only</b> the <b>last</b> name (e.g. family name) here. There are additional fields for the first and the middle name of the partner.',
		'maiden_name.description' => 'Maiden Name of the partner',
		'maiden_name.details' => 'This field can be used to enter the maiden (unmarried) name of the partner. Please note that this name will usually not be used to display or print, but server just for information.',
		'general_suffix.description' => 'General Suffix of the partner',
		'general_suffix.details' => 'If the whole name of the partner should be suffixed, you can enter this suffix here. Examples would be \'retired\' or \'deceased\'.',
		'initials.description' => 'Initials of the partner',
		'initials.details' => 'Usually formed by the first, middle and last name of a partner, but you can use this according to your own syntax. Example: \'Paul J. Miller\' could have the initials \'PJM\'.',
		'org_name.description' => 'Organisation Name',
		'org_name.details' => 'Full name of the organisation, for instance the name of the firm or the name of a group of persons.',
		'org_type.description' => 'Organisation Type',
		'org_type.details' => 'Defines the type of organistion, for instance a firm, a club, a group, or anything else which is not a person. This list can be defined by your administrator.',
		'org_legal_form.description' => 'Legal Form of the organisation',
		'org_legal_form.details' => 'Defines the legal form of the organisation, if applicable. This list can be defined by your administrator.',
		'department.description' => 'Department (address of the partner)',
		'department.details' => 'Usually used for business addresses, if the partner is a member of or has the address of a certain department.',
		'building.description' => 'Building (address of the partner)',
		'building.details' => 'Can be used to further refine the address by exactly naming the name of the building where the partner is located (like \'Egis\' or \'Beauty Appartments\').',
		'floor.description' => 'Floor (address of the partner)',
		'floor.details' => 'Can be used to further refine the address by exactly naming the floor where the partner is located.',
		'room.description' => 'Room (address of the partner)',
		'room.details' => 'Can be used to further refine the address by exactly naming the room (usually a number) where the partner is located.',
		'street.description' => 'Street (address of the partner)',
		'street.details' => 'Name of the street (as part of the address).',
		'street_number.description' => 'Street Number (address of the partner)',
		'street_number.details' => 'Street Number of the partner\'s building (as part of the address).',
		'postal_code.description' => 'Postal Code (address of the partner)',
		'postal_code.details' => 'The postal code (or ZIP code) of the partner\'s locality.',
		'locality.description' => 'Locality (address of the partner)',
		'locality.details' => 'The partner\'s locality. <b>Do not</b> enter the postal code here. There is an additional field just for the postal code.',
		'admin_area.description' => 'Administrative Area (address of the partner)',
		'admin_area.details' => 'The administrative area that the partner\'s locality belongs to. In the USA, this could be the two-letter State-Code (like \'NY\' for New York).',
		'country.description' => 'Country (address of the partner)',
		'country.details' => 'The country where the partner resides.',
		'po_number.description' => 'Post Box Number (address of the partner)',
		'po_number.details' => 'If the partner has a post box (PO), you can enter the number here.',
		'po_no_number.description' => 'PO Box without number (address of the partner)',
		'po_no_number.details' => 'If the partner has a post box (PO box), but the PO box is not numbered, you can check this box.',
		'po_postal_code.description' => 'PO Box Postal Code (address of the partner)',
		'po_postal_code.details' => 'The postal code (or ZIP-code) or the partner\'s PO box locality.',
		'po_locality.description' => 'PO Box Locality (address of the partner)',
		'po_locality.details' => 'The partner\'s PO box locality. <b>Do not</b> enter the postal code of the PO box here. There is an additional field just for the PO box postal code.',
		'po_admin_area.description' => 'PO Box Administrative Area (address of the partner)',
		'po_admin_area.details' => 'The administrative area that the partner\'s PO box locality belongs to. In the USA, this could be the two-letter State-Code (like \'NY\' for New York).',
		'po_country.description' => 'PO Box Country (address of the partner)',
		'po_country.details' => 'The country of the partner\'s PO box.',
		'contact_info.description' => 'Contact Information of the partner',
		'contact_info.details' => 'You can enter an unlimited numer of contact informations for the partner here (like phone numbers or e-mail addresses).',
		'formation_date.description' => 'Formation Date of the organisation',
		'formation_date.details' => 'If known, you can enter the date of the formation of the organisation here.',
		'closure_date.description' => 'Closure Date of the organisation',
		'closure_date.details' => 'This is usually the date where the organisation has ceased to formally exist (e.g. if a firm went bankrupt).',
		'birth_date.description' => 'Birth Date of the person',
		'birth_date.details' => '<b>Important Notice</b>: You <b>must</b> enter the birth date exactly in the following format: <b>YYYYMMDD</b>.
Example: <b>19390209</b> is February 2, 1939.
Cookbook: You enter the year first (with 4 digits, like 1939), then without a comma or anything you enter the month (with 2 digits, like 02 for february) and then, again with no comma or anything you add the day of the month (with 2 digits, like 09).',
		'birth_place.description' => 'Birth Place of the person',
		'birth_place.details' => 'Place (possibly including the country) where the person was born.',
		'death_date.description' => 'Death Date of the person',
		'death_date.details' => '<b>Important Notice</b>: You <b>must</b> enter the death date exactly in the following format: <b>YYYYMMDD</b>.
Example: <b>19390209</b> is February 2, 1939.
Cookbook: You enter the year first (with 4 digits, like 1939), then without a comma or anything you enter the month (with 2 digits, like 02 for february) and then, again with no comma or anything you add the day of the month (with 2 digits, like 09).',
		'death_place.description' => 'Death Place of the person',
		'death_place.details' => 'Place (possibly including the country) where the person has died.',
		'gender.description' => 'Gender of the person',
		'gender.details' => 'Enter the gender (sex) of the partner. The list of values are defined by ISO 5218.',
		'marital_status.description' => 'Marital Status of the person',
		'marital_status.details' => 'Enter the marital status (e.g. \'married\' or \'single\') of the person. This list can be defined by your administrator.',
		'nationality.description' => 'Nationality of the partner',
		'nationality.details' => 'Enter the country of the partner\'s nationality.',
		'religion.description' => 'Religion of the partner',
		'religion.details' => 'Enter the religion of the person. This list can be defined by your administrator.',
		'mother_tongue.description' => 'Mother Tongue of the person',
		'mother_tongue.details' => 'This is the language that the person grew up with.',
		'preferred_language.description' => 'Preferred Language of the partner',
		'preferred_language.details' => 'Enter the language in which the partner should be contacted.',
		'join_date.description' => 'Join Date of the partner',
		'join_date.details' => 'If you manage the partner data of a club, then this might be the date at which the partner joined your club.',
		'leave_date.description' => 'Leave Date of the partner',
		'leave_date.details' => 'If you manage the partner data of a club, then this might be the date at which the partner left your club.',
		'occupations.description' => 'Occupations of the partner',
		'occupations.details' => 'Enter all the occupations of this partner. The list of possible occupations can be defined by your administrator.',
		'hobbies.description' => 'Hobbies of the partner',
		'hobbies.details' => 'Enter all the hobbies of this partner. The list of possible hobbies can be defined by your administrator.',
		'courses.description' => 'Courses of the partner',
		'courses.details' => 'Enter all the courses which this partner has visited. The list of possible courses can be defined by your administrator.',
		'meeting_period.description' => 'Meeting Period of the organisation',
		'meeting_period.details' => 'Enter the period in which the organisation will meet. If the organisation meets every 2 weeks, you would enter a \'2\' here and chose \'weeks\' as the meeting unit.',
		'meeting_period.seeAlso' => 'tx_partner_main:meeting_unit, tx_partner_main:meeting_start_date',
		'meeting_unit.description' => 'Meeting Unit of the organisation',
		'meeting_unit.details' => 'Enter the unit in which the organisation will meet. If the organisation meets every 2 weeks, you would chose \'weeks\' here and enter a \'2\' in the field Meeting Period. The list of possible meeting units is defined by the Partner-Extension.',
		'meeting_unit.seeAlso' => 'tx_partner_main:meeting_period, tx_partner_main:meeting_start_date',
		'meeting_start_date.description' => 'Meeting Start Date of the organisation',
		'meeting_start_date.details' => 'Enter the date at which the meeting series shall start or has started. The system will calculate the dates of further meetings based on this date.
<b>Example:</b>
If your organisation meets every 2 weeks, always on a monday, you would enter a \'2\' as meeting period and chose \'weeks\' as the meeting unit. Then you would enter a date here that was a monday, like 27-09-2004.',
		'meeting_start_date.seeAlso' => 'tx_partner_main:meeting_period, tx_partner_main:meeting_unit',
		'field_visibility.description' => 'Field Visibility Settings',
		'field_visibility.details' => 'In this table, you can see all the settings which concern the visibility of partner and contact-information fields in the frontend.',
		'field_visibility_no_values.description' => 'No Default Values found',
		'field_visibility_no_values.details' => 'The system could not find any default values for the field visibility settings. You can configure the default values in TSconfig in your sys-folder. For details, please refer to the manual (reference section).',
		'field_visibility_no_values.image' => 'EXT:partner/csh/img/field_visibility_default_settings.png',
		'error_invalid_keyword.description' => 'Invalid Keyword used',
		'error_invalid_keyword.details' => 'You have used a keyword other than PRIVATE, RESTRICTED or PUBLIC in your TSconfig setup. Therefore, the system cannot assign a default value for this field. Please note that the keywords are case-sensitive! You must use capital letters. Please refer to the following example for a proper setup.',
		'error_invalid_keyword.image' => 'EXT:partner/csh/img/field_visibility_default_settings.png',
	),
	'dk' => Array (
	),
	'de' => Array (
		'label.description' => 'Bezeichnung des Partners',
		'label.details' => 'Diese Bezeichnung wird an vielen Stellen im Backend verwendet, normalerweise falls der Partner durch nur ein Feld beschrieben werden muss. Die Bezeichnung wird aufgrund Ihrer Eingaben durch TYPO3 wie folgt vergeben:
<b>Für Personsn</b>
[Nachname], [Vorname] - [Ort]

<b>Für Organisationen</b>
[Name der Organisation] - [Ort]',
		'status.description' => 'Status des Partners',
		'status.details' => 'Beschreibt den aktuellen Status des Partners. Die Liste der möglichen Stati kann durch Ihren Administrator definiert werden. Mögliche Stati sind z.B. \'Aktiv\', \'Passiv\' oder \'Zu Archivieren\'.',
		'data_source.description' => 'Datenherkunft des Partners',
		'data_source.details' => 'Bezeichnet die ursprüngliche Herkunft der Partnerdaten. Kann z.B. der Name einer Drittanwendung sein. Kann zusammen mit dem Feld \'Externe ID\' verwendet werden, um einen Partner aus einer externen Anwendung vollständig zu identifizieren.',
		'data_source.seeAlso' => 'tx_partner_main:external_id',
		'external_id.description' => 'Externe Identifikation des Partners',
		'external_id.details' => 'Enthält die Identifikation (z.B. Nummer) des Partners in einer externen Anwendung. Der Name der Anwendung wird normalerweise im Feld \'Datenherkunft\' abgelegt.',
		'external_id.seeAlso' => 'tx_partner_main:data_source',
		'contact_permission.description' => 'Kontakterlaubnis für den Partner',
		'contact_permission.details' => 'Gibt an, ob und wie ein Partner kontaktiert werden darf. Diese Liste kann durch Ihren Administrator definiert werden. Mögliche Werte sind z.B. \'Keine Einschränkungen\', \'Kontakt verboten\' oder \'Vor Kontakt X anrufen\'.',
		'fe_user.description' => 'Frontend Benutzer ID des Partners',
		'fe_user.details' => 'Falls ein Partner auch einen Frontend Benutzer ist, kann die Frontend Benutzer-ID in diesem Feld eingegeben werden.',
		'image.description' => 'Bild des Partners',
		'image.details' => 'Sie können hier genau ein Bild des Partners hinterlegen. Dieses Bild kann z.B. bei der Anzeige des Partners auf Ihrer Website verwendet werden.',
		'relationships_overview.description' => 'Liste aller Beziehungen des Partners',
		'relationships_overview.details' => 'Diese Liste gibt Ihnen einen Überblick über alle Beziehungen, die zu diesem Partner erfasst wurden. Sie können auch Beziehungen und verbundene Partner ändern oder neue Beziehungen anlegen.

<strong>Hauptpartner oder verbundener Partner?</strong>
Eine Beziehung verbindet immer genau zwei Partner miteinander. Einer dieser Partner ist der Hauptpartner, der andere der verbundene Partner. Die Beziehungs-Art bestimmt, welche Beziehung die beiden Partner miteinander führen.

Nehmen beispielsweise an, dass ein Partner Mitglied eines anderen Partners ist, wie im Fall eines Vereinsmitgliedes. In diesem Fall wäre die Beziehungs-Art \'Hat Mitglied\'. Sie sehen bereits an der Art der Formulierung der Beziehungs-Art dass es eine bestimmte \'Richtung\' in der Beziehung gibt. Der Club Y \'Hat Mitglied\' Hr. Müller und nicht anders herum. Daraus folgt, dass der Club Y der Hauptpartner und Hr. Müller der verbundene Partner ist.

Ein weiteres Beispiel. Sie möchten erfassen, wer zu einer bestimmten Familie gehört. Ihr Administrator hat die Beziehungs-Arten \'Hat Kind\' und \'Hat Ehefrau\' zu diesem Zweck definiert. Sie sehen auch hier dass beide Formulierungen eine \'Richtung\' haben. Nun haben Herr und Frau Müller ein Kind, David. Um die Familie zu erfassen würden Sie also drei Partner erfassen: Herr Müller, Frau Müller und David Müller. Um die Familie abzubilden, öffnen Sie den Datensatz von Herr Müller und legen Sie eine Beziehung an mit Hr. Müller als \'Hauptpartner\'. Dort wählen Sie als Beziehungstyp \'Hat Kind\' aus und bestimmen David Müller als verbundenen Partner. Danach eröffnen Sie eine zweite Beziehung, erneut mit Hr. Müller als Hauptpartner. Dieses Mal verwenden Sie die Beziehungs-Art \'Hat Ehefrau\' und bestimmen Fr. Müller als verbundenen Partner. Das war\'s! Sie haben soeben die Familie im System erfasst.
		',
		'preceding_title.description' => 'Vorangestellter Titel eines Partners',
		'preceding_title.details' => 'Bezeichnet allfällige Titel, die der Anrede des Partners vorangestellt werden sollen. Beispiele: \'Ihre Majestät\', \'Honorable\', etc.',
		'title.description' => 'Anrede des Partners',
		'title.details' => 'Bezeichnet die Anrede des Partners, z.B. \'Herr\', \'Mister\', \'Professor\', \'Dr.\', etc. Diese Liste kann durch Ihren Administrator definiert werden.',
		'letter_title.description' => 'Briefanrede für diesen Partner',
		'letter_title.details' => 'Geben Sie hier eine Briefanrede ein, die nur für diesen Partner gültig ist. Falls Sie dieses Feld leer lassen, werden Briefe an diesen Partner mit der Standard-Briefanrede versehen, die durch die Anrede definiert wird (z.B. \'Sehr geehrter Herr xyz\')',
		'first_name.description' => 'Vorname des Partners',
		'first_name.details' => 'Geben Sie <b>nur</b> den <b>Vor-</b>namen in dieses Feld ein. Für den mittleren Namen und den Nachnamen stehen eigene Felder zur Verfügung.',
		'middle_name.description' => 'Mittlerer Name des Partners',
		'middle_name.details' => 'Geben Sie <b>nur</b> den <b>mittleren</b> Namen in dieses Feld ein. Für Vor- und Nachnamen stehen eigene Felder zur Verfügung.',
		'last_name_prefix.description' => 'Präfix zum Nachnamen des Partners',
		'last_name_prefix.details' => 'Geben Sie hier einen allfälligen Präfix ein, der dem Nachnamen des Partners vorangestellt werden soll. Dies könnte z.B. \'de la\' oder \'van den\', etc. sein.',
		'last_name.description' => 'Nachname des Partners',
		'last_name.details' => 'Geben Sie <b>nur</b> den <b>Nachnamen</b> (z.B. den Familiennamen) in dieses Feld ein. Für den Vornamen und den mittleren Namen stehen eigene Felder zur Verfügung.',
		'maiden_name.description' => 'Mädchenname es Partners',
		'maiden_name.details' => 'Dieses Feld kann den Mädchennamen (unverheirateten Namen) des Partners beinhalten. Bitte beachten Sie, dass dieser Name normalerweise nicht angezeigt oder angedruckt wird, sondern nur zu Informationszwecken dient.',
		'general_suffix.description' => 'Genereller Anhang zum Namen des Partners',
		'general_suffix.details' => 'Falls zum Namen des Partners einen generellen Anhang definieren möchten, können Sie dieses Feld benutzen. Dies könnte z.B. \'a.D.\', \'retired\' oder \'deceased\' sein.',
		'initials.description' => 'Initialen des Partners',
		'initials.details' => 'Wird normalerweise durch den Vor-, mittleren und Nachnamen des Partners bestimmt, aber Sie können Ihre eigene Logik verwenden. Beispiel: \'Paul J. Miller\' könnte die Initialen \'PJM\' haben.',
		'org_name.description' => 'Name der Organisation',
		'org_name.details' => 'Vollständiger Name der Organisation, z.B. den Namen der Firma oder die Bezeichnung einer Gruppe von Personen.',
		'org_type.description' => 'Typ der Organisation',
		'org_type.details' => 'Bestimmt den Typ der Organisation, z.B. eine Firma, einen Club, eine Gruppe oder irgendeine andere Institution, aber keine Person. Diese Liste kann durch Ihren Administrator definiert werden.',
		'org_legal_form.description' => 'Rechtsform der Organisation',
		'org_legal_form.details' => 'Bestimmt die Rechtsform der Organisation, falls vorhanden. Diese Liste kann durch Ihren Administrator definiert werden.',
		'department.description' => 'Abteilung (Adresse des Partners)',
		'department.details' => 'Wird normalerweise für Geschäftsadressen verwendet, sofern der Partner Mitarbeiter einer Abteilung ist oder die Adresse einer Abteilung besitzt.',
		'building.description' => 'Gebäude (Adresse des Partners)',
		'building.details' => 'Kann verwendet werden um die Adresse durch die Angabe des Gebäudenamens genauer zu beschreiben (z.B. \'Egis\' or \'Beauty Appartments\').',
		'floor.description' => 'Stockwerk (Adresse des Partners)',
		'floor.details' => 'Kann verwendet werden um die Adresse durch die Angabe des Stockwerkes genauer zu beschreiben.',
		'room.description' => 'Raum (Adresse des Partners)',
		'room.details' => 'Kann verwendet werden um die Adresse durch die Angabe des Raumes (normalerweise eine Nummer) genauer zu beschreiben',
		'street.description' => 'Strasse (Adresse des Partners)',
		'street.details' => 'Name der Strasse (als Teil der Adresse).',
		'street_number.description' => 'Hausnummer (Adresse des Partners)',
		'street_number.details' => 'Hausnummer des Gebäudes (als Teil der Adresse).',
		'postal_code.description' => 'Postleitzahl (Adresse des Partners)',
		'postal_code.details' => 'Die Postleitzahl (z.B. ZIP code) des Ortes.',
		'locality.description' => 'Ort (Adresse des Partners)',
		'locality.details' => 'Angabe der Ortschaft. Geben Sie hier <b>nicht</b> die Postleitzahl ein. Dafür steht ein gesondertes Feld zur Verfügung.',
		'admin_area.description' => 'Region (Adresse des Partners)',
		'admin_area.details' => 'Die Region, in welcher sich die Ortschaft befindet. In der Schweiz ist dies der Kanton (z.B. \'FR\' für Fribourg), in den USA der Bundesstaat (z.B. \'NY\' für New York).',
		'country.description' => 'Land (Adresse des Partners)',
		'country.details' => 'Angabe des Landes.',
		'po_number.description' => 'Postfach Nummer (Adresse des Partners)',
		'po_number.details' => 'Falls es sich um eine Postfachadresse handelt, geben Sie hier die Nummer des Postfaches ein.',
		'po_no_number.description' => 'Postfach ohne Nummer (Adresse des Partners)',
		'po_no_number.details' => 'Falls der Partner ein Postfach hat, dieses aber keine Nummer aufweist, dann setzen Sie dieses Kennzeichen.',
		'po_postal_code.description' => 'Postleitzahl des Postfaches (Adresse des Partners)',
		'po_postal_code.details' => 'Die Postleitzahl (z.B. ZIP code) des Postfaches.',
		'po_locality.description' => 'Ort des Postfaches (Adresse des Partners)',
		'po_locality.details' => 'Die Ortschaft des Postfaches. Geben Sie hier <b>nicht</b> die Postleitzahl des Postfaches ein. Dafür steht ein gesondertes Feld zur Verfügung.',
		'po_admin_area.description' => 'Region des Postfaches (Adresse des Partners)',
		'po_admin_area.details' => 'Die Region des Postfachesto. In der Schweiz ist dies der Kanton (z.B. \'FR\' für Fribourg), in den USA der Bundesstaat (z.B. \'NY\' für New York).',
		'po_country.description' => 'Land des Postfaches (Adresse des Partners)',
		'po_country.details' => 'Land des Postfaches.',
		'contact_info.description' => 'Kontaktinformationen des Partners',
		'contact_info.details' => 'Sie können eine unbeschränkte Anzahl von Kontaktinformationen (z.B. Telefonnummern oder E-Mail Adressen) zu diesem Partner hier definieren.',
		'formation_date.description' => 'Gründungsdatum der Organisation',
		'formation_date.details' => 'Falls bekannt, geben Sie hier das Gründungsdatum der Organisation ein.',
		'closure_date.description' => 'Auflösungsdatum der Organisation',
		'closure_date.details' => 'Dieses Datum entspricht dem Datum, an welchem die Organisation formell aufgelöst wurde (z.B. durch Konkurs einer Firma).',
		'birth_date.description' => 'Geburtsdatum einer Person',
		'birth_date.details' => '<b>Wichtiger Hinweis</b>: Sie <b>müssen</b> das Geburtsdatum genau in folgendem Format eingeben: <b>JJJJMMTT</b>.
Beispiel: <b>19390209</b> steht für den 2. Februar 1939.
Anleitung: Zuerst geben Sie das Jahr vierstellig ein (z.B. 1939), danach (ohne ein Komma oder ein sonstiges Trennzeichen) den Monat (evenfalls zweistellig, z.B. 02 für Februar) und schliesslich, ebenfalls wieder ohne Trennzeichen, den Tag (wiederum zweistellig, z.B. 09).',
		'birth_place.description' => 'Geburtsort der Person',
		'birth_place.details' => 'Geburtsort (allenfalls inkl. Angabe des Landes) der Person.',
		'death_date.description' => 'Todesdatum der Person',
		'death_date.details' => '<b>Wichtiger Hinweis</b>: Sie <b>müssen</b> das Todesdatum genau in folgendem Format eingeben: <b>JJJJMMTT</b>.
Beispiel: <b>19390209</b> steht für den 2. Februar 1939.
Anleitung: Zuerst geben Sie das Jahr vierstellig ein (z.B. 1939), danach (ohne ein Komma oder ein sonstiges Trennzeichen) den Monat (evenfalls zweistellig, z.B. 02 für Februar) und schliesslich, ebenfalls wieder ohne Trennzeichen, den Tag (wiederum zweistellig, z.B. 09).',
		'death_place.description' => 'Todesort der Person',
		'death_place.details' => 'Ort (allenfalls inkl. Angabe des Landes), an welchem die Person verstorben ist.',
		'gender.description' => 'Geschlecht der Person',
		'gender.details' => 'Geben Sie das Geschlecht der Person an. Die Auswahlliste entspricht der ISO 5218 Norm.',
		'marital_status.description' => 'Zivilstand der Person',
		'marital_status.details' => 'Geben Sie den Zivilstand der Person ein (z.B. \'verheiratet\' oder \'Alleinstehend\'). Diese Liste kann durch Ihren Administrator definiert werden.',
		'nationality.description' => 'Nationalität des Partners',
		'nationality.details' => 'Wählen Sie das Land aus, für welchen der Partner das Bürgerrecht besitzt.',
		'religion.description' => 'Religion des Partners',
		'religion.details' => 'Wählen Sie die Religion des Partners aus. Diese Liste kann durch Ihren Administrator definiert werden.',
		'mother_tongue.description' => 'Muttersprache des Partners',
		'mother_tongue.details' => 'Dies entspricht der Sprache, mit welcher der Partner aufgewachsen ist.',
		'preferred_language.description' => 'Bevorzugte Sprache des Partners',
		'preferred_language.details' => 'Wählen Sie die Sprache, in welcher der Partner kontaktiert werden soll.',
		'join_date.description' => 'Eintrittsdatum des Partners',
		'join_date.details' => 'Falls Sie die Partnerdaten eines Vereins verwalten, dann können Sie dieses Datum z.B. dafür nutzen um anzugeben, wann der Partner Ihrem Verein beigetreten ist.',
		'leave_date.description' => 'Austrittsdatum des Partners',
		'leave_date.details' => 'Falls Sie die Partnerdaten eines Vereins verwalten, dann können Sie dieses Datum z.B. dafür nutzen um anzugeben, wann der Partner aus Ihrem Verein ausgetreten ist.',
		'occupations.description' => 'Tätigkeiten des Partners',
		'occupations.details' => 'Markieren Sie alle Tätigkeiten, die von diesem Partner wahrgenommen werden. Die Liste der möglichen Tätigkeiten kann durch Ihren Administrator definiert werden.',
		'hobbies.description' => 'Hobbys des Partners',
		'hobbies.details' => 'Markieren Sie alle Hobbys, die von diesem Partner wahrgenommen werden. Die Liste der möglichen Hobbys kann durch Ihren Administrator definiert werden.',
		'courses.description' => 'Kurse, die der Partner besucht hat',
		'courses.details' => 'Markieren Sie alle Kurse, die von diesem Partner besucht wurden. Die Liste der möglichen Kurse kann durch Ihren Administrator definiert werden.',
		'meeting_period.description' => 'Meetings-Periodenmasszahl der Organisation',
		'meeting_period.details' => 'Geben Sie die Periodenmasszahl ein, in welcher die Organisation Meetings veranstaltet. Falls sich die Organisation alle 2 Wochen trifft, geben Sie hier \'2\' ein und wählen Sie \'Wochen\' als Periodeneinheit.',
		'meeting_period.seeAlso' => 'tx_partner_main:meeting_unit, tx_partner_main:meeting_start_date',
		'meeting_unit.description' => 'Meetings-Periodeneinheit der Organisation',
		'meeting_unit.details' => 'Geben Sie die Periodeneinheit ein, in welcher die Organisation Meetings veranstaltet. Falls sich die Organisation alle 2 Wochen trifft, wählen Sie hier \'Wochen\' und geben Sie \'2\' als Periodenmasszahl ein.',
		'meeting_unit.seeAlso' => 'tx_partner_main:meeting_period, tx_partner_main:meeting_start_date',
		'meeting_start_date.description' => 'Meetings-Ausgangsdatum der Organisation',
		'meeting_start_date.details' => 'Geben Sie hier das Datum ein, an welcher die Meeting-Serie beginnen soll oder begonnen hat. Das System wird die Daten aller weiterer Meetings aufgrund dieses Datums errechnen.
<b>Beispiel:</b>
Falls sich Ihre Organisation alle 2 Wochen trifft, immer montags, geben Sie \'2\' als Periodenmasszahl ein und wählen Sie \'Wochen\' als Periodeneinheit. Danach geben Sie hier ein Datum ein, das auf einen Montag fällt, z.B. 27-09-2004.',
		'meeting_start_date.seeAlso' => 'tx_partner_main:meeting_period, tx_partner_main:meeting_unit',
		'field_visibility.description' => 'Einstellungen der Feld-Sichtbarkeit',
		'field_visibility.details' => 'In dieser Tabelle können Sie alle Einstellungen sehen, die die Sichtbarkeit von Partner- oder Kontaktinformations-Feldern im Frontend betreffen.',
		'field_visibility_no_values.description' => 'Keine Standard-Werte gefunden',
		'field_visibility_no_values.details' => 'Das System konnte keine Standard-Werte für die Sichtbarkeit der Felder ermitteln. Sie können diese Standard-Werte im TSconfig-Feld Ihres sys-Ordners einstellen. Für weitere Informationen konsultieren Sie bitte das Manual (Kapitel \'Reference\').',
		'error_invalid_keyword.description' => 'Ungültiges Schlüsselwort',
		'error_invalid_keyword.details' => 'Sie haben ein anderes Schlüsselwort als die gültigen Ausdrücke PRIVATE, RESTRICTED oder PUBLIC in Ihrem TSconfig Setup verwendet. Daher kann das System keinen Default-Wert für dieses Feld zuweisen. Bitte beachten Sie, dass die Schlüsselwörter auf Gross- / Kleinschreibung hin unterschieden werden. Sie müssen Grossbuchstaben verwenden. Das untenstehende Bild zeigt ein korrektes Setup.',
		'error_invalid_keyword.image' => 'EXT:partner/csh/img/field_visibility_default_settings.png',
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