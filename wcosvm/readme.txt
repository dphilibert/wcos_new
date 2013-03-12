	php index.php -i anbieter 
Aktualisiert die Tabelle anbieter mit den Daten aus der Tabelle vm_import_kunden. In anbieter nicht vorhandenen Firmen werden neu angelegt.

	php index.php -i stammdaten
Aktualisiert die Tabelle stammdaten  mit den Daten aus der Tabelle vm_import_kunden.

	php index.php -i delete
Löscht Anbieter und Stammdaten, die nicht in der Tabelle vm_import_kunden vorhanden sind.
