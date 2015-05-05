<pre>
<?
// Write a small application to print "Good Morning!" in a variety of languages
// Rather than hard-code the greetings, use gettext to manage the translations

bindtextdomain ('messages', './lang');

// Set the current domain that gettext will use
textdomain ('messages');

// Make an array
// Use the ISO two-letter codes as keys
// Use the language names as values
$iso_codes = array (
    'en'=>'English',
    'pt'=>'Portuguese',
);

foreach ($iso_codes as $iso_code => $language) {
    // Set the LANGUAGE enviroment variable to the desired language
    putenv ('LANGUAGE='.$iso_code);
    setlocale(LC_ALL, $iso_code); 
        // Print out the language name and greeting
        // Note that the greeting is wrapped in a call to gettext
        printf ("<b>%12s:</b> %s\n", $language, _("Good morning!"));
}
?>
</pre>
