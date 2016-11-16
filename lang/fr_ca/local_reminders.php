<?php

$string['local_reminders']          = 'Rappels';
$string['pluginname']               = 'Rappels';
$string['pluginadministration']     = 'Rappels';

$string['num']                      = 'Nombre de Rappels';
$string['confignum']                = 'Champs de rappel à afficher sur les formulaires du système.';

$string['remhdr']                   = 'Rappels';
$string['link']                     = 'Lien au calendrier des événements';
$string['reminder']                 = 'Rappel';
$string['weeks']                    = 'Semaines';
$string['week']                     = 'Semaine';
$string['days']                     = 'Jours';
$string['day']                      = 'Jour';
$string['hours']                    = 'Heures';
$string['hour']                     = 'Heure';
$string['mins']                     = 'Minutes';
$string['before']                   = 'Avant';
$string['after']                    = 'Après';
$string['afteratt']                 = 'Après la participation';
$string['afternoshow']              = 'Après le défaut de participer';

$string['welcomemessage']           = 'Message de bienvenue';
$string['bulk_reminders']           = 'Message de rappel';
$string['invitemessage']            = 'message d\'invitation ';
$string['messagereminderhdr']       = 'Rappels en vrac';
$string['code']                     = 'Identification du message';
$string['event']                    = 'Événement';

$string['noremindersfound']         = 'Il n\'y a actuellement aucun rappel défini. Prière d\'ajouter un rappel pour continuer.';
$string['msg_id']                   = 'Identification du message';
$string['language']                 = 'Langue';
$string['body']                     = 'Corps du message';
$string['edit']                     = 'Réviser';
$string['tools']                    = 'Outils';
$string['reminders']                = 'Rappels';
$string['browse']                   = 'Parcourir les Rappels';
$string['preview']                  = 'Aperçu du rappel';
$string['saveas']                   = 'Ajouter nouveau';
$string['subject']                  = 'Objet';
$string['userfrom']                 = 'Nom de l\'expéditeur';
$string['body']                     = 'Corps du message';
$string['test']                     = 'Envoyer test par courriel à soi-même ';
$string['vevent']                   = 'Pièce jointe iCalendar';
$string['vevent_description']       = 'Description pour iCalendar';
$string['duplicate']                = 'Impossible d\'utiliser un code qui existe déjà';
$string['fieldhelp']                = 'Champs disponibles :';
$string['remindereventdescription'] = 'Détails de l\'événement :<br />
$a';
if( file_exists( "$CFG->dirroot/local/core/lib.php" ) ){
	$string['fields']               = '[[name]] = Nom de l\'événement<br/>
                                       [[description]] = Description de l\'événement<br/>
                                       [[event_description]] = Description de l\'événement (avec titre)<br/>
                                       [[date]] = Date et heure de l\'événement réglées en fonction du fuseau horaire de l\'utilisateur<br/>
                                       [[timezone]] = Fuseau horaire de l\'utilisateur<br/>
                                       [[mins]] = Durée de l\'événement (en minutes)<br/>
                                       [[duration]] = Durée de l\'événement (en heures et minutes)<br/><br/>
                                       [[firstname]] = Prénom de l\'utilisateur<br/>
                                       [[lastname]]  = Nom de famille de l\'utilisateur    <br><br/>
                                       [[url]] = URL à la page du cours dans Moodle <br/>
                                       [[urlx]] = URL à la page du cours dans Moodle (connexion automatique)<br/>
                                       [[meeting]] = URL à la page de connexion à Moodle pour la réunion sur Adobe Connect (connexion automatique)<br/>
                                       [[ievent]] = URL au rappel de téléchargement d\'Outlook  <br/>
                                       [[cpurl]] = Lien d\'adresse URL à la page de connexion à la réunion sur Connect Pro <br/>
                                       [[course]] = Nom au complet du cours (pour les rappels de cours)<br/>
                                       [[shortname]] = Nom abrégé du cours. <br/>
                                       [[user#field]] = Champ provenant de l\'utilisateur. <br/>
                                       [[course#field]] = Champ provenant du cours.<br/><br/>';
}else{
	$string['fields']                = '[[name]] = Nom de l\'événement<br/>
                                       [[description]] = Description de l\'événement<br/>
                                       [[event_description]] = Description de l\'événement (avec titre)<br/>
                                       [[date]] = Date et heure de l\'événement réglées en fonction du fuseau horaire de l\'utilisateur<br/>
                                       [[timezone]] = Fuseau horaire de l\'utilisateur<br/>
                                       [[mins]] = Durée de l\'événement (en minutes)<br/>
                                       [[duration]] = Durée de l\'événement (en heures et minutes)<br/><br/>
                                       [[firstname]] = Prénom de l\'utilisateur<br/>
                                       [[lastname]]  = Nom de famille de l\'utilisateur    <br><br/>
                                       [[url]] = URL à la page du cours dans Moodle <br/>
									   [[ievent]] = URL au rappel de téléchargement d\'Outlook  <br/>
                                       [[cpurl]] = Lien d\'adresse URL à la page de connexion à la réunion sur Connect Pro <br/>
                                       [[course]] = Nom au complet du cours (pour les rappels de cours)<br/>
                                       [[shortname]] = Nom abrégé du cours. <br/>
                                       [[user#field]] = Champ provenant de l\'utilisateur. <br/>';
}