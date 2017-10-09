<?php
        class Config
        {
                public $databaseHost = "dbhost";
                public $databaseUser = "user";
                public $databasePass = "pass";
                public $databaseName = "dbname";

                public $cookieName = "cookiename";
                public $logQueryErrors = true;
                public $reportQueryErrors = true;
                public $showQueryErrors = true;
                public $banPossibleAttacks = false;
                public $queryErrorLogPath = "dberror.log";

                public static $errorReporting = E_ALL; //
                public static $displayErrors = true; //turn on-off to writing erros to screen.


                public $banTimeout = 3600;      // 1 hour


                function __construct()
                {

                }

        }

?>
