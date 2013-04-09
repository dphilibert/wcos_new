<?php
return array (
  //Alnum
  'notAlnum'                      => "'%value%' darf nur Buchstaben und Zahlen enthalten",
  'stringEmpty'                   => "'%value%' Dieser Wert darf nicht leer sein",
  //Alpha
  'notAlpha'                      => "'%value%' Darf nur Buchstaben enthalten",
  //Barcode
  //Between
  'notBetween'                    => "'%value%' muss zwischen '%min%' und '%max%' sein",
  'notBetweenStrict'              => "'%value%' muss genau zwischen '%min%' und '%max%' sein",
  //Ccnum
  'ccnumLength'                   => "'%value%' muss 13 bis 19 Ziffern enthalten",
  'ccnumChecksum'                 => "Die Anwendung des Luhn Algorithmus (mod-10 Prüfsumme) auf '%value%' schlug fehl",
  //Date
  'dateNotYYYY-MM-DD'             => "'%value%' muss das Format JJJJ-MM-TT habe",
  'dateInvalid'                   => "'%value%' scheint kein gültiges Datum zu sein",
  'dateFalseFormat'               => "'%value%' hat ein falsches Datumsformat",
  //Digits
  'notDigits'                     => "'%value%' darf nur aus nummerischen Zeichen bestehen",
  //EmailAddress
  'emailAddressInvalid'           => "'%value%' ist keine valide E-Mail Adresse im Format name@domain",  
  'emailAddressInvalidFormat'     => "'%value%' ist keine valide E-Mail Adresse im Format name@domain",
  'emailAddressInvalidHostname'   => "'%hostname%' ist kein güliger Servername bei der E-Mail Adresse '%value%'",
  'emailAddressInvalidMxRecord'   => "'%hostname%' scheint keinen gültigen MX Eintrag zu haben für die E-Mail Adresse '%value%'",
  'emailAddressDotAtom'           => "'%localPart%' dieser Empfängername stimmt nicht mit dem dot-atom Format überein",
  'emailAddressQuotedString'      => "'%localPart%' dieser Empfängername stimmt nicht mit dem quoted-string Format überein",
  'emailAddressInvalidLocalPart'  => "'%localPart%' diser Empfängername ist kein gültiger Empfänger bei der E-Mail Adresse '%value%'",
  //Float
  'notFloat'                      => "'%value%' scheint keine Gleitkommazahl zu sein",
  //GreaterThan
  'notGreaterThan'                => "'%value%' muss größer sein als '%min%'",
  //Hex
  'notHex'                        => "'%value%' muss ein Hexadezimalwert sein",
  //Hostname
  'hostnameIpAddressNotAllowed'   => "'%value%' scheint eine IP Adresse zu sein, diese sind aber nicht erlaubt",
  'hostnameUnknownTld'            => "'%value%' scheint ein DNS Servername zu sein, die Länderkennung kann aber nicht bestätigt werden",
  'hostnameDashCharacter'         => "'%value%' scheint ein DNS Servername zu sein, enthält aber einen Bindestrich (-) an einer unzulässigen Position",
  'hostnameInvalidHostnameSchema' => "'%value%' scheint ein DNS Servername zu sein, entspricht aber nicht dem Servernamensschema für Länderkennungen '%tld%'",
  'hostnameUndecipherableTld'     => "'%value%' scheint ein DNS Servername zu sein, die Länderkennung kann aber nicht ermittelt werden.",
  'hostnameInvalidHostname'       => "'%value%' entspricht nicht dem erforderlichen Muster für Servernamen",
  'hostnameInvalidLocalName'      => "'%value%' scheint kein gültiger Name in einem lokalen Netzwerk zu sein",
  'hostnameLocalNameNotAllowed'   => "'%value%' scheint ein gültiger Name in einem lokalen Netzwerk zu sein, diese sind aber nicht erlaubt",
  //Identical
  'notSame'                       => "Die Werte müssen übereinstimmen",
  'missingToken'                  => "Es wurde kein Vergleichswert übermittelt",
  //InArray
  'notInArray'                    => "'%value%' ist kein zulässiger Wert",
  //Int
  'notInt'                        => "'%value%' muss eine Ganzzahl sein",
  //Ip
  'notIpAddress'                  => "'%value%' scheint keine gültige IP Adresse zu sein",
  //LessThan
  'notLessThan'                   => "'%value%' muss kleiner sein als '%max%'",
  //NotEmpty
  'isEmpty'                       => "Dieses Feld darf nicht leer sein",
  //Regex
  'regexNotMatch'                 => "'%value%' muss dem Suchmuster des regulären Ausdrucks '%pattern%' entsprechen",
  //StringLength
  'stringLengthTooShort'          => "'%value%' muss mindestens %min% Zeichen lang sein",
  'stringLengthTooLong'           => "'%value%' darf höchstens %max% Zeichen lang sein"
);
?>