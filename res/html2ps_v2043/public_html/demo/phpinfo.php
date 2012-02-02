<?php

  // 111216, security, dwildt+
if( ! isset ( $_COOKIE["be_typo_user"] ) )
{
  die( '
     <h1>TYPO3 PDF Controller: no access!</h1>
     <p>Sorry, the content of the current page is accessible only, if you are logged on the backend of TYPO3.</p>
     <h1>TYPO3 PDF Controller: kein Zugangsrecht!</h1>
     <p>Sorry, der Inhalt dieser Seite wird nur ausgeliefert, wenn Du im Backend von TYPO3 angemeldet bist.</p>
    ' );
}
  // 111216, security, dwildt+

phpinfo();

?>